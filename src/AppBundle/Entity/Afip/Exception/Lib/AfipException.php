<?php
/**
	 * Base exception class.
	 * 
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
class AfipException extends \Exception{
	/**
	 * Construnctor
	 *
	 * @param string $message
	 *        	[Default: ""]
	 * @param int $code
	 *        	[Defualt: 99000]
	 * @param Exception $previous
	 *        	[Default: NULL]
	 */
	public function __construct($message = "", $code = 99000, Exception $previous = NULL) {
		$message = trim ( $message );
		if (($message == NULL) || ($message == ""))
			$message = "Unknown Exception";
		
		parent::__construct ( $message, $code );
		
		$this->addMessage ( new Mage_Core_Model_Message_Error ( $code ) );
	}
	public function __toString() {
		return (($this->getCode () == 0) ? "" : "[{$this->getCode()}] ") . "{$this->getMessage()} ({$this->getFile()}:{$this->getLine()})";
	}
}

?>