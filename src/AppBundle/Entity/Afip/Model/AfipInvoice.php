<?php

/**
 * @author manueldelapenna
 *
 */
class AfipInvoice
{
	const PENDING = 0;
	const REJECTED = 1;
	const AUTHORIZED = 2;
	
    public function _construct()
    {    
        $this->_init('afip/invoice');
    }
    
    /**
     * Return max (last) AfipInvoice number for a specific billingType
     * 
     * @param Afip_Model_Enums_TypeEnum $billingType
     * @return int number
     */
    public static function getLastNumber($billingType){
    	$read = Mage::getSingleton('core/resource')->getConnection('core_read');
    	$status = AfipInvoice::AUTHORIZED;
    	$result = $read->fetchAll("SELECT MAX(number) as max 
    							   FROM afip_invoice 
    			                   where status = $status and
    									 type = $billingType
    			                   limit 1");
    	$max = $result[0]['max'];
    	if ($max == null){
    		$max = 0;
    	}
    	return $max;
    }
    
    /**
     * Retrieve 100 pending AfipInvoice for printing
     *
     * @return int number
     */
    public static function getPendingForPrinting(){
    	$status = AfipInvoice::AUTHORIZED;
    	return Mage::getModel('afip/invoice')->getCollection()
    	->addFieldToFilter('status',array('eq' => $status))
    	->addFieldToFilter('is_pdf_created', array('eq' => 0))
    	->setPageSize(100);
    	 
    }
    
    /**
     * Retrieve 250 pending AfipInvoice for a specific billingType
     *
     * @param Afip_Model_Enums_TypeEnum $billingType
     * @return Afip_Model_Mysql4_Invoice_Collection
     */
    public static function getPendingForAuthorize($billingType){
    	return Mage::getModel('afip/invoice')->getCollection()
    		->addFieldToFilter('status',array('eq' => AfipInvoice::PENDING))
    		->addFieldToFilter('type', array('eq' => $billingType))
    		->setPageSize(250);
    }
    
    public static function getStatusName($id){
    	 
    	switch ($id) {
    		case self::PENDING:
    			$option = "Pendiente";
    			break;
    		case self::REJECTED:
    			$option = "Rechazada";
    			break;
    		case self::AUTHORIZED:
    			$option = "Autorizada";
    			break;
    	}
    	 
    	return $option;
    	 
    }
    
    /**
     * Retrieve AfipInvoice by Mage_Sales_Model_Order_Invoice id
     * 
     * @param AfipInvoice attribute value
     * @return AfipInvoice
     */
    public function loadInvoiceByOrderInvoiceId($value){
    	return Mage::getModel('afip/invoice')->getCollection()->addFieldToFilter('order_invoice_id',array('eq' => $value))->getFirstItem();
    }
    
    /**
     * Retrieve AfipInvoice by Mage_Sales_Model_Order_Invoice id for download
     *
     * @param AfipInvoice attribute value
     * @return AfipInvoice
     */
    public function afipInvoiceForDownloadFromOrderInvoiceId($value){
    	return Mage::getModel('afip/invoice')->getCollection()
    					->addFieldToFilter('order_invoice_id',array('eq' => $value))
    					->addFieldToFilter('is_pdf_created',array('eq' => true))
    					->getFirstItem();
    }
    
    /**
     * Retrieve AfipInvoice with Rejected status by Mage_Sales_Model_Order_Invoice id
     *
     * @param AfipInvoice attribute value
     * @return AfipInvoice
     */
    public function afipInvoiceForChangeStateFromOrderInvoiceId($value){
    	return Mage::getModel('afip/invoice')->getCollection()
    	->addFieldToFilter('order_invoice_id',array('eq' => $value))
    	->addFieldToFilter('status',array('eq' => self::REJECTED))
    	->getFirstItem();
    }
    
    
    /**
     * Retrieve AfipInvoice by number and billingType
     * 
     * @param AfipInvoice attribute value
     * @param Afip_Model_Enums_TypeEnum $billingType
     * @return AfipInvoice
     */
    public function loadInvoiceByNumber($value, $billingType){
    	return Mage::getModel('afip/invoice')->getCollection()
    		->addFieldToFilter('number',array('eq' => $value))
    		->addFieldToFilter('type',array('eq' => $billingType))
    		->getFirstItem();
    }
    
    /**
     * Update and save AfipInvoice
     *
     * @param int $number
     * @param int $type
     * @param int $cae_number
     * @param DateTime $caeDueDate
     * @param DateTime $authorizationDate
     * @param string $observations
     * @param int $status
     * @return void
     */
    public function updateAndSave($number, $type, $cae_number,$caeDueDate,$authorizationDate,$observations,$status){
    	$this->setNumber($number);
    	$this->setType($type);
    	$this->setCaeNumber($cae_number);
    	$this->setCaeDueDate($caeDueDate);
    	$this->setAuthorizationDate($authorizationDate);
    	$this->setObservations($observations);
    	$this->setStatus($status);
    	if($status == AfipInvoice::REJECTED){
    		$this->resetAmounts();
    	}
    	$this->save();
    }
    
    public function resetAmounts(){
    	$this->setNeto_0250(0);
    	$this->setIva_0250(0);
    	$this->setNeto_0500(0);
    	$this->setIva_0500(0);
    	$this->setNeto_1050(0);
    	$this->setIva_1050(0);
    	$this->setNeto_2100(0);
    	$this->setIva_2100(0);
    	$this->setNeto_2700(0);
    	$this->setIva_2700(0);
    	$this->setNetoExento(0);
    	$this->setSubtotal(0);
    	$this->setTotalIva(0);
    	$this->setTotal(0);
    	
    	return $this;
    }
   
    /**
     * Returns an array of AfipInvoice states by id
     *
     * @return $array
     */
    public function getStates(){
		
		$options[self::PENDING] = "Pendiente";
		$options[self::REJECTED] = "Rechazada";
		$options[self::AUTHORIZED] = "Autorizada";
 	
   		return $options;
    }
    
    
    /**
     * Returns an array of AfipInvoice types by id
     *
     * @return $array
     */
    public function getTypes(){
    
    	$options[Afip_Model_Enums_TypeEnum::A] = "A";
    	$options[Afip_Model_Enums_TypeEnum::B] = "B";
    	    
    	return $options;
    	 
    }
    
    public static function setNullNumberToPedingInvoices($billingType){
    	
    	$resource = Mage::getSingleton('core/resource');
    	
    	$writeConnection = $resource->getConnection('core_write');
    	
    	$table = $resource->getTableName('afip/invoice');

    	$pendingStatus = self::PENDING;
    	
    	$query = "UPDATE {$table} SET number = null WHERE status = $pendingStatus and type = $billingType";
    	
    	$writeConnection->query($query);
    	
    }
}