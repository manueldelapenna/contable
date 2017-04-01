<?php

namespace AfipBundle\Entity\Exception\Lib;
/**
	 * Base exception class.
	 * 
	 
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
		
	}
	public function __toString() {
		return (($this->getCode () == 0) ? "" : "[{$this->getCode()}] ") . "{$this->getMessage()} ({$this->getFile()}:{$this->getLine()})";
	}
}

?>