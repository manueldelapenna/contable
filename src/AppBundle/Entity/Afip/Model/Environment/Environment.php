<?php
	
	/**
	 * Abstract helper for environment of AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	abstract class Afip_Model_Environment_Environment
	{
		/* Constants and Variables */
		
		/**
		 * The base directory path.
		 * @var string
		 */
		protected $basePath;
		
		
		
		/* Public methods */
		
		public function __construct()
		{
			$this->basePath = Mage::getBaseDir('code') . "/local/Quanbit/Afip";
		}
		
		public function __toString()
		{
			return $this->getEnvironmentKey();
		}
		
		
		/**
		 * Returns the file path of access ticket.
		 *
		 * @return string
		 */
		public function getAccessTicketFilePath()
		{
			return $this->getTempPathFor("accessTicket.xml");
		}
		
		/**
		 * Returns the AFIP certificate.
		 *
		 * @return string
		 */
		abstract public function getAfipCertificate();
		
		/**
		 * Returns the consumer certificate.
		 *
		 * @return string
		 */
		abstract public function getConsumerCertificate();
		
		/**
		 * Returns the consumer CUIT.
		 *
		 * @deprecated 2013/07/25
		 * @see getTaxpayerCuit()
		 * @return float
		 */
		public function getConsumerCuit()
		{
			return $this->getTaxpayerCuit();
		}
		
		/**
		 * Returns the consumer private key for its certificate.
		 *
		 * @return string
		 */
		abstract public function getConsumerPrivateKey();
		
		/**
		 * Returns the environment key.
		 *
		 * @return string
		 */
		abstract public function getEnvironmentKey();
		
		/**
		 * Returns the point of sale.
		 *
		 * @return string
		 */
		abstract public function getPointOfSale();
		
		/**
		 * Returns the taxpayer CUIT.
		 *
		 * @return float
		 */
		abstract public function getTaxpayerCuit();
		
		/**
		 * Returns the path of given filename in temp folder.
		 *
		 * @param string $filename
		 * @return string
		 */
		public function getTempPathFor($filename)
		{
			$filename = trim(basename((string) $filename));
			if (($filename == ""))
				Afip_Exception_ExceptionFactory::throwFor(__CLASS__ . ":: Invalid given filename for method: <" . __FUNCTION__ . ">.");
			
			return "{$this->getTempDirectoryPath()}/{$this->getEnvironmentKey()}__$filename";
		}
		
		/**
		 * Returns the name of accessing WSAA.
		 *
		 * @return string
		 */
		abstract public function getWsaaCommonName();
		
		/**
		 * Returns the path of local WSAA file.
		 *
		 * @return string
		 */
		public function getWsaaFilePath()
		{
			return "{$this->getWebserviceDirectoryPath()}/wsaa.wsdl";
		}
		
		/**
		 * Returns the URL to access to WSAA.
		 *
		 * @return string
		 */
		abstract public function getWsaaUrl();
		
		/**
		 * Returns the path of local WSFE file.
		 *
		 * @return string
		 */
		public function getWsfeFilePath()
		{
			return "{$this->getWebserviceDirectoryPath()}/wsfe.wsdl";
		}
		
		/**
		 * Returns the URL to access to WSFE.
		 *
		 * @return string
		 */
		abstract public function getWsfeUrl();
		
		
		
		/* Proteced methods */
		
		/**
		 * Returns the path of certificates directory.
		 *
		 * @return string
		 */
		protected function getCertificatesDirectoryPath()
		{
			return "{$this->basePath}/etc/certificates/{$this->getEnvironmentKey()}";
		}
		
		/**
		 * Returns the path of temp directory.
		 *
		 * @return string
		 */
		protected function getTempDirectoryPath()
		{
			return "{$this->basePath}/etc/temp";
		}
		
		/**
		 * Returns the path of webservice directory.
		 *
		 * @return string
		 */
		protected function getWebserviceDirectoryPath()
		{
			return "{$this->basePath}/etc/webservice";
		}
	}
	
?>