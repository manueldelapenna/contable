<?php
/**
 * Afip Invoice PDF model
 *
 * @author     manueldelapenna
 */
class Afip_Model_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice {
	
	private $barcode = NULL;
	private $copies = 3;
	
	public function getPdf($invoices = array()) {
		$this->_beforeGetPdf();
		$this->_initRenderer('invoice');
		
		$invoice = $invoices[0];
			
		$customer = Mage::getModel('customer/customer')->load($invoice->getOrder()->getCustomerId());
		$afipInvoice = Mage::getModel('afip/invoice')->loadInvoiceByOrderInvoiceId($invoice->getId());
		$this->barcode = new Afip_Model_Pdf_Barcode_Barcode();
		$this->barcode->addCUIT(33709315329);
		$this->barcode->addInvoiceType(Afip_Model_Enums_TypeEnum::getLetterForBillingTypeKey($afipInvoice->getType()));
		$this->barcode->addPOS(Afip_Model_Pdf_InvoicePrinterExecutor::getPointOfSaleOfConfiguratedEnvironment());
		
		if ($afipInvoice->getType() == Afip_Model_Enums_TypeEnum::A){
			$pdf = Zend_Pdf::load(Mage::getModuleDir('etc', 'Afip') . "/pdfTemplates/invoiceATemplate.pdf");
		}else{
			if($invoice->getOrder()->getOrderCurrencyCode() == 'ARS'){
				$pdf = Zend_Pdf::load(Mage::getModuleDir('etc', 'Afip') . "/pdfTemplates/invoiceBTemplate.pdf");
			}else{
				$pdf = Zend_Pdf::load(Mage::getModuleDir('etc', 'Afip') . "/pdfTemplates/invoiceBDollarTemplate.pdf");
			}
		}
		$this->_setPdf($pdf);
		
		$this->setClientName($invoice->getOrder()->getCustomerName());
		
		$this->setCuitOrDni($afipInvoice,$customer);
				
		$this->setGenerationTime($afipInvoice->getAuthorizationDate());
		$this->setTaxCondition(QBCustomer_Model_Iva::getIvaConditionNameFromIvaConditionNumber($customer->getIvaCondition()));
		
		$billingAddress = $invoice->getBillingAddress();
		$this->setAddress($billingAddress->getStreet1() . " " . $billingAddress->getStreet2() . " (" . $billingAddress->getPostcode() . ") " . $billingAddress->getCity() . ", " . $billingAddress->getRegion() . ", " . $billingAddress->getCountryModel()->getName());
		$this->setBillingNumber(Afip_Model_Pdf_InvoicePrinterExecutor::getPointOfSaleOfConfiguratedEnvironment() ."-". Afip_Model_Pdf_InvoicePrinterExecutor::getNormalizedInvoiceNumber($afipInvoice));
	
		$this->setDueDate($afipInvoice->getCaeDueDate());
		$this->setCae($afipInvoice->getCaeNumber());
		
		$this->printInvoiceItems($invoice, $afipInvoice);
		$this->printShippingItem($invoice, $afipInvoice);
		
		$this->printTaxAmountsAndTotals($invoice, $afipInvoice);
		
		$this->addBarcode();
		
		$this->_afterGetPdf();
		
		return $pdf;
	}
	
