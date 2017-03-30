<?php
/**
 * Description of InvoicePrinterExecutor
 *
 * @author manueldelapenna
 */
class InvoicePrinterExecutor{
	
	/**
	 * Process ID.
	 */
	const PROCESS_ID = 'block_print_pdf_invoices';
	
	/**
	 * Performs the Invoices Printing
	 *
	 * @return void
	 */
	public static function execute() {

		$sem = new Quanbit_Afip_Model_FileSemaphore(self::PROCESS_ID, NULL, 0, 1800);
						
		if ($sem->isLock()){
				$sem->executeWarningTimeAction();
		}else{	
			$sem->lock();
			try{
				self::executePrinter();
			}catch(Exception $e){
				Mage::logException($e);
				echo $e->getMessage();
			}
			$sem->unlock();
		}
	}
	
	public static function executePrinter(){
		$pendingForPrintingAfipInvoices = Quanbit_Afip_Model_Invoice::getPendingForPrinting();
		
		foreach ($pendingForPrintingAfipInvoices as $afipInvoice){
			$invoice = Mage::getModel('sales/order_invoice')->load($afipInvoice->getOrderInvoiceId());
			$pdf = Mage::getModel('afip/pdf_invoice')->getPdf(array($invoice));
		
			$dir = self::getDirForAfipDocument($afipInvoice);
				
			if (!is_dir($dir))
				mkdir($dir, 0777, 1);
		
			$filename = self::getFilenameForAfipDocument($afipInvoice);
		
			$path = "$dir/$filename";
			$pdf->save($path);
			$afipInvoice->setIsPdfCreated(true);
			$afipInvoice->save();
		}
		$dir = Mage::getBaseDir() . "/afip/barcodes";
		self::delDir($dir);
		
	}
	
	public static function getPointOfSaleOfConfiguratedEnvironment() {
		if (Mage::getStoreConfig ( 'afip/config/enable_prod' )) {
			$environment = Quanbit_Afip_Model_Environment_ProductionEnvironment::getInstance ();
		} else {
			$environment = Quanbit_Afip_Model_Environment_StagingEnvironment::getInstance ();
		}
		return $environment->getPointOfSale();
	}
	
	public static function getDirForAfipDocument(Quanbit_Afip_Model_Invoice $afipInvoice) {
		
		$date = new DateTime($afipInvoice->getAuthorizationDate());
		$year = date_format($date, 'Y');
		$month = date_format($date,'m');
		$day = date_format($date, 'd');
		
		$afipType = Quanbit_Afip_Model_Enums_TypeEnum::getInstance()->getValueFor($afipInvoice->getType());
		$dir = Mage::getBaseDir()."/afip/$afipType/$year/$month/$day";
		
		return $dir;
		
	}
	
	public static function getFilenameForAfipDocument(Quanbit_Afip_Model_Invoice $afipInvoice) {
		
		$invoiceLetter = Quanbit_Afip_Model_Enums_TypeEnum::getLetterForBillingTypeKey($afipInvoice->getType());
			
		$pointOfSale = self::getPointOfSaleOfConfiguratedEnvironment();
		$normalizedPointOfSale = str_pad($pointOfSale, 4, 0, STR_PAD_LEFT);
			
		$normalizedNumber = self::getNormalizedInvoiceNumber($afipInvoice);
		
		$filename = "$invoiceLetter-$normalizedPointOfSale-$normalizedNumber.pdf";
		
		return $filename;
	}
	
	public static function getNormalizedInvoiceNumber($afipInvoice){
		
		$number = $afipInvoice->getNumber();
		return str_pad($number, 8, 0, STR_PAD_LEFT);
		
	}
	
	public static function delDir($dir) {
		if(file_exists($dir)){
			$files = array_diff(scandir($dir), array('.','..'));
			foreach ($files as $file) {
				(is_dir("$dir/$file") && !is_link($dir)) ? delTree("$dir/$file") : unlink("$dir/$file");
			}
			rmdir($dir);
		}
	}
	
}