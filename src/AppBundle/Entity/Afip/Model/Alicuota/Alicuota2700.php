<?php
	
	/**
	 * Helper for alicuotas of 27.00% for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Model_Alicuota_Alicuota2700 extends Quanbit_Afip_Model_Alicuota_Alicuota
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
			return Quanbit_Afip_Model_Enums_TaxTypeEnum::IVA_2700;
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