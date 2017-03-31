<?php
	
	/**
	 * Enumerative for document types for AFIP Invoice Manager.
	 *
	 
	 * @author Eduardo Casey
	 */
	class DocumentTypeEnum extends AbstractEnum
	{
		/* Contants and Variables */
		
		/**
		 * A CI document (only for people).
		 * @var int
		 */
		const CI = 0;
		
		/**
		 * A CUIL document (only for people).
		 * @var int
		 */
		const CUIL = 86;
		
		/**
		 * A CUIT document (for enterprises and people).
		 * @var int
		 */
		const CUIT = 80;
		
		/**
		 * A DNI document (only for people).
		 * @var int
		 */
		const DNI = 96;
		
		/**
		 * A LE document (only for people).
		 * @var int
		 */
		const LE = 89;
		
		/**
		 * A passport (only for people).
		 * @var int
		 */
		const PASSPORT = 94;
		
		/**
		 * An undefined document.
		 * @var int
		 */
		const UNKNOWN = 99;
		
		/**
		 * A singleton instance.
		 * @var DocumentTypeEnum
		 */
		protected static $singleton;
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new singleton instance.
		 *
		 * @return DocumentTypeEnum instance
		 */
			public static function getInstance()
		{
			if (!self::$singleton)
				self::$singleton = new self();
			
			return self::$singleton;
		}
		
		
		public function getDefaultKey()
		{
			return self::UNKNOWN;
		}
		
		public function getList()
		{
			return array(self::CI => "CI", self::CUIL => "CUIL", self::CUIT => "CUIT", self::DNI => "DNI", self::LE => "LE", self::PASSPORT => "Passport", self::UNKNOWN => "Unknown");
		}
		
		
		
		/* Protected methods */
		
		protected function getNameForPeople()
		{
			return "document type";
		}
	}
	
?>