<?php
	
	/**
	 * Helper for alicuotas of 0.00% for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Model_Alicuota_Alicuota0000 extends Quanbit_Afip_Model_Alicuota_Alicuota
	{
		/* Public methods */
		
		/**
		 * Returns a new instance.
		 *
		 * @return Quanbit_Afip_Model_Alicuota_Alicuota0000 instance
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
			return 0;
		}
		
		public function getTaxType()
		{
			return Quanbit_Afip_Model_Enums_TaxTypeEnum::IVA_0000;
		}
		
		
		
		/* Protected methods */
		
		protected function validate()
		{
			if ($this->taxAmount != 0)
				$this->errors->add("The tax amount of alicuota (0.00%) must be equals to 0.");
			
			parent::validate();
		}
	}
	
?>