<?php
//cambiar atributo a varchar por error instalacion en produccion
$installer = $this;

$installer->startSetup();

$attribute_id = $installer->getAttribute(
		Mage_Catalog_Model_Product::ENTITY,
		'aliquot',
		'attribute_id'
);

$connection = $this->getConnection();
$connection->beginTransaction();
$connection->query("DELETE FROM catalog_product_entity_varchar WHERE attribute_id = ?", array($attribute_id));
$connection->query("DELETE FROM catalog_product_entity_int WHERE attribute_id = ?", array($attribute_id));
$connection->commit();

$installer->updateAttribute('catalog_product', 'aliquot', array(
		'backend_type'    => 'int',
));
$this->endSetup();
?>