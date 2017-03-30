<?php
	
	/**
	 * Helper for production environment of AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	final class Quanbit_Afip_Model_Environment_ProductionEnvironment extends Quanbit_Afip_Model_Environment_Environment
	{
		/**
		 * Returns a new instance.
		 *
		 * @return Quanbit_Afip_Model_Environment_ProductionEnvironment instance
		 */
		public static function getInstance()
		{
			return new self();
		}
		
		public function __construct()
		{
			parent::__construct();
		}
		
		
		public function getAfipCertificate()
		{
			return "{$this->getCertificatesDirectoryPath()}/AFIPcerthomo.crt";
		}
		
		public function getConsumerCertificate()
		{
			return "{$this->getCertificatesDirectoryPath()}/usershop_prod.crt";
		}
		
		public function getConsumerPrivateKey()
		{
			return "{$this->getCertificatesDirectoryPath()}/private_prod.key";
		}
		
		public function getEnvironmentKey()
		{
			return "production";
		}
		
		public function getPointOfSale()
		{
			return "0006";
		}
		
		public function getTaxpayerCuit()
		{
			return (float) "33709315329";
		}
		
		public function getWsaaCommonName()
		{
			return "wsaa.afip.gov.ar";
		}
		
		public function getWsaaUrl()
		{
			return "https://wsaa.afip.gov.ar/ws/services/LoginCms";
		}
		
		public function getWsfeUrl()
		{
			return "https://servicios1.afip.gov.ar/wsfev1/service.asmx";
		}
	}
	
?>