	protected function printInvoiceItems($invoice, $afipInvoice){
		
		$items = $invoice->getAllItems();
		$billingType = $afipInvoice->getType();
				
		$adjustCents = TaxerHelper::calculateAdjustTaxAmounts($invoice, $afipInvoice);
		
		foreach($items as $item)
		{
			
			$product = Mage::getModel('catalog/product')->load($item->getProductId());
			
			$isItemParent = AlicuotaProduct::isParentItem($item);
				
			if ($isItemParent){
				$taxPercent = AlicuotaProduct::getAlicuotaForProduct($product);
				
				if($billingType == Afip_Model_Enums_TypeEnum::A){
					$itemTotal = TaxerHelper::getNetoAmountForProductItem($item, $invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice);
					$itemTotal = TaxerHelper::adjustAmount($itemTotal, $adjustCents, $taxPercent);
					
					$itemUnitaryPrice = TaxerHelper::getNetoUnitaryAmountForProductItem($item, $invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice);
					if($taxPercent != AlicuotaProduct::NO_GRAVADO && $taxPercent != AlicuotaProduct::EXENTO){
						$this->addItem($item->getQty(), $product->getName(), $itemUnitaryPrice, $itemTotal, $taxPercent);
					}else{
						$this->addItem($item->getQty(), $product->getName(),$itemUnitaryPrice, $itemTotal, 0);
					}
				}else{
					$itemTotal = TaxerHelper::getFinalAmountForProductItem($item,$invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice, FALSE);
					$itemUnitaryPrice = TaxerHelper::getFinalUnitaryAmountForProductItem($item, $invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice, FALSE);
					if($taxPercent != AlicuotaProduct::NO_GRAVADO && $taxPercent != AlicuotaProduct::EXENTO){
						$this->addItem($item->getQty(), $product->getName(), $itemUnitaryPrice, $itemTotal, $taxPercent);
					}else{
						$this->addItem($item->getQty(), $product->getName(),$itemUnitaryPrice, $itemTotal, 0);
					}
				}
			}
		}
	}
	
	protected function printShippingItem($invoice, $afipInvoice){
		
		$billingType = $afipInvoice->getType();
		
		if($invoice->getShippingAmount() > 0){
			$taxPercent = AlicuotaShipping::getAlicuotaForShipping();
			$shippingDescription = "Envío " . $invoice->getOrder()->getShippingDescription();
			if($billingType == Afip_Model_Enums_TypeEnum::A){
				$shippingTotal = TaxerHelper::getNetoAmountForShippingItem($invoice, $invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice);
				if ($taxPercent != AlicuotaProduct::NO_GRAVADO && $taxPercent != AlicuotaProduct::EXENTO){
					$this->addItem(1, $shippingDescription, $shippingTotal, $shippingTotal, $taxPercent);
				}else{
					$this->addItem(1, $shippingDescription, $shippingTotal, $shippingTotal, 0);
				}
			}else{
				$shippingTotal = TaxerHelper::getFinalAmountForShippingItem($invoice,$invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice, FALSE);
				if ($taxPercent != AlicuotaProduct::NO_GRAVADO && $taxPercent != AlicuotaProduct::EXENTO){
					$this->addItem(1, $shippingDescription, $shippingTotal, $shippingTotal, $taxPercent);
				}else{
					$this->addItem(1, $shippingDescription, $shippingTotal, $shippingTotal, 0);
				}
			}
		}
	}
	
	protected function printTaxAmountsAndTotals($invoice, $afipInvoice){
			
		$ivaAmount = $afipInvoice->getIva_0250() + $afipInvoice->getIva_0500() + $afipInvoice->getIva_1050() + $afipInvoice->getIva_2100() + $afipInvoice->getIva_2700();
		$netoAmount = $afipInvoice->getNeto_0250() + $afipInvoice->getNeto_0500() + $afipInvoice->getNeto_1050() + $afipInvoice->getNeto_2100() + $afipInvoice->getNeto_2700() + $afipInvoice->getNetoExento();
		
		if ($afipInvoice->getType() == Afip_Model_Enums_TypeEnum::A){
			
			$this->setTaxAmount0250($afipInvoice->getNeto_0250(), $afipInvoice->getIva_0250());
			$this->setTaxAmount0500($afipInvoice->getNeto_0500(), $afipInvoice->getIva_0500());
			$this->setTaxAmount1050($afipInvoice->getNeto_1050(), $afipInvoice->getIva_1050());
			$this->setTaxAmount2100($afipInvoice->getNeto_2100(), $afipInvoice->getIva_2100());
			$this->setTaxAmount2700($afipInvoice->getNeto_2700(), $afipInvoice->getIva_2700());
			$this->setTaxAmountExento($afipInvoice->getNetoExento(), 0);
			
			$this->setIVAAmount($afipInvoice->getTotalIva());
			$this->setSubtotalAmount($afipInvoice->getSubtotal(),$afipInvoice->getType());
			$this->setTotalAmount($afipInvoice->getTotal(),$afipInvoice->getType());
		}else{
			if($invoice->getOrder()->getOrderCurrencyCode() != 'ARS'){
				$this->setObservations($afipInvoice);
			}
			
			$this->setSubtotalAmount($invoice->getGrandTotal(),$afipInvoice->getType());
			$this->setTotalAmount($invoice->getGrandTotal(),$afipInvoice->getType());
		}
		
		
	}
	
