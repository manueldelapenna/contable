<?php
	
	/**
	 * Billing target for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	abstract class Quanbit_Afip_Model_BillingTarget_BillingTarget
	{
		/* Constants and Variables */
		
		/**
		 * The errors.
		 * @var Quanbit_Afip_Helper_ErrorCollection instance
		 */
		protected $errors;
		
		/**
		 * The billing number.
		 * @var int | NULL
		 */
		protected $number;
		
		/**
		 * The billing type.
		 * @var int | NULL
		 */
		protected $type;
		
		
		
		/* Public methods */
		
		/**
		 * Transfors the inner information for AFIP Webservice (WSFE).
		 *
		 * @param string $pointOfSale
		 * @return array
		 */
		public function asWebserviceInput($pointOfSale)
		{
			$input = array("Tipo" => $this->getType(), "PtoVta" => $pointOfSale, "Nro" => $this->getNumber());
			return $input;
		}
		
		/**
		 * Returns the errors.
		 *
		 * @return Quanbit_Afip_Helper_ErrorCollection instance
		 */
		public function getErrors()
		{
			return $this->errors;
		}
		
		/**
		 * Returns the billing number.
		 *
		 * @return int
		 */
		public function getNumber()
		{
			return $this->number;
		}
		
		/**
		 * Returns the billing type.
		 *
		 * @return int
		 */
		public function getType()
		{
			return $this->type;
		}
		
		/**
		 * Indicates whether the stored information is valid.
		 *
		 * @return void
		 */
		public function isValid()
		{
			$this->errors->clean();
			$this->validate();
			
			return $this->errors->isEmpty();
		}
		
		/**
		 * Sets the billing number.
		 *
		 * @param int $number
		 * @return void
		 */
		public function setNumber($number)
		{
			$this->number = $number;
		}
		
		/**
		 * Sets the billing type.
		 *
		 * @param int $type
		 * @return void
		 */
		public function setType($type)
		{
			$this->type = $type;
		}
		
		
		
		/* Protected methods */
		
		/**
		 * Returns the name of billing target, readable for people.
		 */
		abstract protected function getNameForPeople();
		
		/**
		 * Returns the target types that are valid.
		 *
		 * @return array.
		 */
		abstract protected function getValidTargetTypes();
		
		/**
		 * Initializes the instance.
		 *
		 * @return void
		 */
		protected function initialize()
		{
			$this->errors = Quanbit_Afip_Helper_ErrorCollection::getInstance();
			$this->setNumber(NULL);
			$this->setType(NULL);
		}
		
		/**
		 * Validates the stored data.
		 *
		 * @return void
		 */
		protected function validate()
		{
			if (($this->getNumber() <= 0) || ($this->getNumber() > 99999999.0))
				$this->errors->add("The billing target number of {$this->getNameForPeople()} must be an integer between 1 and 99999999, and this number was detected: {$this->getNumber()}.");
			
			if (!in_array($this->getType(), $this->getValidTargetTypes()))
				$this->errors->add("The billing target type of {$this->getNameForPeople()} is invalid, this type was detected: {$this->getType()}.");
		}
	}
	
?>