<?php
	
	/**
	 * Helper for alicuotas of 2.50% for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author manueldelapenna
	 */
	class Quanbit_Afip_Model_Alicuota_Alicuota0250 extends Quanbit_Afip_Model_Alicuota_Alicuota
	{
		/* Public methods */
		
		/**
		 * Returns a new instance.
		 *
		 * @return Quanbit_Afip_Model_Alicuota_Alicuota0250 instance
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
			return 2.5;
		}
		
		public function getTaxType()
		{
			return Quanbit_Afip_Model_Enums_TaxTypeEnum::IVA_0250;
		}
		
		
		
		/* Protected methods */
		
		protected function validate()
		{
			if ($this->taxAmount <= 0)
				$this->errors->add("The tax amount of alicuota (2.50%) must be greater than 0.");
			
			parent::validate();
		}
	}
	
?>