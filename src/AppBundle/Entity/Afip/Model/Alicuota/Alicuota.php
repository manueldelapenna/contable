<?php
	
	/**
	 * Helper for alicuotas for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	abstract class Afip_Model_Alicuota_Alicuota
	{
		/* Constants and Variables */
		
		/**
		 * The base amount.
		 * @var float
		 */
		protected $baseAmount;
		
		/**
		 * The errors.
		 * @var Afip_Helper_ErrorCollection instance
		 */
		protected $errors;
		
		/**
		 * The tax amount.
		 * @var float
		 */
		protected $taxAmount;
		
		
		
		/* Public methods */
		
		/**
		 * Transfors the inner information for AFIP Webservice (WSFE).
		 *
		 * @return array
		 */
		public function asWebserviceInput()
		{
			$input = array("Id" => $this->getTaxType(), "BaseImp" => $this->getBaseAmount(), "Importe" => $this->getTaxAmount());
			return $input;
		}
		
		/**
		 * Returns the base amount.
		 *
		 * @return float
		 */
		public function getBaseAmount()
		{
			return $this->baseAmount;
		}
		
		/**
		 * Returns the errors.
		 *
		 * @return Afip_Helper_ErrorCollection instance
		 */
		public function getErrors()
		{
			return $this->errors;
		}
		
		/**
		 * Returns the tax amount.
		 *
		 * @return float
		 */
		public function getTaxAmount()
		{
			return $this->taxAmount;
		}
		
		/**
		 * Returns the tax percent.
		 *
		 * @return float
		 */
		abstract public function getTaxPercent();
		
		/**
		 * Returns the tax type (Afip_Model_Enums_TaxTypeEnum)
		 *
		 * @return int
		 */
		abstract public function getTaxType();
		
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
		 * Sets the base amount.
		 *
		 * @param float $amount
		 * @return void
		 */
		public function setBaseAmount($amount)
		{
			$this->baseAmount = Afip_Helper_DataType_Number::truncate(floatval($amount), 2);
		}
		
		/**
		 * Sets the tax amount.
		 *
		 * @param float $amount
		 * @return void
		 */
		public function setTaxAmount($amount)
		{
			$this->taxAmount = Afip_Helper_DataType_Number::truncate(floatval($amount), 2);
		}
		
		
		
		/* Protected methods */
		
		/**
		 * Returns its name.
		 *
		 * @return string
		 */
		protected function getName()
		{
			return Afip_Model_Enums_TaxTypeEnum::getInstance()->getValueFor($this->getTaxType());
		}
		
		protected function initialize()
		{
			$this->setBaseAmount(0);
			$this->setTaxAmount(0);
			
			$this->errors = Afip_Helper_ErrorCollection::getInstance();
		}
		
		/**
		 * Validates the stored data.
		 *
		 * @return void
		 */
		protected function validate()
		{
			if ($this->getBaseAmount() <= 0)
				$this->errors->add("The base amount of alicuota must be greater than 0.");
// 			else
// 			{
// 				$relativeError = (($this->getTaxAmount() / $this->getBaseAmount() * 100) - $this->getTaxPercent());
// 				$relativeError = $relativeError / $this->getTaxPercent();
// 				$relativeError = Afip_Helper_DataType_Number::truncate($relativeError, 2);
				
// 				if (abs($relativeError) > 0.01)
// 					$this->errors->add("The base and tax amount do not match with the alicuota ({$this->getName()}). Diference: $relativeError");
// 			}
		}
	}
	
?>