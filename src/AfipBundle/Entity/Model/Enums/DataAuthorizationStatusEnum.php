<?php

namespace AfipBundle\Entity\Model\Enums;

/**
 * Enumerative for invoice data authorization status for AFIP Invoice Manager.
 *

 * @author Eduardo Casey
 */
class DataAuthorizationStatusEnum extends AbstractEnum {
    /* Contants and Variables */

    /**
     * The sent invoice have been authorized.
     * @var string
     */
    const ACCEPTED = "A";

    /**
     * The invoice data has errors.
     * @var string
     */
    const INVALID = "X01";

    /**
     * The sent invoice data have been rejected.
     * @var string
     */
    const REJECTED = "R";

    /**
     * The invoice data are ready to be sent to Websersive (WSFE).
     * @var string
     */
    const SCHEDULED = "X00";

    /**
     * The invoice data does not have errors.
     * @var string
     */
    const VALID = "X02";

    /**
     * A singleton instance.
     * @var DataAuthorizationStatusEnum
     */
    protected static $singleton;

    /* Public methods */

    /**
     * Returns a new singleton instance.
     *
     * @return DataAuthorizationStatusEnum instance
     */
    public static function getInstance() {
        if (!self::$singleton) {
            self::$singleton = new self();
        }

        return self::$singleton;
    }

    public function getDefaultKey() {
        return self::SCHEDULED;
    }

    public function getList() {
        return
                array(self::ACCEPTED => "Accepted", self::INVALID => "Invalid", self::REJECTED => "Rejected", self::SCHEDULED => "Scheduled",
                    self::VALID => "Valid");
    }

    /* Protected methods */

    protected function getNameForPeople() {
        return "invoice authorization status";
    }

}

?>