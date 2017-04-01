<?php

namespace AfipBundle\Entity\Exception\Lib;

/**
 * Exception class for Class missmatch.
 * 

 * @author Eduardo Casey
 */
class ClassMismatchException extends AfipException {

    protected $className;

    /**
     * Constructor
     * 
     * @param string $className
     * @param Exception $previous [Default: NULL]
     */
    public function __construct($className, Exception $previous = NULL) {
        $this->setClassName($className);

        parent::__construct("Expected an instance of: <{$this->getClassName()}>.", 99003, $previous);
    }

    /**
     * Sets the class name.
     * 
     * @param string $className
     * @return void
     */
    public function setClassName($className = "UNDEFINED") {
        $this->className = $className;
    }

    /**
     * Returns the class name.
     * 
     * @return string
     */
    public function getClassName() {
        return $this->className;
    }

}

?>