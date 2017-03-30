<?php

class Quanbit_Afip_Model_Resource_Eav_Mysql4_Setup extends Mage_Catalog_Model_Resource_Eav_Mysql4_Setup {
    
    public function getDefaultEntities() {
        $aliquot = array(
            'label' => 'Alicuota',
            'type' => 'int',
            'input' => 'select',
            'default' => '',
            'frontend_class' => null,
            'backend' => '',
            'frontend' => '',
            'source' => 'afip/aliquot',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'unique' => false,
        );
   
        return array(
            'catalog_product' => array(
                'entity_model' => 'catalog/product',
                'attribute_model' => 'catalog/resource_eav_attribute',
                'table' => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes' => array(
                    'aliquot' => $aliquot,
                )
            ),
        );
    }
 
    function removeAttributeSetIfPresent($setName) {
        try {
           $typeId = $this->getEntityTypeId('catalog_product');
           $setId = $this->getAttributeSetId($typeId, $setName);
           $this->removeAttributeSet($typeId, $setId);
           
           $sql = "DELETE FROM `{$this->getTable('eav/attribute_group')}` WHERE attribute_set_id = " . $setId;
           $this->getConnection()->query($sql);
           
           $sql = "DELETE FROM `{$this->getTable('eav/entity_attribute')}` WHERE attribute_set_id = " . $setId;
           $this->getConnection()->query($sql);
  
           } catch (Mage_Eav_Exception $e) {
            
            } //The set does not exist. Do nothing.
    }
    function addNewAttributeGroup($setName, $groupName, $entityType='catalog_product') {
        $entityTypeId = $this->getEntityTypeId($entityType);
        $setId = $this->getAttributeSetId($entityTypeId, $setName);
        $this->addAttributeGroup($entityTypeId, $setId, $groupName);
    }

    function addAttributeToSetByNames($setName, $groupName, $attributeCode, $entityType='catalog_product', $sortOrder = null) {
        //Get the "standard" entity type
        $entityTypeId = $this->getEntityTypeId($entityType);

        //Get the attribute id based on its code
        $row = $this->getTableRow('eav/attribute', 'attribute_code', $attributeCode);
        $attributeId = $row['attribute_id'];

        //Get the set id based in its name
        $setId = $this->getAttributeSetId($entityTypeId, $setName);

        //Get the group id based on the set it belongs an its name
        $groupId = $this->getAttributeGroupId($entityTypeId, $setId, $groupName);

        //Finally, create the f*****g attribute
        $newAttribute = Mage::getModel('eav/entity_attribute')
                ->setId($attributeId)
                ->setAttributeSetId($setId)
                ->setAttributeGroupId($groupId)
                ->setEntityTypeId($entityTypeId)
                ->setSortOrder($sortOrder)
                ->save();
    }

    function addNewAttributeSet($setName, $entityTypeCode = 'catalog_product') {
        $entityTypeId = $this->getEntityTypeId($entityTypeCode);

        Mage::app('default');
        $modelSet = Mage::getModel('eav/entity_attribute_set')
                ->setEntityTypeId($entityTypeId)
                ->setAttributeSetName($setName)
                ->save();
        $modelSet->initFromSkeleton($entityTypeId)->save();
    }

    function removeAttributeIfPresent($attributeCode) {
        $row = $this->getTableRow('eav/attribute', 'attribute_code', $attributeCode);
        if ($row !== false) {
            $entity_type_id = $row['entity_type_id'];
            $attribute_id = $row['attribute_id'];

            $this->removeAttribute($entity_type_id, $attribute_id);

            $catalog_row = $this->getTableRow(
                    'catalog_eav_attribute', 'attribute_id', $attribute_id);
            if ($catalog_row !== false) {
                $this->deleteTableRow(
                        'catalog_eav_attribute', 'attribute_id', $attribute_id);
            }
        }
    }
    function cleanCache(){
        parent::cleanCache();
        $this->_setupCache = array();
        return $this;
    }

}

?>
