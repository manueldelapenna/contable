<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Afip_Block_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'sales_invoice_grid' );
		$this->setUseAjax ( true );
		$this->setDefaultSort ( 'created_at' );
		$this->setDefaultDir ( 'DESC' );
		$this->setSaveParametersInSession ( true );
	}
	
	/**
	 * Retrieve collection class
	 *
	 * @return string
	 */
	protected function _getCollectionClass() {
		return 'sales/order_invoice_grid_collection';
	}
	protected function _prepareCollection() {
		$collection = Mage::getResourceModel ( $this->_getCollectionClass () );
		
		$collection->getSelect ()->joinLeft ( array (
				'a' => 'afip_invoice' 
		), 'main_table.entity_id = a.order_invoice_id' );

		$this->setCollection ( $collection ); $collection->getSelect()->__toString();
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'increment_id', array (
				'header' => Mage::helper ( 'sales' )->__ ( 'Invoice #' ),
				'index' => 'increment_id',
				'filter_index' => 'main_table.increment_id',
				'type' => 'text' 
		) );
		
		$this->addColumn ( 'created_at', array (
				'header' => Mage::helper ( 'sales' )->__ ( 'Invoice Date' ),
				'index' => 'created_at',
				'filter_index' => 'main_table.created_at',
				'type' => 'datetime' 
		) );
		
		$this->addColumn ( 'order_increment_id', array (
				'header' => Mage::helper ( 'sales' )->__ ( 'Order #' ),
				'index' => 'order_increment_id',
				'filter_index' => 'main_table.order_increment_id',
				'type' => 'text' 
		) );
		
		$this->addColumn ( 'order_created_at', array (
				'header' => Mage::helper ( 'sales' )->__ ( 'Order Date' ),
				'index' => 'order_created_at',
				'filter_index' => 'main_table.order_created_at',
				'type' => 'datetime' 
		) );
		
		$this->addColumn ( 'billing_name', array (
				'header' => Mage::helper ( 'sales' )->__ ( 'Bill to Name' ),
				'index' => 'billing_name' ,
				'filter_index' => 'main_table.billing_name',
		) );
		
		$this->addColumn ( 'state', array (
				'header' => Mage::helper ( 'sales' )->__ ( 'Status' ),
				'index' => 'state',
				'filter_index' => 'main_table.state',
				'type' => 'options',
				'options' => Mage::getModel ( 'sales/order_invoice' )->getStates () 
		) );
		
		$this->addColumn ( 'number', array (
				'header' => 'AFIP #',
				'index' => 'number',
				'filter_index' => 'a.number',
				'width' => '100px',
				'type' => 'number' 
		) );
		
		$this->addColumn ( 'type', array (
				'header' => 'Tipo',
				'index' => 'type',
				'filter_index' => 'a.type',
				'width' => '50px',
				'type' => 'options',
				'options' => Mage::getModel ( 'afip/invoice' )->getTypes () 
		) );
		
		$this->addColumn ( 'authorization_date', array (
				'header' => 'F. Aut',
				'index' => 'authorization_date',
				'filter_index' => 'a.authorization_date',
				'width' => '100px',
				'type' => 'date' 
		) );
		
		$this->addColumn ( 'status', array (
				'header' => 'Estado AFIP',
				'index' => 'status',
				'filter_index' => 'a.status',
				'width' => '90px',
				'type' => 'options',
				'options' => Mage::getModel ( 'afip/invoice' )->getStates () 
		) );
		
		$this->addColumn ( 'grand_total', array (
				'header' => Mage::helper ( 'customer' )->__ ( 'Amount' ),
				'index' => 'grand_total',
				'filter_index' => 'main_table.grand_total',
				'type' => 'currency',
				'align' => 'right',
				'currency' => 'order_currency_code' 
		) );
		
		$this->addColumn ( 'action', array (
				'header' => Mage::helper ( 'sales' )->__ ( 'Action' ),
				'width' => '50px',
				'type' => 'action',
				'getter' => 'getId',
				'actions' => array (
						array (
								'caption' => Mage::helper ( 'sales' )->__ ( 'View' ),
								'url' => array (
										'base' => '*/sales_invoice/view' 
								),
								'field' => 'invoice_id' 
						) 
				),
				'filter' => false,
				'sortable' => false,
				'is_system' => true 
		) );
		
		if(Mage::getSingleton('admin/session')->isAllowed('admin/sales/export_invoices_info')){
			$this->addExportType ( '*/*/exportCsv', Mage::helper ( 'sales' )->__ ( 'CSV' ) );
			$this->addExportType ( '*/*/exportExcel', Mage::helper ( 'sales' )->__ ( 'Excel XML' ) );
			$this->addExportType ( '*/*/exportDetailed', Mage::helper ( 'sales' )->__ ( 'CSV Detallado' ) );
			$this->addExportType ( '*/*/exportDetailedExcel', Mage::helper ( 'sales' )->__ ( 'Excel Detallado' ) );
		}
		
		
		return parent::_prepareColumns ();
	}
	protected function _prepareMassaction() {
		$this->setMassactionIdField ( 'entity_id' );
		$this->getMassactionBlock ()->setFormFieldName ( 'invoice_ids' );
		$this->getMassactionBlock ()->setUseSelectAll ( false );
		
		$this->getMassactionBlock ()->addItem ( 'pdfinvoices_order', array (
				'label' => Mage::helper ( 'sales' )->__ ( 'PDF Invoices' ),
				'url' => $this->getUrl ( '*/sales_invoice/pdfinvoices' ) 
		) );
		
		return $this;
	}
	public function getRowUrl($row) {
		if (! Mage::getSingleton ( 'admin/session' )->isAllowed ( 'sales/order/invoice' )) {
			return false;
		}
		
		return $this->getUrl ( '*/sales_invoice/view', array (
				'invoice_id' => $row->getId () 
		) );
	}
	public function getGridUrl() {
		return $this->getUrl ( '*/*/grid', array (
				'_current' => true 
		) );
	}
	
	/**
	 * *******CUSTOM EXPORTS**********
	 */
	
	/* CSV */
	public function getDetailedFile() {
		$this->_isExport = true;
		$this->_prepareGrid ();
		$io = new Varien_Io_File ();
		$path = Mage::getBaseDir ( 'var' ) . DS . 'export' . DS;
		$name = md5 ( microtime () );
		$file = $path . DS . $name . '.csv';
		while ( file_exists ( $file ) ) {
			sleep ( 1 );
			$name = md5 ( microtime () );
			$file = $path . DS . $name . '.csv';
		}
		$io->setAllowCreateFolders ( true );
		$io->open ( array (
				'path' => $path 
		) );
		$io->streamOpen ( $file, 'w+' );
		$io->streamLock ( true );
		$io->streamWriteCsv ( array (
				'Nº Factura Magento',
				'Fecha Factura Magento',
				'Nº Pedido',
				'Fecha Pedido',
				'Cliente',
				'Domicilio',
				'Localidad',
				'Provincia',
				'Pais',
				'Estado',
				'Importe',
				'Moneda',
				'Cotización Moneda en $AR',
				'Total $AR',
				'Nº Factura AFIP',
				'Tipo Factura',
				'F. Autorización',
				'Nº CAE',
				'Vencimiento CAE',
				'Estado AFIP',
				'Observaciones AFIP',
				'Neto 2,5%',
				'IVA 2,5%',
				'Neto 5%',
				'IVA 5%',
				'Neto 10,5%',
				'IVA 10,5%',
				'Neto 21%',
				'IVA 21%',
				'Neto 27%',
				'IVA 27%',
				'Neto Exento',
				'Subtotal',
				'Total IVA',
				'Total',
				'Forma Pago',
				'Cuotas',
				'Pago Tarjeta',
				'Pago DA',
				'F. Presentacion',
				'Pago Tarj. Debito',
				'Pago Efect.',
				'Pago P.Facil',
				'Pago W.Union',
				'Pago Transf. Arg',
				'Pago Transf. Ext',
				'Pago Paypal'
		) );
		
		$this->_exportIterateCollectionDetailed ( '_exportCsvItem', $io );
		if ($this->getCountTotals ()) {
			$io->streamWriteCsv ( $this->_getExportTotals () );
		}
		$io->streamUnlock ();
		$io->streamClose ();
		return array (
				'type' => 'filename',
				'value' => $file,
				'rm' => false 
		);
	}
	public function _exportIterateCollectionDetailed($callback, $io) {
		$originalCollection = $this->getCollection ();
		$count = null;
		$page = 1;
		$lPage = null;
		$break = false;
		$ourLastPage = 50; // MAX 50 PAGES OF SIZE defined in $this->_exportPageSize
		
		$collection_to_find = clone $originalCollection;
			
		$collection_to_find->getSelect()->join(
				array('sfop' => 'sales_flat_order_payment'),
				'main_table.order_id = sfop.parent_id',
				array('method', 'cc_type','additional_data','amount_ordered','check_cash_amount','check_debit_card_amount','check_credit_card_amount')
		);
		
		$collection_to_find->getSelect()->join(
				array('sfi' => 'sales_flat_invoice'),
				'main_table.entity_id = sfi.entity_id',
				array())->join(
						array('sfoa' => 'sales_flat_order_address'),
						'sfi.billing_address_id = sfoa.entity_id',
						array('street', 'city','region','country_id')
				);
					
				$collection_to_find->getSelect()->joinLeft(
						array('ddr' => 'directdebit_record'), 'main_table.entity_id = ddr.invoice', array('presentation_date'));
		
		while ( $break !== true ) {
			$collection = clone $originalCollection;
			
			$collection->setPageSize ( $this->_exportPageSize );
			$collection->setCurPage ( $page );
			$collection->load ();
			if (is_null ( $count )) {
				$count = $collection->getSize ();
				$lPage = $collection->getLastPageNumber ();
			}
			if ($lPage == $page || $ourLastPage == $page) {
				$break = true;
			}
			$page ++;
			foreach ( $collection as $invoice ) {
				
				unset($paymentDescription);
				$paymentDescription = $this->getPaymentDescription($invoice);
				
				unset($streetArray);
				unset($street);
				$streets = explode("\n", $invoice->getStreet());
				$street = $streets[0];
				
				if(!is_null($invoice->getType())){
					$invoiceType = TypeEnum::getLetterForBillingTypeKey($invoice->getType());
					$afipStatus = Afip_Model_Invoice::getStatusName($invoice->getStatus());
				}else{
					$invoiceType = "";
					$afipStatus = "";
				}
				
				$io->streamWriteCsv ( array (
						'Nº Factura Magento' => $invoice->getIncrementId (),
						'Fecha Factura Magento' => $invoice->getCreatedAt (),
						'Nº Pedido' => $invoice->getOrderIncrementId (),
						'Fecha Pedido' => $invoice->getOrderCreatedAt (),
						'Cliente' => $invoice->getBillingName (),
						'Domicilio' => $street,
						'Localidad' => $invoice->getCity(),
						'Provincia' => $invoice->getRegion(),
						'Pais' => Mage::app()->getLocale()->getCountryTranslation($invoice->getCountryId()),
						'Estado' => $invoice->getStateName (),
						'Importe' => round($invoice->getGrandTotal(),2),
						'Moneda' => $invoice->getOrder ()->getOrderCurrencyCode (),
						'Cotización Moneda $AR' => $invoice->getCotizacionMonedaExtranjeraPesos (),
						'Total $AR' => $invoice->getTotal (),
						'Nº Factura AFIP' => $invoice->getNumber (),
						'Tipo Factura' => $invoiceType,
						'F. Autorización' => $invoice->getAuthorizationDate (),
						'Nº CAE' => $invoice->getCaeNumber (),
						'Vencimiento CAE' => $invoice->getCaeDueDate (),
						'Estado AFIP' => $afipStatus,
						'Observaciones AFIP' => $invoice->getObservations (),
						'Neto 2,5%' => $invoice->getNeto_0250 (),
						'IVA 2,5%' => $invoice->getIva_0250 (),
						'Neto 5%' => $invoice->getNeto_0500 (),
						'IVA 5%' => $invoice->getIva_0500 (),
						'Neto 10,5%' => $invoice->getNeto_1050 (),
						'IVA 10,5%' => $invoice->getIva_1050 (),
						'Neto 21%' => $invoice->getNeto_2100 (),
						'IVA 21%' => $invoice->getIva_2100 (),
						'Neto 27%' => $invoice->getNeto_2700 (),
						'IVA 27%' => $invoice->getIva_2700 (),
						'Neto Exento' => $invoice->getNetoExento (),
						'Subtotal' => $invoice->getSubtotal (),
						'Total IVA' => $invoice->getTotalIva (),
						'Total' => $invoice->getTotal (),
						'Forma Pago' => $paymentDescription['name'],
						'Cuotas' => $paymentDescription["quotas"],
						'Pago Tarjeta' => $paymentDescription["creditCard"],
						'Pago DA' => $paymentDescription["debit"],
						'F. Presentacion' => $invoice->getPresentationDate(),
						'Pago Tarj. Debito' => $paymentDescription["debitCard"],
						'Pago Efect.' => $paymentDescription["cash"],
						'Pago P.Facil' => $paymentDescription["pagoFacil"],
						'Pago W.Union' => $paymentDescription["westernUnion"],
						'Pago Transf. Arg' => $paymentDescription["cbuArg"],
						'Pago Transf. Ext' => $paymentDescription["cbuExt"],
						'Pago Paypal' => $paymentDescription["paypal"]
				) );
			}
		}
	}
	
	/* EXCEL */
	public function getDetailedExcelFile($sheetName = '') {
		$this->_isExport = true;
		$this->_prepareGrid ();
		
		$parser = new Varien_Convert_Parser_Xml_Excel ();
		$io = new Varien_Io_File ();
		
		$path = Mage::getBaseDir ( 'var' ) . DS . 'export' . DS;
		$name = md5 ( microtime () );
		$file = $path . DS . $name . '.xml';
		
		$io->setAllowCreateFolders ( true );
		$io->open ( array (
				'path' => $path 
		) );
		$io->streamOpen ( $file, 'w+' );
		$io->streamLock ( true );
		$io->streamWrite ( $parser->getHeaderXml ( $sheetName ) );
		$io->streamWrite ( $parser->getRowXml ( array (
				'Nº Factura Magento',
				'Fecha Factura Magento',
				'Nº Pedido',
				'Fecha Pedido',
				'Cliente',
				'Domicilio',
				'Localidad',
				'Provincia',
				'Pais',
				'Estado',
				'Importe',
				'Moneda',
				'Cotización Moneda en $AR',
				'Total $AR',
				'Nº Factura AFIP',
				'Tipo Factura',
				'F. Autorización',
				'Nº CAE',
				'Vencimiento CAE',
				'Estado AFIP',
				'Observaciones AFIP',
				'Neto 2,5%',
				'IVA 2,5%',
				'Neto 5%',
				'IVA 5%',
				'Neto 10,5%',
				'IVA 10,5%',
				'Neto 21%',
				'IVA 21%',
				'Neto 27%',
				'IVA 27%',
				'Neto Exento',
				'Subtotal',
				'Total IVA',
				'Total',
				'Forma Pago',
				'Cuotas',
				'Pago Tarjeta',
				'Pago DA',
				'F. Presentacion',
				'Pago Tarj. Debito',
				'Pago Efect.',
				'Pago P.Facil',
				'Pago W.Union',
				'Pago Transf. Arg',
				'Pago Transf. Ext',
				'Pago PayPal'
		) ) );
		
		$this->_exportIterateCollectionDetailedExcel ( '_exportExcelItem', array (
				$io,
				$parser 
		) );
		
		if ($this->getCountTotals ()) {
			$io->streamWrite ( $parser->getRowXml ( $this->_getExportTotals () ) );
		}
		
		$io->streamWrite ( $parser->getFooterXml () );
		$io->streamUnlock ();
		$io->streamClose ();
		
		return array (
				'type' => 'filename',
				'value' => $file,
				'rm' => true  // can delete file after use
				);
	}
	public function _exportIterateCollectionDetailedExcel($callback, $args) {
		
		$io = $args [0];
		$parser = $args [1];
		
		$originalCollection = $this->getCollection ();
		$count = null;
		$page = 1;
		$lPage = null;
		$break = false;
		$ourLastPage = 50; // MAX 50 PAGES OF SIZE defined in $this->_exportPageSize
			
		$collection_to_find = clone $originalCollection;
			
		$collection_to_find->getSelect()->join(
				array('sfop' => 'sales_flat_order_payment'),
				'main_table.order_id = sfop.parent_id',
				array('method', 'cc_type','additional_data','amount_ordered','check_cash_amount','check_debit_card_amount','check_credit_card_amount')
		);
		
		$collection_to_find->getSelect()->join(
				array('sfi' => 'sales_flat_invoice'),
				'main_table.entity_id = sfi.entity_id',
				array())->join(
				array('sfoa' => 'sales_flat_order_address'),
				'sfi.billing_address_id = sfoa.entity_id',
				array('street', 'city','region','country_id')
		);
			
		$collection_to_find->getSelect()->joinLeft(
				array('ddr' => 'directdebit_record'), 'main_table.entity_id = ddr.invoice', array('presentation_date'));
		
		while ( $break !== true ) {
			unset($collection);
			$collection = clone $collection_to_find;
			
			$collection->setPageSize ( 500 );
			$collection->setCurPage ( $page );
			$collection->load ();
			if (is_null ( $count )) {
				$count = $collection->getSize ();
				$lPage = $collection->getLastPageNumber ();
			}
			if ($lPage == $page || $ourLastPage == $page) {
				$break = true;
			}
			$page ++;
			foreach ( $collection as $invoice ) {
				
				unset($paymentDescription);
				$paymentDescription = $this->getPaymentDescription($invoice);
				
				unset($streetArray);
				unset($street);
				$streets = explode("\n", $invoice->getStreet());
				$street = $streets[0];
				
				if(!is_null($invoice->getType())){
					$invoiceType = TypeEnum::getLetterForBillingTypeKey($invoice->getType());
					$afipStatus = Afip_Model_Invoice::getStatusName($invoice->getStatus());
				}else{
					$invoiceType = "";
					$afipStatus = "";
				}
				
				$io->streamWrite ( $parser->getRowXml ( array (
						'Nº Factura Magento' => $invoice->getIncrementId (),
						'Fecha Factura Magento' => $invoice->getCreatedAt (),
						'Nº Pedido' => $invoice->getOrderIncrementId (),
						'Fecha Pedido' => $invoice->getOrderCreatedAt (),
						'Cliente' => $invoice->getBillingName (),
						'Domicilio' => $street,
						'Localidad' => $invoice->getCity(),
						'Provincia' => $invoice->getRegion(),
						'Pais' => Mage::app()->getLocale()->getCountryTranslation($invoice->getCountryId()),
						'Estado' => $invoice->getStateName (),
						'Importe' => round($invoice->getGrandTotal(),2),
						'Moneda' => $invoice->getOrder ()->getOrderCurrencyCode (),
						'Cotización Moneda $AR' => $invoice->getCotizacionMonedaExtranjeraPesos (),
						'Total $AR' => $invoice->getTotal (),
						'Nº Factura AFIP' => $invoice->getNumber (),
						'Tipo Factura' => $invoiceType,
						'F. Autorización' => $invoice->getAuthorizationDate (),
						'Nº CAE' => $invoice->getCaeNumber (),
						'Vencimiento CAE' => $invoice->getCaeDueDate (),
						'Estado AFIP' => $afipStatus,
						'Observaciones AFIP' => $invoice->getObservations (),
						'Neto 2,5%' => $invoice->getNeto_0250 (),
						'IVA 2,5%' => $invoice->getIva_0250 (),
						'Neto 5%' => $invoice->getNeto_0500 (),
						'IVA 5%' => $invoice->getIva_0500 (),
						'Neto 10,5%' => $invoice->getNeto_1050 (),
						'IVA 10,5%' => $invoice->getIva_1050 (),
						'Neto 21%' => $invoice->getNeto_2100 (),
						'IVA 21%' => $invoice->getIva_2100 (),
						'Neto 27%' => $invoice->getNeto_2700 (),
						'IVA 27%' => $invoice->getIva_2700 (),
						'Neto Exento' => $invoice->getNetoExento (),
						'Subtotal' => $invoice->getSubtotal (),
						'Total IVA' => $invoice->getTotalIva (),
						'Total' => $invoice->getTotal (),
						'Forma Pago' => $paymentDescription['name'],
						'Cuotas' => $paymentDescription["quotas"],
						'Pago Tarjeta' => $paymentDescription["creditCard"],
						'Pago DA' => $paymentDescription["debit"],
						'F. Presentacion' => $invoice->getPresentationDate(),
						'Pago Tarj. Debito' => $paymentDescription["debitCard"],
						'Pago Efect.' => $paymentDescription["cash"],
						'Pago P.Facil' => $paymentDescription["pagoFacil"],
						'Pago W.Union' => $paymentDescription["westernUnion"],
						'Pago Transf. Arg' => $paymentDescription["cbuArg"],
						'Pago Transf. Ext' => $paymentDescription["cbuExt"],
						'Pago Paypal' => $paymentDescription["paypal"],
				) ) );
			}
		}
	}

