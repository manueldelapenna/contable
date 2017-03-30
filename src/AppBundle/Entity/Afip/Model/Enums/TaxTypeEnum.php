<?php
	
	/**
	 * Enumerative for ax types for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class TaxTypeEnum extends AbstractEnum
	{
		/* Contants and Variables */
		
		/**
		 * IVA 0.00%
		 * @var int
		 */
		const IVA_0000 = 3;
		
		/**
		 * IVA 10.50%
		 * @var int
		 */
		const IVA_1050 = 4;
		
		/**
		 * IVA 21.00%
		 * @var int
		 */
		const IVA_2100 = 5;
		
		/**
		 * IVA 27.00%
		 * @var int
		 */
		const IVA_2700 = 6;
		
		/**
		 * IVA 5.00%
		 * @var int
		 */
		const IVA_0500 = 8;
		
		
		/**
		 * IVA 2.50%
		 * @var int
		 */
		const IVA_0250 = 9;
		
		/**
		 * A singleton instance.
		 * @var TaxTypeEnum
		 */
		protected static $singleton;
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new singleton instance.
		 *
		 * @return TaxTypeEnum instance
		 */
		public static function getInstance()
		{
			if (!self::$singleton)
				self::$singleton = new self();
			
			return self::$singleton;
		}
		
		
		public function getDefaultKey()
		{
			return self::IVA_2100;
		}
		
		public function getList()
		{
			return array(self::IVA_0000 => "0.00%", self::IVA_0250 => "2.50%", self::IVA_0500 => "5.00%", self::IVA_1050 => "10.50%", self::IVA_2100 => "21.00%", self::IVA_2700 => "27.00%");
		}



		/* Protected methods */
		
		protected function getNameForPeople()
		{
			return "tax type";
		}
	}
	
?>