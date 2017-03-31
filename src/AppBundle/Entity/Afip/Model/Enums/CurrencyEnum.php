<?php
	
	/**
	 * Enumerative for invoice currency for AFIP Invoice Manager.
	 *
	 
	 * @author Eduardo Casey
	 */
	class CurrencyEnum extends AbstractEnum
	{
		/* Contants and Variables */
		
		/**
		 * United States' currency.
		 * @var string
		 */
		const DOLAR = "DOL";
		
		/**
		 * European Union's currency.
		 * @var string
		 */
		const EURO = "060";
		
		/**
		 * Argentine's currency.
		 * @var string
		 */
		const PESOS = "PES";
		
		/**
		 * A singleton instance.
		 * @var CurrencyEnum
		 */
		protected static $singleton;
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new singleton instance.
		 *
		 * @return CurrencyEnum instance
		 */
			public static function getInstance()
		{
			if (!self::$singleton)
				self::$singleton = new self();
			
			return self::$singleton;
		}
		
		
		public function getDefaultKey()
		{
			return self::PESOS;
		}
		
		public function getList()
		{
			return array(self::DOLAR => "USD", self::EURO => "EUR", self::PESOS => "ARS");
		}



		/* Protected methods */
		
		protected function getNameForPeople()
		{
			return "invoice currency";
		}
	}
	
?>