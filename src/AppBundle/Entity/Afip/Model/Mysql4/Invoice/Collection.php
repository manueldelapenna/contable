<?php

class Quanbit_Afip_Model_Mysql4_Invoice_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('afip/invoice');
    }
}