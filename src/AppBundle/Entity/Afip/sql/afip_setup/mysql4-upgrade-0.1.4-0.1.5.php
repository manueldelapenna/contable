<?php

$installer = $this;

$installer->startSetup();


$installer->removeAttributeIfPresent('aliquot');

$installer->installEntities();

$installer->cleanCache();


//Add to all attributes set
$installer->addAttributeToSetByNames('Book', 'Prices', 'aliquot','catalog_product',2);
$installer->addAttributeToSetByNames('Subscription', 'Prices', 'aliquot','catalog_product',2);
$installer->addAttributeToSetByNames('Fascicle', 'Prices', 'aliquot','catalog_product',2);
$installer->addAttributeToSetByNames('Magazine Number', 'Prices', 'aliquot','catalog_product',2);
$installer->addAttributeToSetByNames('BundleWithSuscription', 'Prices', 'aliquot','catalog_product',2);
$installer->addAttributeToSetByNames('Collection', 'Prices', 'aliquot','catalog_product',2);
$installer->addAttributeToSetByNames('Pack', 'Prices', 'aliquot','catalog_product',2);
$installer->addAttributeToSetByNames('Monthly Subscription', 'Prices', 'aliquot','catalog_product',2);
$installer->addAttributeToSetByNames('Premium Subscription', 'Prices', 'aliquot','catalog_product',2);
$installer->addAttributeToSetByNames('Collection Subscription', 'Prices', 'aliquot','catalog_product',2);

$installer->endSetup();