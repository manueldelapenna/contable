<?php

namespace AfipBundle\Entity\Model\Environment;

/**
 * Helper for staging environment for AFIP Invoice Manager.
 *

 * @author Eduardo Casey
 */
final class StagingEnvironment extends Environment {

    /**
     * Returns a new instance.
     *
     * @return StagingEnvironment instance
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
        return "{$this->getCertificatesDirectoryPath()}/usershop_test.crt";
    }

    public function getConsumerPrivateKey() {
        return "{$this->getCertificatesDirectoryPath()}/private_test.key";
    }

    public function getEnvironmentKey() {
        return "staging";
    }

    public function getPointOfSale() {
        return '0006';
    }

    public function getTaxpayerCuit() {
        return (float) "20307777461";
    }

    public function getWsaaCommonName() {
        return "wsaahomo.afip.gov.ar";
    }

    public function getWsaaUrl() {
        return "https://wsaahomo.afip.gov.ar/ws/services/LoginCms";
    }

    public function getWsfeUrl() {
        return "https://wswhomo.afip.gov.ar/wsfev1/service.asmx";
    }

}

?>