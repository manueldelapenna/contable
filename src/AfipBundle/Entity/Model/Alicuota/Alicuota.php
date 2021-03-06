<?php

namespace AfipBundle\Entity\Model\Alicuota;

/**
 * Helper for alicuotas for AFIP Invoice Manager.
 *

 * @author Eduardo Casey
 */
abstract class Alicuota {
    /* Constants and Variables */

    /**
     * The base amount.
     * @var float
     */
    protected $baseAmount;

    /**
     * The errors.
     * @var ErrorCollectionHelper instance
     */
    protected $errors;

    /**
     * The tax amount.
     * @var float
     */
    protected $taxAmount;

    /* Public methods */

    /**
     * Transfors the inner information for AFIP Webservice (WSFE).
     *
     * @return array
     */
    public function asWebserviceInput() {
        $input = array("Id" => $this->getTaxType(), "BaseImp" => $this->getBaseAmount(), "Importe" => $this->getTaxAmount());
        return $input;
    }

    /**
     * Returns the base amount.
     *
     * @return float
     */
    public function getBaseAmount() {
        return $this->baseAmount;
    }

    /**
     * Returns the errors.
     *
     * @return ErrorCollectionHelper instance
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Returns the tax amount.
     *
     * @return float
     */
    public function getTaxAmount() {
        return $this->taxAmount;
    }

    /**
     * Returns the tax percent.
     *
     * @return float
     */
    abstract public function getTaxPercent();

    /**
     * Returns the tax type (TaxTypeEnum)
     *
     * @return int
     */
    abstract public function getTaxType();

    /**
     * Indicates whether the stored information is valid.
     *
     * @return void
     */
    public function isValid() {
        $this->errors->clean();
        $this->validate();

        return $this->errors->isEmpty();
    }

    /**
     * Sets the base amount.
     *
     * @param float $amount
     * @return void
     */
    public function setBaseAmount($amount) {
        $this->baseAmount = NumberDataTypeHelper::truncate(floatval($amount), 2);
    }

    /**
     * Sets the tax amount.
     *
     * @param float $amount
     * @return void
     */
    public function setTaxAmount($amount) {
        $this->taxAmount = NumberDataTypeHelper::truncate(floatval($amount), 2);
    }

    /* Protected methods */

    /**
     * Returns its name.
     *
     * @return string
     */
    protected function getName() {
        return TaxTypeEnum::getInstance()->getValueFor($this->getTaxType());
    }

    protected function initialize() {
        $this->setBaseAmount(0);
        $this->setTaxAmount(0);

        $this->errors = ErrorCollectionHelper::getInstance();
    }

    /**
     * Validates the stored data.
     *
     * @return void
     */
    protected function validate() {
        if ($this->getBaseAmount() <= 0)
            $this->errors->add("The base amount of alicuota must be greater than 0.");
// 			else
// 			{
// 				$relativeError = (($this->getTaxAmount() / $this->getBaseAmount() * 100) - $this->getTaxPercent());
// 				$relativeError = $relativeError / $this->getTaxPercent();
// 				$relativeError = NumberDataTypeHelper::truncate($relativeError, 2);
// 				if (abs($relativeError) > 0.01)
// 					$this->errors->add("The base and tax amount do not match with the alicuota ({$this->getName()}). Diference: $relativeError");
// 			}
    }

}

?>