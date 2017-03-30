<?php
	
	/**
	 * Enumerative for invoice types for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Model_Enums_TypeEnum extends Quanbit_Afip_Model_Enums_AbstractEnum
	{
		/* Contants and Variables */
		
		/**
		 * Invoice "A".
		 * @var int
		 */
		const A = 1;
		
		/**
		 * Credit Note for invoice "A".
		 * @var int
		 */
		const A_CREDIT_NOTE = 3;
		
		/**
		 * DEBIT Note for invoice "A".
		 * @var int
		 */
		const A_DEBIT_NOTE = 2;
		
		/**
		 * Invoice "B".
		 * @var int
		 */
		const B = 6;
		
		/**
		 * Credit Note for invoice "B".
		 * @var int
		 */
		const B_CREDIT_NOTE = 8;
		
		/**
		 * Debit Note for invoice "B".
		 * @var int
		 */
		const B_DEBIT_NOTE = 7;
		
		/**
		 * A singleton instance.
		 * @var Quanbit_Afip_Model_Enums_TypeEnum
		 */
		protected static $singleton;
		
		/**
		 * A matrix with the types compatibility.
		 * @var array
		 */
		protected static $compatibleMap = array(
			self::A => array(self::A => false, self::B => false, self::A_CREDIT_NOTE => true, self::B_CREDIT_NOTE => false, self::A_DEBIT_NOTE => true, self::B_DEBIT_NOTE => false),
			self::B => array(self::A => false, self::B => false, self::A_CREDIT_NOTE => false, self::B_CREDIT_NOTE => true, self::A_DEBIT_NOTE => false, self::B_DEBIT_NOTE => true),
			self::A_CREDIT_NOTE => array(self::A => true, self::B => false, self::A_CREDIT_NOTE => false, self::B_CREDIT_NOTE => false, self::A_DEBIT_NOTE => true, self::B_DEBIT_NOTE => false),
			self::B_CREDIT_NOTE => array(self::A => false, self::B => true, self::A_CREDIT_NOTE => false, self::B_CREDIT_NOTE => false, self::A_DEBIT_NOTE => false, self::B_DEBIT_NOTE => true),
			self::A_DEBIT_NOTE => array(self::A => true, self::B => false, self::A_CREDIT_NOTE => true, self::B_CREDIT_NOTE => false, self::A_DEBIT_NOTE => false, self::B_DEBIT_NOTE => false),
			self::B_DEBIT_NOTE => array(self::A => false, self::B => true, self::A_CREDIT_NOTE => false, self::B_CREDIT_NOTE => true, self::A_DEBIT_NOTE => false, self::B_DEBIT_NOTE => false)
		);
		
		
		
		/* Public methods */
		
		/**
		 * Indicates if given types are compatible.
		 *
		 * @param int $typeOne
		 * @param int $typeTwo
		 * @return boolean
		 */
		public static function areCompatible($typeOne, $typeTwo)
		{
			$result = @self::$compatibleMap[$typeOne][$typeTwo];
			return $result;
		}
		
		/**
		 * Indicates if given type can has billing target.
		 *
		 * @param int $type
		 * @return boolean
		 */
		public static function canHasBillingTarget($type)
		{
			$result = (($type != self::A) && ($type != self::B));
			return $result;
		}
		
		/**
		 * Returns a new singleton instance.
		 *
		 * @return Quanbit_Afip_Model_Enums_TypeEnum instance
		 */
		public static function getInstance()
		{
			if (!self::$singleton)
				self::$singleton = new self();
			
			return self::$singleton;
		}
		
		/**
		 * Returns the types for block "A" of billing receipts.
		 *
		 * @return array
		 */
		public static function getTypesForBlockA()
		{
			$types = array(self::A, self::A_CREDIT_NOTE, self::A_DEBIT_NOTE);
			return $types;
		}
		
		/**
		 * Returns the types for block "B" of billing receipts.
		 *
		 * @return array
		 */
		public static function getTypesForBlockB()
		{
			$types = array(self::B, self::B_CREDIT_NOTE, self::B_DEBIT_NOTE);
			return $types;
		}
		
		/**
		 * Returns the target types of a credit note.
		 *
		 * @return array
		 */
		public static function getTypesForCreditNote()
		{
			$types = array(self::A, self::B);
			return $types;
		}
		
		/**
		 * Returns the target types of a debit note.
		 *
		 * @return array
		 */
		public static function getTypesForDebitNote()
		{
			$types = array(self::A, self::B, self::A_CREDIT_NOTE, self::B_CREDIT_NOTE);
			return $types;
		}
		
		
		/**
		 * Returns the letter for a billing type key.
		 * @param int $billingType
		 * @return string
		 */
		public static function getLetterForBillingTypeKey($billingType)
		{
			$list = self::getInstance()->getList();
			if (in_array($billingType, self::getTypesForBlockA())) {
				return "A";
			}else{
				return "B";
			}
		}
		
		
		public function getDefaultKey()
		{
			return self::B;
		}
		
		public function getList()
		{
			$types = array(
				self::A => "FACTURA_A", self::B => "FACTURA_B",
				self::A_CREDIT_NOTE => "NOTA_CREDITO_A", self::B_CREDIT_NOTE => "NOTA_CREDITO_B",
				self::A_DEBIT_NOTE => "NOTA_DEBITO_A", self::B_DEBIT_NOTE => "NOTA_DEBITO_B"
			);
			
			return $types;
		}



		/* Protected methods */
		
		protected function getNameForPeople()
		{
			return "invoice type";
		}
	}
	
?>