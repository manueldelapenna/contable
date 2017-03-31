<?php
/**
 * Description of Observer
 *
 * @author manueldelapenna
 */
class Observer {
	public function generateInvoice(Varien_Event_Observer $observer) {
		
		if(Mage::getStoreConfig('afip/config/enable') && Mage::getStoreConfig('afip/config/enable_queue')){ 
			
			$data = $observer->getEvent()->getData();
			$invoice = $observer->getEvent()->getInvoice();
			
			$afipInvoice = Mage::getModel('afip/invoice')->loadInvoiceByOrderInvoiceId($invoice->getId());
			
			//if not exists AfipInvoice for Mage_Sales_Model_Order_Invoice
			if (count($afipInvoice->getData()) == 0){
				$paymentMethod = $invoice->getOrder()->getPayment()->getOrigData();
				$storeId = $invoice->getStoreId();

				//el monto de la factura es > 0
				if($invoice->getGrandTotal() > 0){
					//Todos los pagos menos pago en tienda para tienda Argetina y Western Union y NPS para Tienda internacional
					//y Paypal para ambas tiendas
					/*
					 * PRECAUCIÓN: Paypal Maneja devoluciones de dinero que generan Notas de Crédito Magento (Facturas de Abono)
					 * Se deshabilita la facturación AFIP para paypal standard independientemente de la tienda
					 * 
					 * */
					if((($paymentMethod["method"] != "simplepaymentmethod_store_billing" && $storeId == 1) ||
					    ($paymentMethod["method"] == "simplepaymentmethod_western_union" && $storeId == 3) ||
					    ($paymentMethod["method"] == "directdebitpaymentmethod" && $storeId == 3) ||
					    ($paymentMethod["method"] == "qbnpsgateway" && $storeId == 3)) &&
						 $paymentMethod["method"] != "paypal_standard"){
						//save afip invoice
						try {
							$afipInvoice = Mage::getModel('afip/invoice');
							$afipInvoice->setOrderInvoiceId($invoice->getId());
							$afipInvoice->setStatus(AfipInvoice::PENDING);
							
							$orderCurrencyCode = $invoice->getOrder()->getOrderCurrencyCode();
							
							$afipInvoice->setMoneda($orderCurrencyCode);
							
							if($orderCurrencyCode == 'ARS'){
								$afipInvoice->setCotizacionMonedaExtranjeraPesos(1);
							}else{
								$afipInvoice->setCotizacionMonedaExtranjeraPesos($invoice->getBaseToGlobalRate());
							}
							
							$customer = Mage::getModel('customer/customer')->load($invoice->getCustomerId());
							//resp. inscripto
							if ($customer->getIvaCondition() == 2){
								$afipInvoice->setType(TypeEnum::A);
							}else{
								$afipInvoice->setType(TypeEnum::B);
							}
							
							$afipInvoice->save();
						} catch (Exception $e) {
							throw $e;
						}
					}
				}
			}
		}
		
	}
	
}