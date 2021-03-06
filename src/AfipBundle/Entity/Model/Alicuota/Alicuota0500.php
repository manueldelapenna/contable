<?php

namespace AfipBundle\Entity\Model\Alicuota;
/**
 * Helper for alicuotas of 5.00% for AFIP Invoice Manager.
 *

 * @author manueldelapenna
 */
class Alicuota0500 extends Alicuota {
    /* Public methods */

    /**
     * Returns a new instance.
     *
     * @return Alicuota0500 instance
     */
    public static function getInstance() {
        return new self();
    }

    public function __construct() {
        $this->initialize();
    }

    public function getTaxPercent() {
        return 5;
    }

    public function getTaxType() {
        return TaxTypeEnum::IVA_0500;
    }

    /* Protected methods */

    protected function validate() {
        if ($this->taxAmount <= 0)
            $this->errors->add("The tax amount of alicuota (5.00%) must be greater than 0.");

        parent::validate();
    }

}

?>