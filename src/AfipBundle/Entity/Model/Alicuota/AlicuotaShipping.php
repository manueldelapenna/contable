<?php

/**
 * @author manueldelapenna
 *
 */
class AlicuotaShipping
{
	const IVA_0250 = "2,5";
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
		
		$alicuota = self::getAlicuotaForShipping();
		$alicuotaAmounts[$alicuota] = $alicuotaAmounts[$alicuota] + TaxerHelper::getFinalAmountForShippingItem($invoice, $invoice->getOrder()->getOrderCurrencyCode(), $afipInvoice);
				
		return $alicuotaAmounts;
	
	}

	public static function getAlicuotaForShipping(){
	
		return self::IVA_2100;
	}
}