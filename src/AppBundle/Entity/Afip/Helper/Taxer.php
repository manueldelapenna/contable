<?php
/**
 * Taxer class.
 *
 * @author Quanbit Sofware SA
 * @author manueldelapenna
 */
class Quanbit_Afip_Helper_Taxer extends Mage_Core_Helper_Abstract{
	
	/**
	 * Returns neto amount unitary price for product item
	 * @param Mage_Sales_Model_Order_Invoice_Item $item
	 * @param string $orderCurrencyCode
	 * @param boolean $currencyAR
	 * @param Quanbit_Afip_Model_Invoice $afipInvoice
	 * @return float
	 */
	public static function getNetoUnitaryAmountForProductItem($item, $orderCurrencyCode, $afipInvoice, $currencyAR = TRUE)
	{
		$itemTotal = self::getFinalAmountForProductItem($item, $orderCurrencyCode, $afipInvoice, $currencyAR);
		
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$alicuota = self::normalizeAlicuota(Quanbit_Afip_Model_Alicuota_Product::getAlicuotaForProduct($product));
		
		$itemNeto = $itemTotal / ($alicuota/100 + 1);
				
		$price =  round($itemNeto / $item->getQty(),4);
		
		return $price;
	}
	
	/**
	 * Returns neto amount price for product item
	 * @param Mage_Sales_Model_Order_Invoice_Item $item
	 * @param string $orderCurrencyCode
	 * @param boolean $currencyAR
	 * @param Quanbit_Afip_Model_Invoice $afipInvoice
	 * @return float
	 */
	public static function getNetoAmountForProductItem($item, $orderCurrencyCode, $afipInvoice, $currencyAR = TRUE)
	{
		$itemUnitary = self::getNetoUnitaryAmountForProductItem($item, $orderCurrencyCode, $afipInvoice);
		
		$itemNeto = Quanbit_Afip_Helper_DataType_Number::truncate($itemUnitary * $item->getQty(), 2);
				
		return $itemNeto;
		
	}
	
	/**
	 * Returns neto amount price for shipping item
	 * @param Mage_Sales_Model_Order_Invoice $invoice
	 * @param string $orderCurrencyCode
	 * @param boolean $currencyAR
	 * @param Quanbit_Afip_Model_Invoice $afipInvoice
	 * @return float
	 */
	public static function getNetoAmountForShippingItem($invoice, $orderCurrencyCode, $afipInvoice, $currencyAR = TRUE)
	{
		$shippingAmount = $invoice->getShippingAmount() - $invoice->getOrder()->getShippingDiscountAmount();
		$itemTotal = self::getFinalAmountForShippingItem($invoice, $orderCurrencyCode, $afipInvoice, $currencyAR);
		
		$shippingAmount = $invoice->getShippingAmount() - $invoice->getOrder()->getShippingDiscountAmount();
		$alicuota = self::normalizeAlicuota(Quanbit_Afip_Model_Alicuota_Shipping::getAlicuotaForShipping());
		
		$itemNeto = Quanbit_Afip_Helper_DataType_Number::truncate($itemTotal / ($alicuota/100 + 1), 2);
				
		return $itemNeto;
	}
	
	/**
	 * Returns final amount unitary price for product item
	 * @param Mage_Sales_Model_Order_Invoice_Item $item
	 * @param string $orderCurrencyCode
	 * @param boolean $currencyAR
	 * @param Quanbit_Afip_Model_Invoice $afipInvoice
	 * @return float
	 */
	public static function getFinalUnitaryAmountForProductItem($item, $orderCurrencyCode, $afipInvoice, $currencyAR = TRUE)
	{
		$price =  Quanbit_Afip_Helper_DataType_Number::truncate(self::getFinalAmountForProductItem($item, $orderCurrencyCode, $afipInvoice, $currencyAR) / $item->getQty(),4);
						
		return $price;
	}
	
	/**
	 * Returns final amount price for product item
	 * @param Mage_Sales_Model_Order_Invoice_Item $item
	 * @param string $orderCurrencyCode
	 * @param boolean $currencyAR
	 * @param Quanbit_Afip_Model_Invoice $afipInvoice
	 * @return float
	 */
	public static function getFinalAmountForProductItem($item, $orderCurrencyCode, $afipInvoice, $currencyAR = TRUE)
	{
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		
		$priceType = $product->getPriceType();
		
		if(!is_null($priceType) && $priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC){
			$discount = 0;
			$orderItem = $item->getOrderItem();
			foreach ($orderItem->getChildrenItems() as $child) {
				$discount += $child->getDiscountAmount();
			}
			$price = $item->getRowTotal() - $discount;
		}else{
			$price = $item->getRowTotal() - $item->getDiscountAmount();
		}
		if($currencyAR){
			$price = self::convertPrice($orderCurrencyCode, $afipInvoice, $price);
		}
		
		return $price;
	}
		