/**
 * *******END CUSTOM EXPORTS**********
 */
	public function getPaymentDescription($invoice){
		$paymentDescription = array();		

		//name
		$method = $invoice->getMethod();
		if($method == 'telephonepaymentmethod' || $method == 'qbnpsgateway' || $method == 'directdebitpaymentmethod'){
			$paymentNames = array("telephonepaymentmethod", "qbnpsgateway", "directdebitpaymentmethod");
			$reportPaymentNames = array("C.Telefónica", "P.Online", "DA");
			$paymentDescription['name'] = $this->getCcName($invoice->getCcType()) . " (" . str_replace($paymentNames, $reportPaymentNames, $method) . ")";
		}else{
			$paymentNames = array("simplepaymentmethod_cbu", "simplepaymentmethod_western_union", "simplepaymentmethod_store_billing", "pagofacil", "free", "paypal_standard", "simplepaymentmethod_paypal_ru");
			$reportPaymentNames = array("Transferencia Bancaria", "Western Union", "Facturación en Tienda", "Pago Fácil", "Bonificado", "PayPal","PayPal");
			$paymentDescription['name'] = str_replace($paymentNames, $reportPaymentNames, $method);
		}
		
		//quotas
		$paymentDescription["quotas"] = '';
		$unserializedAditionalData = unserialize($invoice->getAdditionalData());
		if(isset($unserializedAditionalData) && !is_null($unserializedAditionalData) && is_array($unserializedAditionalData) && array_key_exists("numPayments",$unserializedAditionalData)){
			$paymentDescription["quotas"] = $unserializedAditionalData["numPayments"];
		}
		
		
		//init totals
		$paymentDescription["creditCard"] = "0.0000";
		$paymentDescription["debitCard"] = "0.0000";
		$paymentDescription["cash"] = "0.0000";
		$paymentDescription["debit"] = "0.0000";
		$paymentDescription["pagoFacil"] = "0.0000";
		$paymentDescription["westernUnion"] = "0.0000";
		$paymentDescription["cbuArg"] = "0.0000";
		$paymentDescription["cbuExt"] = "0.0000";
		$paymentDescription["paypal"] = "0.0000";
		
		
		if($method == 'telephonepaymentmethod' || $method == 'qbnpsgateway'){
			$paymentDescription["creditCard"] = $invoice->getGrandTotal();
		}else if($method == 'directdebitpaymentmethod'){
			$paymentDescription["debit"] = $invoice->getGrandTotal();
		}else if($method == 'simplepaymentmethod_store_billing'){
			if(!is_null($invoice->getCheckCreditCardAmount())){
				$paymentDescription["creditCard"] = $invoice->getCheckCreditCardAmount();
			}
			if(!is_null($invoice->getCheckDebitCardAmount())){
				$paymentDescription["debitCard"] = $invoice->getCheckDebitCardAmount();
			}
			if(!is_null($invoice->getCheckCashAmount())){
				$paymentDescription["cash"] = $invoice->getCheckCashAmount();
			}
		}else if($method == 'pagofacil'){
			$paymentDescription["pagoFacil"] = $invoice->getGrandTotal();
		}else if($method == 'simplepaymentmethod_western_union'){
			$paymentDescription["westernUnion"] = $invoice->getGrandTotal();
		}else if($method == 'simplepaymentmethod_cbu'){
			if($invoice->getOrder()->getStoreId() == 1){
				$paymentDescription["cbuArg"] = $invoice->getGrandTotal();
			}else{
				$paymentDescription["cbuExt"] = $invoice->getGrandTotal();
			}
		}else if($method == 'paypal_standard'){
			$paymentDescription["paypal"] = $invoice->getGrandTotal();
		}else if($method == 'simplepaymentmethod_paypal_ru'){
			$paymentDescription["paypal"] = $invoice->getGrandTotal();
		}
		
		return $paymentDescription;
	}
	
	protected function getCcName($ccType){
		switch ($ccType) {
			case 'AE':
				return "American Express";
			case 'CB':
				return "Cabal";
			case 'MC':
				return "Mastercard";
			case 'NR':
				return "Naranja";
			case 'VI':
				return "Visa";
			default:
				return $ccType;
		}
	}
	
}
