<?php
	
	/**
	 * Billing target for Debit Notes.
	 *
	 
	 * @author Eduardo Casey
	 */
	class BillingTargetForDebitNote extends BillingTarget
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
			$types = TypeEnum::getTypesForDebitNote();
			return $types;
		}
	}
	
?>