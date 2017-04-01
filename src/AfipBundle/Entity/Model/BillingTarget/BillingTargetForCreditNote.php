<?php

namespace AfipBundle\Entity\Model\BillingTarget;

/**
 * Billing target for Credit Notes.
 *

 * @author Eduardo Casey
 */
class BillingTargetForCreditNote extends BillingTarget {
    /* Public methods */

    public function getInstance() {
        $target = new self();
        return $target;
    }

    public function __construct() {
        $this->initialize();
    }

    /* Protected methods */

    protected function getNameForPeople() {
        return "credit note";
    }

    protected function getValidTargetTypes() {
        $types = TypeEnum::getTypesForCreditNote();
        return $types;
    }

}

?>