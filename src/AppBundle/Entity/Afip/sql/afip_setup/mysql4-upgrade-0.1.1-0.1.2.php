<?php
$this->startSetup();
$this->run("
			ALTER TABLE `afip_invoice` ADD `number` BIGINT NULL AFTER `order_invoice_id` ,
			ADD UNIQUE (
			`number`
			);
		
			ALTER TABLE `afip_invoice` ADD `observations` VARCHAR(255) NULL AFTER `authorization_date`;
    ");
$this->endSetup();