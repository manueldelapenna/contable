<?php
	
	/**
	 * Enumerative for invoice concept types for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Model_Enums_ConceptEnum extends Quanbit_Afip_Model_Enums_AbstractEnum
	{
		/* Contants and Variables */
		
		/**
		 * Products.
		 * @var int
		 */
		const PRODUCT = 1;
		
		/**
		 * Services.
		 * @var int
		 */
		const SERVICE = 2;
		
		/**
		 * Products and Services.
		 * @var int
		 */
		const PRODUCT_AND_SERVICE = 3;
		
		/**
		 * A singleton instance.
		 * @var Quanbit_Afip_Model_Enums_ConceptEnum
		 */
		protected static $singleton;
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new singleton instance.
		 *
		 * @return Quanbit_Afip_Model_Enums_ConceptEnum instance
		 */
			public static function getInstance()
		{
			if (!self::$singleton)
				self::$singleton = new self();
			
			return self::$singleton;
		}
		
		
		public function getDefaultKey()
		{
			return self::PRODUCT;
		}
		
		public function getList()
		{
			return array(self::PRODUCT => "Product", self::SERVICE => "Service", self::PRODUCT_AND_SERVICE => "Product and Service");
		}



		/* Protected methods */
		
		protected function getNameForPeople()
		{
			return "invoice concept";
		}
	}
	
?>