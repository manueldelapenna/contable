<?php

namespace AfipBundle\Entity\Model\Alicuota;/**
 * Helper for alicuotas of 21.00% for AFIP Invoice Manager.
 *

 * @author Eduardo Casey
 */
class Alicuota2100 extends Alicuota {
    /* Public methods */

    /**
     * Returns a new instance.
     *
     * @return Alicuota2100 instance
     */
    public static function getInstance() {
        return new self();
    }

    public function __construct() {
        $this->initialize();
    }

    public function getTaxPercent() {
        return 21.00;
    }

    public function getTaxType() {
        return TaxTypeEnum::IVA_2100;
    }

    /* Protected methods */

    protected function validate() {
        if ($this->taxAmount <= 0)
            $this->errors->add("The tax amount of alicuota (21.00%) must be greater than 0.");

        parent::validate();
    }

}

?>