	/**
	 * Sets the client name for all pages.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setClientName($value)
	{
		$this->setTextFont(10);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($value, 70, 657, 'UTF-8');
		}
	}
	
	/**
	 * Sets the generation time for all pages.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setGenerationTime($value)
	{
		$this->setTextFont(10);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText(date("d/m/Y", strtotime($value)), 385, 761, 'UTF-8');
		}
		$this->barcode->addGenerationTime($value);
	}
	
	/**
	 * Sets the tax condition for all pages.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setTaxCondition($value)
	{
		$this->setTextFont(10);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($value, 92, 623, 'UTF-8');
		}
	}
	
	/**
	 * Sets the client document number for all pages (only for CUIT).
	 *
	 * @param $value
	 * @return void
	 */
	protected function setCUITNumber($value)
	{
		$this->setTextFont(10);
		preg_match("/(\d{2})(\d{8})(\d{1})/", $value, $matches);
	
		if (count($matches) == 4)
		{
			$cuit = $matches[1] . "-" . $matches[2] . "-" . $matches[3];
	
			if (strlen($cuit) == 13){
				for($i=0;$i<$this->copies;$i++){
					$this->_getPdf()->pages[$i]->drawText($cuit, 298, 623, 'UTF-8');
				}
			}
		}
	}
	
