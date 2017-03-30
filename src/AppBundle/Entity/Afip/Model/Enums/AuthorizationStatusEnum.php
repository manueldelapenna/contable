<?php
	
	/**
	 * Enumerative for invoice authorization status for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Model_Enums_AuthorizationStatusEnum extends Quanbit_Afip_Model_Enums_AbstractEnum
	{
		/* Contants and Variables */
		
		/**
		 * The sent invoices have been authorized.
		 * @var string
		 */
		const ACCEPTED = "A";
		
		/**
		 * There was an exception on code and the autorization process was aborted.
		 * @var string
		 */
		const EXCEPTION = "X01";
		
		/**
		 * No result was recieved from Webservice (WSFE).
		 * @var string
		 */
		const NO_RESULT_GIVEN = "X03";
		
		/**
		 * After the processing of invoices data, there are not valid data to be sent to Webservice (WSFE).
		 * @var string
		 */
		const NO_VALID_DATA = "X02";
		
		/**
		 * The sent invoices have been partially authorized (there are rejected invoices).
		 * @var string
		 */
		const PARTIAL = "P";
		
		/**
		 * The sent invoices data have been rejected.
		 * @var string
		 */
		const REJECTED = "R";
		
		/**
		 * The invoices data are ready to be sent to Websersive (WSFE).
		 * @var string
		 */
		const SCHEDULED = "X00";
		
		
		/**
		 * A singleton instance.
		 * @var Quanbit_Afip_Model_Enums_AuthorizationStatusEnum
		 */
		protected static $singleton;
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new singleton instance.
		 *
		 * @return Quanbit_Afip_Model_Enums_AuthorizationStatusEnum instance
		 */
		public static function getInstance()
		{
			if (!self::$singleton)
				self::$singleton = new self();
			
			return self::$singleton;
		}
		
		/**
		 * Returns the status for a normal ending of authorization process.
		 *
		 * @return array
		 */
		public static function normalEndingStatus()
		{
			$status = array(self::ACCEPTED, self::NO_VALID_DATA, self::PARTIAL, self::REJECTED);
			return $status;
		}
		
		
		
		public function getDefaultKey()
		{
			return self::SCHEDULED;
		}
		
		public function getList()
		{
			return
				array(self::ACCEPTED => "Accepted", self::EXCEPTION => "Exception", self::PARTIAL => "Partial", self::NO_RESULT_GIVEN => "No result given",
				      self::NO_VALID_DATA => "No valid data", self::REJECTED => "Rejected", self::SCHEDULED => "Scheduled");
		}



		/* Protected methods */
		
		protected function getNameForPeople()
		{
			return "invoice authorization status";
		}
	}
	
?>