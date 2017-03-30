<?php
/**
 * Description of SchedulingExecutor
 *
 * @author manueldelapenna
 */
class SchedulingExecutor {
	
	/**
	 * Process ID.
	 */
	const PROCESS_ID = 'block_AFIP_authorization';
	
	/**
	 * Performs the Invoices authorization with AFIP for all billing types
	 *
	 * @return void
	 */
	public static function execute() {
		
		if(Mage::getStoreConfig('afip/config/enable')) {
			
			$sem = new FileSemaphore(self::PROCESS_ID, NULL, 0, 1800);
						
			if ($sem->isLock()){
				$sem->executeWarningTimeAction();
			}else{
				$sem->lock();
				try{
					SchedulingExecutor::executeFor(TypeEnum::A);
					SchedulingExecutor::executeFor(TypeEnum::B);
				}catch(Exception $e){
					Mage::logException($e);
					echo $e->getMessage();
				}
				$sem->unlock();
			}
		}
	}
	
	
	
	/**
	 * Performs the Invoices authorization with AFIP for a specific billing type
	 *
	 * @param TypeEnum $billingType        	
	 * @return void
	 */
	public static function executeFor($billingType) {
		
		$invoiceManager = SchedulingExecutor::createInvoiceManager();
		$lastAfipInvoiceNumber = $invoiceManager->getLastAcceptedNumberFor($billingType);
		$lastRUInvoiceNumber = AfipInvoice::getLastNumber($billingType);
		
		if($lastRUInvoiceNumber == $lastAfipInvoiceNumber) {
			$pendingAfipInvoices = AfipInvoice::getPendingForAuthorize($billingType);
			AfipInvoice::setNullNumberToPedingInvoices($billingType);
			$collector = SchedulingExecutor::generateAfipInvoiceDataCollectorFromOrderInvoice($pendingAfipInvoices, $billingType);
			$collector->assignInvoiceNumbersFrom($lastAfipInvoiceNumber);
			$collector = SchedulingExecutor::iterateCollectorAndAssignInvoiceNumbers($collector);
			$invoiceManager->authorize($collector);
							
			// collector finish without errors
			if($collector->hasNormalEndingStatus()) {
				$collector = SchedulingExecutor::iterateCollectorAndUpdateInvoiceWithAfipResponse($collector);
			}
			
			// exists previous lost responses
		} else {
			SchedulingExecutor::loadAndResendLostResponseInvoicesToAfip($invoiceManager, $lastRUInvoiceNumber, $lastAfipInvoiceNumber, $billingType);
		}
		
	}
	
	/**
	 * Creates a new InvoiceManager for configurated environment
	 *
	 * @return InvoiceManager
	 */
	public static function createInvoiceManager() {
		if(Mage::getStoreConfig('afip/config/enable_prod')) {
			$environment = ProductionEnvironment::getInstance();
		} else {
			$environment = StagingEnvironment::getInstance();
		}
		
		$logger = FileLoggerHelper::getInstance(NULL, Mage::getBaseDir('var') . '/log/afip');
		$invoiceManager = InvoiceManager::getInstance($environment, $logger);
		return $invoiceManager;
	}
	
	/**
	 * Creates a AfipInvoiceDataCollector instance for a Afip_Model_Mysql4_Invoice_Collection with a specific TypeEnum
	 *
	 * @param Afip_Model_Mysql4_Invoice_Collection $pendingAfipInvoices        	
	 * @param TypeEnum $billingType        	
	 * @return AfipInvoiceDataCollector
	 */
	public static function generateAfipInvoiceDataCollectorFromOrderInvoice($pendingAfipInvoices, $billingType) {
		$collector = AfipInvoiceDataCollector::getInstance($billingType);
		foreach($pendingAfipInvoices as $afipInvoice) {
			$invoice = Mage::getModel('sales/order_invoice')->load($afipInvoice->getOrderInvoiceId());
			$data = SchedulingExecutor::generateAfipInvoiceDataFromOrderInvoice($invoice, $billingType, $afipInvoice);
			$collector->add($data);
		}
		return $collector;
	}
	
