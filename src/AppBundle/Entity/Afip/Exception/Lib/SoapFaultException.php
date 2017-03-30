<?php
	
	/**
	 * Exception class for Soap Faults.
	 * 
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Exception_Lib_SoapFaultException extends Quanbit_Afip_Exception_Lib_Exception
	{
		protected $soapFault;
		
		
		
		/**
		 * Constructor
		 * 
		 * @param object $soapFault
		 * @param Exception $previous [Default: NULL]
		 */
		public function __construct($soapFault, Exception $previous = NULL)
		{
			$this->setSoapFault($soapFault);
			
			parent::__construct("There is a SOAP fault: {$this->soapFault->faultcode} {$this->soapFault->faultstring}", 99004, $previous);
		}
		
		/**
		 * Sets the SOAP fault.
		 * 
		 * @param object $soapFault
		 * @return void
		 */
		public function setSoapFault($soapFault)
		{
			$this->soapFault = $soapFault;
		}
		
		/**
		 * Returns the SOAP fault.
		 * 
		 * @return object
		 */
		public function getSoapFault()
		{
			return $this->soapFault;
		}
	}
	
?>