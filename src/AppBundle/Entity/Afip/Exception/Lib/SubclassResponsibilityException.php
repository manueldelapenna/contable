<?php
	
	/**
	 * Exception class for Subclass Responsbility.
	 * 
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Exception_Lib_SubclassResponsibilityException extends Quanbit_Afip_Exception_Lib_Exception
	{
		protected $methodName;
		
		
		/**
		 * Constructor
		 * 
		 * @param string $methodName
		 * @param Exception $previous [Default: NULL]
		 */
		public function __construct($methodName, Exception $previous = NULL)
		{
			$this->setMethodName($methodName);
			
			parent::__construct("Subclass should be re-implement the method: <{$this->getMethodName()}>.", 99001, $previous);
		}
		
		/**
		 * Sets the method name.
		 * 
		 * @param string $methodName
		 * @return void
		 */
		public function setMethodName($methodName = "UNDEFINED")
		{
			$this->methodName = $methodName;
		}
		
		/**
		 * Returns the method name.
		 * 
		 * @return string
		 */
		public function getMethodName()
		{
			return $this->methodName;
		}
	}
	
?>