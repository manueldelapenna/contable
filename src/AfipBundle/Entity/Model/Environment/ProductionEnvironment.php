<?php

namespace AfipBundle\Entity\Model\Environment;

/**
 * Helper for production environment of AFIP Invoice Manager.
 *

 * @author Eduardo Casey
 */
final class ProductionEnvironment extends Environment {

    /**
     * Returns a new instance.
     *
     * @return ProductionEnvironment instance
     */
    public static function getInstance() {
        return new self();
    }

    public function __construct() {
        parent::__construct();
    }

    public function getAfipCertificate() {
        return "{$this->getCertificatesDirectoryPath()}/AFIPcerthomo.crt";
    }

    public function getConsumerCertificate() {
        return "{$this->getCertificatesDirectoryPath()}/usershop_prod.crt";
    }

    public function getConsumerPrivateKey() {
        return "{$this->getCertificatesDirectoryPath()}/private_prod.key";
    }

    public function getEnvironmentKey() {
        return "production";
    }

    public function getPointOfSale() {
        return "0006";
    }

    public function getTaxpayerCuit() {
        return (float) "33709315329";
    }

    public function getWsaaCommonName() {
        return "wsaa.afip.gov.ar";
    }

    public function getWsaaUrl() {
        return "https://wsaa.afip.gov.ar/ws/services/LoginCms";
    }

    public function getWsfeUrl() {
        return "https://servicios1.afip.gov.ar/wsfev1/service.asmx";
    }

}

?>