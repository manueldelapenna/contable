<?php
		
	/**
	 * Factory class for exceptions.
	 * 
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	abstract class Quanbit_Afip_Exception_ExceptionFactory
	{
		/**
		 * Throws a class mismatch exception.
		 * 
		 * @param string $className The expected class.
		 * @param Exception $previous [Default: NULL]
		 * @throws Quanbit_Afip_Exception_Lib_ClassMismatchException
		 */
		public static function throwClassMismatch($className, Exception $previous = NULL)
		{
			throw new Quanbit_Afip_Exception_Lib_ClassMismatchException($className, $previous);
		}
		
		/**
		 * Throws an exception.
		 * 
		 * @param string $message [Default: ""]
		 * @param int $code [Default: 99000]
		 * @param Exception $previous [Default: NULL]
		 * @return void
		 * @throws BaseException
		 */
		public static function throwFor($message = "", $code = 99000, Exception $previous = NULL)
		{
			throw new Quanbit_Afip_Exception_Lib_Exception($message, $code, $previous);
		}
		
		/**
		 * Throws a undefined class exception.
		 * 
		 * @param string $className
		 * @param Exception $previous [Default: NULL]
		 * @throws Quanbit_Afip_Exception_Lib_UndefinedClassException
		 */
		public static function throwUndefinedClass($className, Exception $previous = NULL)
		{
			throw new Quanbit_Afip_Exception_Lib_UndefinedClassException($className, $previous);
		}
		
		/**
		 * Throws a SOAP fault exception.
		 * 
		 * @param object $soapFault
		 * @param Exception $previous [Default: NULL]
		 * @throws Quanbit_Afip_Exception_Lib_SoapFaultException
		 */
		public static function throwSoapFaultException($soapFault)
		{
			throw new Quanbit_Afip_Exception_Lib_SoapFaultException($soapFault);
		}
		
		/**
		 * Throws a subclass responsibility exception.
		 * 
		 * @param string $methodName
		 * @param Exception $previous [Default: NULL]
		 * @throws SubclassResponsibilityException
		 */
		public static function throwSubclassResponsibility($methodName, Exception $previous = NULL)
		{
			throw new Quanbit_Afip_Exception_Lib_SubclassResponsibilityException($methodName, $previous);
		}
		
	}
	
?>