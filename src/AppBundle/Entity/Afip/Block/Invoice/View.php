<?php
/**
 
/**
 * Adminhtml invoice create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     manueldelapenna
 */
class Afip_Block_Invoice_View
{
    /**
     * Admin session
     *
     * @var Mage_Admin_Model_Session
     */
    
    public function __construct()
    {
        parent::__construct();
        
        //Download AFIP Invoice button        
        $afipInvoice = Mage::getModel('afip/invoice')->afipInvoiceForDownloadFromOrderInvoiceId($this->getInvoice()->getId());
        
        if (count($afipInvoice->getData()) > 0){
        	$this->_addButton('order_reorder', array(
        			'label'     => 'Descargar Factura AFIP',
        			'onclick'   => 'setLocation(\'' . $this->getUrl('afip/invoice/print', array(
        					'invoice_id' => $this->getInvoice()->getId())) . '\')',
        	));
        }
        
        //Re-Authorize AFIP Invoice button        
        $afipInvoice = Mage::getModel('afip/invoice')->afipInvoiceForChangeStateFromOrderInvoiceId($this->getInvoice()->getId());
        
        if (count($afipInvoice->getData()) > 0){
        	$this->_addButton('order_reauthorize', array(
        			'label'     => 'Volver a Autorizar a AFIP',
        			'onclick'   => 'setLocation(\'' . $this->getUrl('afip/invoice/reauthorize', array(
        					'invoice_id' => $this->getInvoice()->getId())) . '\')',
        	));
        }

    }
}
