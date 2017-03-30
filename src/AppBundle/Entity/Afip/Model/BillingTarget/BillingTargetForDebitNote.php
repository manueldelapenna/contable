<?php
	
	/**
	 * Billing target for Debit Notes.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Afip_Model_BillingTarget_BillingTargetForDebitNote extends Afip_Model_BillingTarget_BillingTarget
	{
		/* Public methods */
		
		public function getInstance()
		{
			$target = new self();
			return $target;
		}
		
		public function __construct()
		{
			$this->initialize();
		}
		
		
		
		/* Protected methods */
		
		protected function getNameForPeople()
		{
			return "debit note";
		}
		
		protected function getValidTargetTypes()
		{
			$types = Afip_Model_Enums_TypeEnum::getTypesForDebitNote();
			return $types;
		}
	}
	
?>