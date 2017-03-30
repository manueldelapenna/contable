<?php
class Quanbit_Afip_GeneralController extends Mage_Core_Controller_Front_Action
{
	public function pruebaAction(){

		Quanbit_Afip_Model_SchedulingExecutor::execute();
		Quanbit_Afip_Model_Pdf_InvoicePrinterExecutor::execute();
	
	}
	
	public function verEstadoAfipAction(){
	
		if(Mage::getStoreConfig('afip/config/enable_prod')) {
			$environment = Quanbit_Afip_Model_Environment_ProductionEnvironment::getInstance();
		} else {
			$environment = Quanbit_Afip_Model_Environment_StagingEnvironment::getInstance();
			
		}

		$logger = Quanbit_Afip_Helper_FileLogger::getInstance(NULL, Mage::getBaseDir('var') . '/log/afip');
		$invoiceManager = Quanbit_Afip_Model_InvoiceManager::getInstance($environment, $logger);
				
		$reporte = $invoiceManager->getStatusReport();
		
		echo "<h1>Estado AFIP</h1>";
		
		echo "<h2>Ambiente: " . 	$reporte['environment'] ."</h2>";
		echo "<h2>Pto. Venta: " . 	$reporte['pointOfSales'] ."</h2>";
		echo "<h2>CUIT: " . $reporte['taxpayerCuit'] ."</h2>";
		echo "<h2>L&iacute;mite de env&iacute;o: " . $reporte['sendingDataLimit'] ."</h2>";
		
		echo "<br>";
		
		echo "<h2>Factura A</h2>";
		
		echo "<h3>&Uacute;ltimo N&uacute;mero de AFIP: " . $reporte['lastNumber']['invoiceA'] ."</h3>";
		echo "<h3>&Uacute;ltimo N&uacute;mero de RU: " . Quanbit_Afip_Model_Invoice::getLastNumber(Quanbit_Afip_Model_Enums_TypeEnum::A) ."</h3>";
		
		echo "<br>";
		
		echo "<h2>Factura B</h2>";
		
		echo "<h3>&Uacute;ltimo N&uacute;mero de AFIP: " . $reporte['lastNumber']['invoiceB'] ."</h3>";
		echo "<h3>&Uacute;ltimo N&uacute;mero de RU: " . Quanbit_Afip_Model_Invoice::getLastNumber(Quanbit_Afip_Model_Enums_TypeEnum::B) ."</h3>";
				
		die;
	
	}
}