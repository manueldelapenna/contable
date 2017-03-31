<?php
$defController = Mage::getBaseDir()
. DS . 'app' . DS . 'code' . DS . 'core'
. DS . 'Mage' . DS . 'Adminhtml' . DS . 'controllers'
. DS . 'Sales' . DS . 'InvoiceController.php';

require_once $defController;

class Afip_Adminhtml_Sales_InvoiceController extends Mage_Adminhtml_Sales_InvoiceController
{
  	/**
     * Export orders with credit card information to CSV format
     */
    public function exportDetailedAction(){
       $fileName   = 'facturas-detalladas-' . gmdate('Y_m_d-H_i_s') . '.csv';
       $grid       = $this->getLayout()->createBlock('adminhtml/sales_invoice_grid');
       $this->_prepareDownloadResponse($fileName, $grid->getDetailedFile());
    }


    /**
     * Export orders with credit card information to excel format
     */

    public function exportDetailedExcelAction() {
        $fileName   = 'facturas-detalladas-' . gmdate('Y_m_d-H_i_s') . '.xls';

        $content = $this->getLayout ()
        ->createBlock('adminhtml/sales_invoice_grid')
        ->getDetailedExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
        exit;
    }
	
}