<?php
	
	/**
	 * Exception class for Undefined Class.
	 * 
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class UndefinedClassException extends AfipException
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
			
			parent::__construct("The given class is not defined in PHP: <{$this->getClassName()}>.", 99002, $previous);
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