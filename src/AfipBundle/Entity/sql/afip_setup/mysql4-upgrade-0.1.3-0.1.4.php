<?php
$this->startSetup();
$this->run("
			ALTER TABLE `afip_invoice` ADD `type` SMALLINT NOT NULL AFTER `order_invoice_id`;

			ALTER TABLE `afip_invoice` DROP INDEX `number`;

			ALTER TABLE `afip_invoice` ADD UNIQUE `type-number` ( `type` , `number` ) ;
		
			ALTER TABLE `afip_invoice` CHANGE `authorization_date` `authorization_date` DATE NULL DEFAULT NULL 
				    ");
$this->endSetup();