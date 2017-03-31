<?php
	
	/**
	 * Helper for alicuotas of 10.50% for AFIP Invoice Manager.
	 *
	 
	 * @author Eduardo Casey
	 */
	class Alicuota1050 extends Alicuota
	{
		/* Public methods */
		
		/**
		 * Returns a new instance.
		 *
		 * @return Alicuota1050 instance
		 */
		public static function getInstance()
		{
			return new self();
		}
		
		
		public function __construct()
		{
			$this->initialize();
		}
		
		
		public function getTaxPercent()
		{
			return 10.50;
		}
		
		public function getTaxType()
		{
			return TaxTypeEnum::IVA_1050;
		}
		
		
		
		/* Protected methods */
		
		protected function validate()
		{
			if ($this->taxAmount <= 0)
				$this->errors->add("The tax amount of alicuota (10.50%) must be greater than 0.");
			
			parent::validate();
		}
	}
	
?>