	/**
	 * Creates a AfipInvoiceData from a Mage_Sales_Order_Invoice
	 *
	 * @param Mage_Sales_Model_Order_Invoice $invoice        	
	 * @param TypeEnum $billingType        	
	 * @param AfipInvoice $afipInvoice
	 * @return AfipInvoiceData
	 */
	public static function generateAfipInvoiceDataFromOrderInvoice($invoice, $billingType, $afipInvoice) {
		$invoiceData = new AfipInvoiceData();
		$invoiceData->setConcept(ConceptEnum::PRODUCT);
		
		$customer = Mage::getModel('customer/customer')->load($invoice->getOrder()->getCustomerId());
		// taxvat = DNI/CUIT
		$invoiceData->setDocumentNumber($customer->getTaxvat());
		
		if ($billingType == TypeEnum::A){
			$invoiceData->setDocumentType(DocumentTypeEnum::CUIT);
		}else{
			$invoiceData->setDocumentType(DocumentTypeEnum::DNI);
		}
		
		$invoiceData->setId($invoice->getId());
		$invoiceData->setInvoiceDate(date('Y-m-d'));
		$invoiceData->setInvoiceType($billingType);
		$invoiceData->setStoreId($invoice->getStoreId());
		
		$itemsAlicuotaAmounts = AlicuotaProduct::getAlicuotaAmountsFromInvoice($invoice, $afipInvoice);
		$shippingAlicuotaAmounts = AlicuotaShipping::getAlicuotaAmountsFromInvoice($invoice, $afipInvoice);

		$invoiceData = self::completeTaxesAmounts($invoiceData, $invoice, $itemsAlicuotaAmounts, $shippingAlicuotaAmounts, $afipInvoice);		
					
		return $invoiceData;
	}
	
	
	public static function completeTaxesAmounts($invoiceData, $invoice, $itemsAlicuotaAmounts, $shippingAlicuotaAmounts, $afipInvoice){
		
		/* Calcula totales (bruto) */
		$totalAmount0250 = $itemsAlicuotaAmounts[AlicuotaProduct::IVA_0250] + $shippingAlicuotaAmounts[AlicuotaShipping::IVA_0250];
		$totalAmount0500 = $itemsAlicuotaAmounts[AlicuotaProduct::IVA_0500] + $shippingAlicuotaAmounts[AlicuotaShipping::IVA_0500];
		$totalAmount1050 = $itemsAlicuotaAmounts[AlicuotaProduct::IVA_1050] + $shippingAlicuotaAmounts[AlicuotaShipping::IVA_1050];
		$totalAmount2100 = $itemsAlicuotaAmounts[AlicuotaProduct::IVA_2100] + $shippingAlicuotaAmounts[AlicuotaShipping::IVA_2100];
		$totalAmount2700 = $itemsAlicuotaAmounts[AlicuotaProduct::IVA_2700] + $shippingAlicuotaAmounts[AlicuotaShipping::IVA_2700];
		
		/* Calcula netos */
		$totalNeto0250 = NumberDataTypeHelper::truncate($totalAmount0250 / 1.025 ,2);
		$totalNeto0500 = NumberDataTypeHelper::truncate($totalAmount0500 / 1.05 ,2);
		$totalNeto1050 = NumberDataTypeHelper::truncate($totalAmount1050 / 1.105 ,2);
		$totalNeto2100 = NumberDataTypeHelper::truncate($totalAmount2100 / 1.21 ,2);
		$totalNeto2700 = NumberDataTypeHelper::truncate($totalAmount2700 / 1.27 ,2);
		
		/*total bruto*/
		$totalAmount = NumberDataTypeHelper::truncate($totalAmount0250,2) +
					   NumberDataTypeHelper::truncate($totalAmount0500,2) + 
					   NumberDataTypeHelper::truncate($totalAmount1050,2) + 
					   NumberDataTypeHelper::truncate($totalAmount2100,2) + 
					   NumberDataTypeHelper::truncate($totalAmount2700,2);
		
		//monto gravado
		$taxableNetAmount = $totalNeto0250 + $totalNeto0500 + $totalNeto1050 + $totalNeto2100 + $totalNeto2700;
		$invoiceData->setTaxableNetAmount($taxableNetAmount);
		
		//monto del impuesto del neto gravado
		$taxAmount = $totalAmount - $taxableNetAmount;
		$invoiceData->setTaxAmount($taxAmount);
		
		//generar alicuota para cada IVA
		if ($totalAmount0250 > 0){
			$alicuota = Alicuota0250::getInstance();
			$alicuota->setBaseAmount($totalNeto0250);
			$alicuota->setTaxAmount($totalAmount0250 - $totalNeto0250);
			$invoiceData->addAlicuota($alicuota);
			
			$afipInvoice->setNeto_0250($alicuota->getBaseAmount());
			$afipInvoice->setIva_0250($alicuota->getTaxAmount());
		}
		
		if ($totalAmount0500 > 0){
			$alicuota = Alicuota0500::getInstance();
			$alicuota->setBaseAmount($totalNeto0500);
			$alicuota->setTaxAmount($totalAmount0500 - $totalNeto0500);
			$invoiceData->addAlicuota($alicuota);
			
			$afipInvoice->setNeto_0500($alicuota->getBaseAmount());
			$afipInvoice->setIva_0500($alicuota->getTaxAmount());
		}
		
		if ($totalAmount1050 > 0){
			$alicuota = Alicuota1050::getInstance();
			$alicuota->setBaseAmount($totalNeto1050);
			$alicuota->setTaxAmount($totalAmount1050 - $totalNeto1050);
			$invoiceData->addAlicuota($alicuota);
			
			$afipInvoice->setNeto_1050($alicuota->getBaseAmount());
			$afipInvoice->setIva_1050($alicuota->getTaxAmount());
		}
		
		if ($totalAmount2100 > 0){
			$alicuota = Alicuota2100::getInstance();
			$alicuota->setBaseAmount($totalNeto2100);
			$alicuota->setTaxAmount($totalAmount2100 - $totalNeto2100);
			$invoiceData->addAlicuota($alicuota);
			
			$afipInvoice->setNeto_2100($alicuota->getBaseAmount());
			$afipInvoice->setIva_2100($alicuota->getTaxAmount());
		}
		
		if ($totalAmount2700 > 0){
			$alicuota = Alicuota2700::getInstance();
			$alicuota->setBaseAmount($totalNeto2700);
			$alicuota->setTaxAmount($totalAmount2700 - $totalNeto2700);
			$invoiceData->addAlicuota($alicuota);
			
			$afipInvoice->setNeto_2700($alicuota->getBaseAmount());
			$afipInvoice->setIva_2700($alicuota->getTaxAmount());
		}
		
		// monto exento
		$totalExento = $itemsAlicuotaAmounts[AlicuotaProduct::EXENTO] + $shippingAlicuotaAmounts[AlicuotaShipping::EXENTO];
		$invoiceData->setTaxExemptAmount($totalExento);
		
		$afipInvoice->setNetoExento($invoiceData->getTaxExemptAmount());
		
		// monto no gravado
		$totalNoGravado = $itemsAlicuotaAmounts[AlicuotaProduct::NO_GRAVADO] + $shippingAlicuotaAmounts[AlicuotaShipping::NO_GRAVADO];
		$invoiceData->setUntaxedNetAmount($totalNoGravado);
		
		$afipInvoice->setNetoNoGravado($invoiceData->getUntaxedNetAmount());

		//AFIP Invoice
		$afipInvoice->setSubtotal($invoiceData->getTaxableNetAmount() + $invoiceData->getTaxExemptAmount() + $invoiceData->getUntaxedNetAmount());
		$afipInvoice->setTotalIva($invoiceData->getTaxAmount());
		$afipInvoice->setTotal($afipInvoice->getSubtotal() + $afipInvoice->getTotalIva());
		$afipInvoice->save();
				
		return $invoiceData;
	}
	
