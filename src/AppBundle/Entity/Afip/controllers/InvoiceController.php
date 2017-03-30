<?php
class Afip_InvoiceController extends Mage_Adminhtml_Controller_Action
{
	public function printAction()
	{
		
		$invoiceId = $this->getRequest()->getParam('invoice_id');
		$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);

		$afipInvoice = Mage::getModel('afip/invoice')->loadInvoiceByOrderInvoiceId($invoiceId);
				
		$dir = Afip_Model_Pdf_InvoicePrinterExecutor::getDirForAfipDocument($afipInvoice);
		$filename = Afip_Model_Pdf_InvoicePrinterExecutor::getFilenameForAfipDocument($afipInvoice);
		$path = "$dir/$filename";
		
		$pdf = file_get_contents($path);
		
		header("Content-type: application/pdf");
		header('Content-Disposition: attachment; filename="' .$filename . '"');
		
		print($pdf);
		exit;
	
	}
	
	public function reauthorizeAction()
	{
		$invoiceId = $this->getRequest()->getParam('invoice_id');
		$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
		
		try{
			$invoiceId = $this->getRequest()->getParam('invoice_id');
			$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
			$afipInvoice = Mage::getModel('afip/invoice')->loadInvoiceByOrderInvoiceId($invoiceId);
		
			if($afipInvoice->getStatus() == Afip_Model_Invoice::REJECTED){
				$afipInvoice->updateAndSave($afipInvoice->getNumber(), $afipInvoice->getType(), NULL, NULL, NULL, NULL, Afip_Model_Invoice::PENDING);
				$customerId = $invoice->getOrder()->getCustomerId();
				$customer = Mage::getModel('customer/customer')->load($customerId);
				
				//No es Resp. Inscripto y la factura es A, se cambia la factura a B				
				if($customer->getIvaCondition() != 2 && $afipInvoice->getType() == Afip_Model_Enums_TypeEnum::A){
					$afipInvoice->setType(Afip_Model_Enums_TypeEnum::B);
					$afipInvoice->save();
				}
				
				//Es Resp. Inscripto y la factura es B, se cambia la factura a A
				if($customer->getIvaCondition() == 2 && $afipInvoice->getType() == Afip_Model_Enums_TypeEnum::B){
					$afipInvoice->setType(Afip_Model_Enums_TypeEnum::A);
					$afipInvoice->save();
				}
				
				$invoice->addComment("El estado de la Factura AFIP ha cambiado Pendiente",false,false);
				$invoice->save();
				$this->_getSession()->addSuccess("El estado de la Factura AFIP ha cambiado a Pendiente");
			}else{
				throw new Afip_Exception_Lib_AfipInvoiceInvalidOperationException();
			}
		}catch(Afip_Exception_Lib_AfipInvoiceInvalidOperationException $e){
			$this->_getSession()->addError('No puede volver a autorizarse una Factura con estado distinto a "Rechazada"');
		}catch(Exception $e){
			$this->_getSession()->addError($e->getMessage());
		}
		
		Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_invoice/view", array('invoice_id'=>$invoiceId)));
	}
	
}