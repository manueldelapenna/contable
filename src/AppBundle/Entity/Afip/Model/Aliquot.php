<?php 
class Aliquot extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

	
	
   public function getAllOptions(){
        if ($this->_options === null) {
            $this->_options = array();

            $this->_options[] = array(
                    'value' => 0,
                    'label' => AlicuotaProduct::EXENTO,
            );

            $this->_options[] = array(
            		'value' => 1,
            		'label' => AlicuotaProduct::IVA_0250,
            );
            
            $this->_options[] = array(
            		'value' => 2,
            		'label' => AlicuotaProduct::IVA_0500,
            );
            
            $this->_options[] = array(
            		'value' => 3,
            		'label' => AlicuotaProduct::IVA_1050,
            );
            
            $this->_options[] = array(
            		'value' => 4,
            		'label' => AlicuotaProduct::IVA_2100,
            );
            
            $this->_options[] = array(
            		'value' => 5,
            		'label' => AlicuotaProduct::IVA_2700,
            );

        }
 
        return $this->_options;
    }
    
}
?>
