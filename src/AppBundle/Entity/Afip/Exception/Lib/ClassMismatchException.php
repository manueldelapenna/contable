<?php
	
	/**
	 * Exception class for Class missmatch.
	 * 
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Exception_Lib_ClassMismatchException extends Quanbit_Afip_Exception_Lib_Exception
	{
		protected $className;
		
		
		
		/**
		 * Constructor
		 * 
		 * @param string $className
		 * @param Exception $previous [Default: NULL]
		 */
		public function __construct($className, Exception $previous = NULL)
		{
			$this->setClassName($className);
			
			parent::__construct("Expected an instance of: <{$this->getClassName()}>.", 99003, $previous);
		}
		
		/**
		 * Sets the class name.
		 * 
		 * @param string $className
		 * @return void
		 */
		public function setClassName($className = "UNDEFINED")
		{
			$this->className = $className;
		}
		
		/**
		 * Returns the class name.
		 * 
		 * @return string
		 */
		public function getClassName()
		{
			return $this->className;
		}
	}
	
?>