	/**
	 * Iterates the Invoice Data Collector and retrieve Invoices for assign number
	 *
	 * @param AfipInvoiceDataCollector $collector        	
	 * @return AfipInvoiceDataCollector $collector
	 */
	public static function iterateCollectorAndAssignInvoiceNumbers($collector) {
		$collector->rewind();
		
		$transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
		try {
			$transaction->beginTransaction();
			
			while($collector->valid()) {
				$billingData = $collector->current();
				$afipInvoice = Mage::getModel('afip/invoice')->loadInvoiceByOrderInvoiceId($billingData->getId());
				$afipInvoice->updateAndSave($billingData->getInvoiceNumber(), $afipInvoice->getType(), NULL, NULL, NULL, NULL, AfipInvoice::PENDING);
				$collector->next();
			}
			
			$transaction->commit();
		} catch(Exception $e) {
			$transaction->rollback();
			throw $e;
		}
		return $collector;
	}
	
	/**
	 * Iterates the Invoice Data Collector and retrieve Invoices for update with AFIP respones information
	 *
	 * @param AfipInvoiceDataCollector $collector        	
	 * @return AfipInvoiceDataCollector $collector
	 */
	public static function iterateCollectorAndUpdateInvoiceWithAfipResponse($collector) {
		$collector->rewind();
		while($collector->valid()) {
			$afipBillingData = $collector->current();
			$afipInvoice = Mage::getModel('afip/invoice')->loadInvoiceByOrderInvoiceId($afipBillingData->getId());
			
			if($afipBillingData->getStatus() == DataAuthorizationStatusEnum::ACCEPTED) {
				$afipInvoice->updateAndSave($afipInvoice->getNumber(), $afipInvoice->getType(), $afipBillingData->getCae(), $afipBillingData->getCaeDueDate(), $afipBillingData->getAuthDate(), NULL, AfipInvoice::AUTHORIZED);
				$invoice = Mage::getModel('sales/order_invoice')->load($afipInvoice->getOrderInvoiceId());
				$invoice->addComment("La Factura ha sido autorizada por la AFIP con el número " . $afipInvoice->getNumber(),false,false);
				$invoice->save();
			} else if(($afipBillingData->getStatus() == DataAuthorizationStatusEnum::INVALID) ||($afipBillingData->getStatus() == DataAuthorizationStatusEnum::REJECTED)) {
				$afipInvoice->updateAndSave(NULL, $afipInvoice->getType(), NULL, NULL, NULL, $afipBillingData->getErrors()->getListAsString(), AfipInvoice::REJECTED);
				$invoice = Mage::getModel('sales/order_invoice')->load($afipInvoice->getOrderInvoiceId());
				$invoice->addComment("La Factura ha sido rechazada por la AFIP. Detalles: " . $afipBillingData->getErrors()->getListAsString(),false,false);
				$invoice->save();
				self::sendAfipInvoiceRejectedEmail($invoice, $afipInvoice);
			} else {
				$afipInvoice->updateAndSave(NULL, $afipInvoice->getType(), NULL, NULL, NULL, NULL, AfipInvoice::PENDING);
			}
			$collector->next();
		}
		
		return $collector;
	}
	
