<?php
/**
 * Description of SchedulingExecutor
 *
 * @author manueldelapenna
 */
class Reporter {
	
	
	/**
	 * Performs the Invoices authorization with AFIP for all billing types
	 *
	 * @return void
	 */
	public static function reportPendingAfipInvoices() {
		//si no hay email configurado no se procesa
		if(!is_null(Mage::getStoreConfig('afip/config/afip_invoice_pending_email'))){
			$today = new DateTime();
			$date_to_compare = $today->format('Y-m-d'); //fecha de hoy
			
			//facturas afip del dia anterior sin realizarce
			$pending_invoices = Mage::getModel('afip/invoice')->getCollection()
			->addFieldToFilter('status',array('eq' => AfipInvoice::PENDING));
			
			$pending_invoices->getSelect()->joinInner(
					array('sfi' => 'sales_flat_invoice'), 'main_table.order_invoice_id = sfi.entity_id and created_at < "'.$date_to_compare.'"', array('created_at')
			);
			$pending_invoices->getSelect()->__toString();
			
			 if(count($pending_invoices)){
				
				$templateId = 'afip_invoices_pending';
				$emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);
				
				$vars = array('count' => count($pending_invoices),'date_to_compare'=>$today->format('d/m/Y'));
				
				$emailTemplate->getProcessedTemplate($vars);
				
				$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_support/email', 1));
				$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_support/name', 1));
				
				$receiveEmail = Mage::getStoreConfig('afip/config/afip_invoice_pending_email');
				$receiveName = "Usershop";
					
				$emailTemplate->setTemplateSubject('Facturas pendientes de autorizar por AFIP');
					
				$emailTemplate->send($receiveEmail,$receiveName, $vars);
				
			}
		}
	}
	
}