	/**
	 * Returns final amount price for shipping item
	 * @param Mage_Sales_Model_Order_Invoice $invoice
	 * @param string $orderCurrencyCode
	 * @param boolean $currencyAR
	 * @param Quanbit_Afip_Model_Invoice $afipInvoice
	 * @return float
	 */
	public static function getFinalAmountForShippingItem($invoice, $orderCurrencyCode, $afipInvoice, $currencyAR = TRUE)
	{
		$shippingAmount = $invoice->getShippingAmount() - $invoice->getOrder()->getShippingDiscountAmount();
		$price = $shippingAmount;
		if($currencyAR){
			$price = self::convertPrice($orderCurrencyCode, $afipInvoice, $price);
		}
		
		return $price;
	}
	
	protected static function normalizeAlicuota($alicuota){
		if($alicuota == Quanbit_Afip_Model_Alicuota_Product::EXENTO || $alicuota == Quanbit_Afip_Model_Alicuota_Shipping::NO_GRAVADO){
			$alicuota = 0;
		}
		return $alicuota;
	}
	
	public static function convertPrice($orderCurrencyCode, $afipInvoice, $price){
		
		if($orderCurrencyCode != 'ARS'){
			$convertedPrice = round($price * $afipInvoice->getCotizacionMonedaExtranjeraPesos(), 2);
			return $convertedPrice;
		}
		
		return $price;
	}
	
	public static function calculateAdjustTaxAmounts($invoice, $afipInvoice){
	
		$netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_0250] = 0;
		$netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_0500] = 0;
		$netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_1050] = 0;
		$netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_2100] = 0;
		$netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_2700] = 0;
		$netTotals[Quanbit_Afip_Model_Alicuota_Product::EXENTO] = 0;
	
		$items = $invoice->getAllItems();
		
		foreach($items as $item){
			$product = Mage::getModel('catalog/product')->load($item->getProductId());
			$isItemParent = Quanbit_Afip_Model_Alicuota_Product::isParentItem($item);
			if ($isItemParent){
				$taxPercent = Quanbit_Afip_Model_Alicuota_Product::getAlicuotaForProduct($product);
				$itemPrice = Quanbit_Afip_Helper_Taxer::getNetoAmountForProductItem($item, $invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice);
				$netTotals[$taxPercent] += $itemPrice;
			}
		}
		
		$taxPercent = Quanbit_Afip_Model_Alicuota_Shipping::getAlicuotaForShipping();
		$shippingPrice = Quanbit_Afip_Helper_Taxer::getNetoAmountForShippingItem($invoice, $invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice);
		$netTotals[$taxPercent] += $shippingPrice;
	
		$adjustCents[Quanbit_Afip_Model_Alicuota_Product::IVA_0250] = $afipInvoice->getNeto_0250() - $netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_0250];
		$adjustCents[Quanbit_Afip_Model_Alicuota_Product::IVA_0500] = $afipInvoice->getNeto_0500() - $netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_0500];
		$adjustCents[Quanbit_Afip_Model_Alicuota_Product::IVA_1050] = $afipInvoice->getNeto_1050() - $netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_1050];
		$adjustCents[Quanbit_Afip_Model_Alicuota_Product::IVA_2100] = $afipInvoice->getNeto_2100() - $netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_2100];
		$adjustCents[Quanbit_Afip_Model_Alicuota_Product::IVA_2700] = $afipInvoice->getNeto_2700() - $netTotals[Quanbit_Afip_Model_Alicuota_Product::IVA_2700];
		$adjustCents[Quanbit_Afip_Model_Alicuota_Product::EXENTO] = $afipInvoice->getNetoExento() - $netTotals[Quanbit_Afip_Model_Alicuota_Product::EXENTO];
	
		foreach($adjustCents as $key => $value){
			$adjustCents[$key] = 100 * round($value, 2);
		}
		
		return $adjustCents;
	
	}
	
	public static function adjustAmount($amount, &$adjustCents, $taxPercent){
	
		if($adjustCents[$taxPercent] > 0){
			$newAmount = $amount + 0.01;
			$adjustCents[$taxPercent] = $adjustCents[$taxPercent] - 1;
			return $newAmount;
		}
		
		return $amount;
	
		
		
	}

}