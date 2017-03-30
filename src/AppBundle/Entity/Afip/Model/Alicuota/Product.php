<?php

/**
 * @author manueldelapenna
 *
 */
class Product extends Mage_Core_Model_Abstract
{
	const IVA_0250 = "2.5";
	const IVA_0500 = "5";
	const IVA_1050 = "10.5";
	const IVA_2100 = "21";
	const IVA_2700 = "27";
	const NO_GRAVADO = "No Gravado";
	const EXENTO = "Exento";
	
	public static function getAlicuotaAmountsFromInvoice($invoice, $afipInvoice) {
	
		$alicuotaAmounts[self::IVA_0250] = 0;
		$alicuotaAmounts[self::IVA_0500] = 0;
		$alicuotaAmounts[self::IVA_1050] = 0;
		$alicuotaAmounts[self::IVA_2100] = 0;
		$alicuotaAmounts[self::IVA_2700] = 0;
		$alicuotaAmounts[self::NO_GRAVADO] = 0;
		$alicuotaAmounts[self::EXENTO] = 0;
		
		$items = $invoice->getAllItems();
			
 		foreach($items as $item){
 		
 			$product = Mage::getModel('catalog/product')->load($item->getProductId());
			$isItemParent = self::isParentItem($item);
			
			if ($isItemParent){
				$alicuota = self::getAlicuotaForProduct($product);
				$alicuotaAmounts[$alicuota] = $alicuotaAmounts[$alicuota] + Quanbit_Afip_Helper_Taxer::getFinalAmountForProductItem($item, $invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice);
			}

 		}
		
		return $alicuotaAmounts;
		
	
	}
	
	public static function isParentItem(Mage_Sales_Model_Order_Invoice_Item $item){
		$order_item = $item->getOrderItem();
		if($order_item->getParentItemId()){
			return false;
		}
		return true;
	}
	
	public static function getAlicuotaForProduct($product){

		$alicuota = $product->getAliquot();
		switch ($alicuota) {
			case null:
				return self::IVA_2100;
			case 0:
				return self::EXENTO;
			case 1:
				return self::IVA_0250;
			case 2:
				return self::IVA_0500;
			case 3:
				return self::IVA_1050;
			case 4:
				return self::IVA_2100;
			case 5:
				return self::IVA_2700;
		}
		
		
		
		if($alicuota != null){
			return $alicuota;
		}else{
			;
		}
	}	
}