	/**
	 * Resend an interval of Invoices with lost Response to AFIP
	 *
	 * @param InvoiceManager $invoiceManager        	
	 * @param int $lastRUInvoiceNumber        	
	 * @param int $lastAfipInvoiceNumber        	
	 * @param TypeEnum $billingType        	
	 * @return void
	 */
	public static function loadAndResendLostResponseInvoicesToAfip($invoiceManager, $lastRUInvoiceNumber, $lastAfipInvoiceNumber, $billingType) {
		for($i = $lastRUInvoiceNumber; $i < $lastAfipInvoiceNumber; $i ++) {
			$afipInvoice = Mage::getModel('afip/invoice')->loadInvoiceByNumber($i + 1, $billingType);
			$afipData = $invoiceManager->retrieveDataFor($billingType, $i + 1);
			$afipInvoice->updateAndSave($i + 1, $afipInvoice->getType(), $afipData->CodAutorizacion, $afipData->FchVto, $afipData->FchProceso, NULL, AfipInvoice::AUTHORIZED);
			$invoice = Mage::getModel('sales/order_invoice')->load($afipInvoice->getOrderInvoiceId());
			$invoice->addComment("La Factura ha sido autorizada por la AFIP con el número " . $afipInvoice->getNumber(),false,false);
			$invoice->save();
		}
	}
	
	public static function sendAfipInvoiceRejectedEmail($invoice, $afipInvoice){

		if(!is_null(Mage::getStoreConfig('afip/config/afip_invoice_rejected_email'))){
			
			$templateId = 'afip_invoice_rejected';
			$emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);
			
			$orderNumber = $invoice->getOrder()->getIncrementId();
			$invoiceNumber = $invoice->getIncrementId();
			$rejectionReason = $afipInvoice->getObservations();
			
			$vars = array('order_number' => $orderNumber, 'invoice_number' => $invoiceNumber, 'rejection_reason' => $rejectionReason);
			
			
			$emailTemplate->getProcessedTemplate($vars);
			
			$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email', 1));
			$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name', 1));
			
			$receiveEmail = Mage::getStoreConfig('afip/config/afip_invoice_rejected_email');
			$receiveName = "Usershop";
				
			$emailTemplate->setTemplateSubject('Factura rechazada por AFIP.');
				
			$emailTemplate->send($receiveEmail,$receiveName, $vars);
			
		}
	}
	
}