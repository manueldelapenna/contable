<?php
	
	/**
	 * Helper for alicuotas of 10.50% for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Model_Alicuota_Alicuota1050 extends Quanbit_Afip_Model_Alicuota_Alicuota
	{
		/* Public methods */
		
		/**
		 * Returns a new instance.
		 *
		 * @return Quanbit_Afip_Model_Alicuota_Alicuota1050 instance
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
			return Quanbit_Afip_Model_Enums_TaxTypeEnum::IVA_1050;
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