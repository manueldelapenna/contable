<?php
	
	/**
	 * Billing target for Credit Notes.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class BillingTargetForCreditNote extends BillingTarget
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
			return "credit note";
		}
		
		protected function getValidTargetTypes()
		{
			$types = Afip_Model_Enums_TypeEnum::getTypesForCreditNote();
			return $types;
		}
	}
	
?>