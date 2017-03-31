<?php
	
	/**
	 * Helper for alicuotas of 27.00% for AFIP Invoice Manager.
	 *
	 
	 * @author Eduardo Casey
	 */
	class Alicuota2700 extends Alicuota
	{
		/* Public methods */
		
		/**
		 * Returns a new instance.
		 *
		 * @return QbAfipAlicuota2700Helper instance
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
			return 27.00;
		}
		
		public function getTaxType()
		{
			return TaxTypeEnum::IVA_2700;
		}
		
		
		
		/* Protected methods */
		
		protected function validate()
		{
			if ($this->taxAmount <= 0)
				$this->errors->add("The tax amount of alicuota (27.00%) must be greater than 0.");
			
			parent::validate();
		}
	}
	
?>