	/**
	 * Sets the client document number for all pages (only for DNI).
	 *
	 * @param $value
	 * @return void
	 */
	protected function setDNINumber($value)
	{
		$this->setTextFont(10);
		$value = number_format($value,"0","",".");
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($value, 298, 623, 'UTF-8');
		}
		
	}
	
	/**
	 * Sets the client document number or CUIT according Customer Iva Condition.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setCuitOrDni($afipInvoice,$customer){
		$ivaAmount = $afipInvoice->getIva_0250() + $afipInvoice->getIva_0500() + $afipInvoice->getIva_1050() + $afipInvoice->getIva_2100() + $afipInvoice->getIva_2700();
		$netoAmount = $afipInvoice->getNeto_0250() + $afipInvoice->getNeto_0500() + $afipInvoice->getNeto_1050() + $afipInvoice->getNeto_2100() + $afipInvoice->getNeto_2700() + $afipInvoice->getNetoExento();
		//Factura B
		if($afipInvoice->getType() == Afip_Model_Enums_TypeEnum::B){
			//>= $1000
			if(($customer->getIvaCondition() == 1 || $customer->getIvaCondition() == 4) && ($ivaAmount + $netoAmount) >= 1000){
				if(strlen($customer->getTaxvat()) == 11){
					$this->setCUITNumber($customer->getTaxvat());
				}else{
					$this->setDNINumber($customer->getTaxvat());
				}
			}else{
				//Responsable monotributo o Exento
				if($customer->getIvaCondition() == 3 || $customer->getIvaCondition() == 5){
					if(strlen($customer->getTaxvat()) == 11){
						$this->setCUITNumber($customer->getTaxvat());
					}else{
						$this->setDNINumber($customer->getTaxvat());
					}
				}
			}
		//Factura A	
		}else{
			$this->setCUITNumber($customer->getTaxvat());
		}
		  
		  
		
	}
	
	/**
	 * Sets the address for all pages.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setAddress($value)
	{
		$this->setTextFont(10);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($value, 80, 640, 'UTF-8');
		}
	}
	
	/**
	 * Sets the invoice number for all pages.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setBillingNumber($value)
	{
		$this->setTextFont(16);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText(str_pad($value, 8, "0", STR_PAD_LEFT), 430, 793, 'UTF-8');
		}
	}
	
	/**
	 * Sets the IVA amount for all pages.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setIVAAmount($value)
	{
		$this->setNumbersFont(10);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($value,11), 498, 106, 'UTF-8');
		}
	}
	
	/**
	 * Sets the observations for all pages.
	 *
	 * @param $afipInvoice
	 * @param $currencyCode
	 * @return void
	 */
	protected function setObservations($afipInvoice)
	{
		$totalAR = number_format($afipInvoice->getTotal(), 2, ",", ".");
		$cambio = number_format($afipInvoice->getCotizacionMonedaExtranjeraPesos(), 4, ",", ".");
		$text1 = "Los montos de esta factura se encuentran expresados en Dólares Estadounidenses.";
		$text2 = "El monto final de la factura equivale a $$totalAR Pesos Argentinos.";
		$text3 = "Tipo de cambio $$cambio por Dólar Estadounidense.";
		$this->setTextFont(8);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($text1, 30, 112, 'UTF-8');
			$this->_getPdf()->pages[$i]->drawText($text2, 30, 100, 'UTF-8');
			$this->_getPdf()->pages[$i]->drawText($text3, 30, 88, 'UTF-8');
		}
	}
	
	/**
	 * Sets the subtotal amount for all pages.
	 *
	 * @param $value
	 * @param $type
	 * @return void
	 */
	protected function setSubtotalAmount($value, $type)
	{
		$this->setNumbersFont(10);
		if ($type ==  Afip_Model_Enums_TypeEnum::A){
			for($i=0;$i<$this->copies;$i++){
				$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($value,11), 498, 126, 'UTF-8');
			}
		}else{
			for($i=0;$i<$this->copies;$i++){
				$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($value,11), 498, 109, 'UTF-8');
			}
		}
	}
	
	/**
	 * Sets the total amount for all pages.
	 *
	 * @param $value
	 * @param $type
	 * @return void
	 */
	protected function setTotalAmount($value, $type)
	{
		$this->setNumbersFont(10);
		if ($type ==  Afip_Model_Enums_TypeEnum::A){
			for($i=0;$i<$this->copies;$i++){
				$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($value,11), 498, 86, 'UTF-8');
			}
		}else{
			for($i=0;$i<$this->copies;$i++){
				$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($value,11), 498, 89, 'UTF-8');
			}				
		}
	}
	
	/**
	 * Sets the due date for all pages.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setDueDate($value)
	{
		$this->setTextFont(10);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText(date("d/m/Y", strtotime($value)), 436, 57, 'UTF-8');
		}
	}
	
	/**
	 * Sets the CAE for all pages.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setCae($value)
	{
		$this->setTextFont(10);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($value, 466, 67, 'UTF-8');
		}
		$this->barcode->addCAE($value);
	}
	
	/**
	 * Sets the tax amount (2,5%) of current page.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setTaxAmount0250($valueNeto, $valueIva)
	{
		$this->setNumbersFont(8);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueNeto,10), 86, 128, 'UTF-8');
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueIva,10), 86, 108, 'UTF-8');
		}
	}
	
	
	
	/**
	 * Sets the tax amount (5%) of current page.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setTaxAmount0500($valueNeto, $valueIva)
	{
		$this->setNumbersFont(8);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueNeto,10), 142, 128, 'UTF-8');
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueIva,10), 142, 108, 'UTF-8');
		}
	}
	
	/**
	 * Sets the tax amount (10,50%) of current page.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setTaxAmount1050($valueNeto, $valueIva)
	{
		$this->setNumbersFont(8);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueNeto,10), 198, 128, 'UTF-8');
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueIva,10), 198, 108, 'UTF-8');
		}
	}
	
	/**
	 * Sets the tax amount (21%) for all pages.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setTaxAmount2100($valueNeto, $valueIva)
	{
		$this->setNumbersFont(8);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueNeto,10), 254, 128, 'UTF-8');
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueIva,10), 254, 108, 'UTF-8');
		}
	}
	
	/**
	 * Sets the tax amount (27%) of current page.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setTaxAmount2700($valueNeto, $valueIva)
	{
		$this->setNumbersFont(8);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueNeto,10), 310, 128, 'UTF-8');
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueIva,10), 310, 108, 'UTF-8');
		}
	}
	
	/**
	 * Sets the tax amount (Exento) of current page.
	 *
	 * @param $value
	 * @return void
	 */
	protected function setTaxAmountExento($valueNeto, $valueIva)
	{
		$this->setNumbersFont(8);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueNeto,10), 366, 128, 'UTF-8');
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($valueIva,10), 366, 108, 'UTF-8');
		}
	}
	
	
	/**
	 * Adds a new item into current page.
	 *
	 * @param float $quantity
	 * @param string $description
	 * @param float $amount
	 * @param float $iva
	 * @return void
	 */
	protected function addItem($quantity, $description, $unitaryPrice, $amount, $iva = 0)
	{
		$this->setTextFont(10);
		$description = wordwrap($description, 90);
		$lines = explode("\n", $description);

		$initLineY = 574 - (15 * $this->itemLine);
		$this->setNumbersFont();
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($quantity, 8), 30, $initLineY, 'UTF-8');
		}
		
		$this->setTextFont(8);
		foreach ($lines as $k => $line)
		{
			$yPos = 574 - (15 * $this->itemLine++);
			//$line = ($k > 0) ? "   " . $line : $line;
			for($i=0;$i<$this->copies;$i++){
				$this->_getPdf()->pages[$i]->drawText($line, 86, $yPos, 'UTF-8');
			}
		}
		
		$this->setNumbersFont();
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($unitaryPrice,11,4), 424, $initLineY, 'UTF-8');
		}
		
		$this->setNumbersFont(6);
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedText("(IVA $iva%)",12), 368, $initLineY, 'UTF-8');
		}
		
		$this->setNumbersFont();
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText($this->asMonospacedNumber($amount,11), 498, $initLineY, 'UTF-8');
		}
		
	}
	
	protected function asMonospacedNumber($value, $size,$decimals = 2){
		return str_pad(number_format($value, $decimals, ",", "."), $size, " ", STR_PAD_LEFT);
	}
	
	protected function asMonospacedText($value, $size){
		return str_pad($value, $size, " ", STR_PAD_LEFT);
	}
	
	/**
	 * Sets the number font for PDF document.
	 *
	 * @param int $size
	 * @return void
	 */
	protected function setNumbersFont($size = 10)
	{
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER), $size);
			$this->_getPdf()->pages[$i]->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		}
	}
	
	/**
	 * Sets the text font for PDF document pages.
	 *
	 * @param int $size
	 * @return void
	 */
	protected function setTextFont($size = 12)
	{
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), $size);
			$this->_getPdf()->pages[$i]->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		}
	}
	
	/**
	 * Adds a barcode to current page.
	 *
	 * @return void;
	 */
	protected function addBarcode()
	{
		$posX = 92;
		$posY = 43;
		$image = Zend_Pdf_Image::imageWithPath($this->barcode->getBarcodeImage());
			
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawImage($image, $posX, $posY, $image->getPixelWidth() + $posX, $image->getPixelHeight() + $posY);
		}
		$this->setNumbersFont(7);
		
		for($i=0;$i<$this->copies;$i++){
			$this->_getPdf()->pages[$i]->drawText(str_pad($this->barcode->getBarcodeValue(), 39, "*", STR_PAD_LEFT), $posX, $posY, 'UTF-8');
		}
	}
}
