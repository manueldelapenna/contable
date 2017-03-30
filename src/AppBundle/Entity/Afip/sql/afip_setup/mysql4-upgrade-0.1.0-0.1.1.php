<?php
$this->startSetup();
$this->run("
	
		DROP TABLE IF EXISTS afip_invoice;
		CREATE TABLE `afip_invoice`
		(
			`id` int(11) unsigned NOT NULL auto_increment,
			`order_invoice_id` int(11) unsigned NOT NULL,
			`cae_number` varchar (50) NULL,
			`cae_due_date` date NULL,
			`authorization_date` datetime NULL,
			`status` smallint(6) NOT NULL default '0',
			PRIMARY KEY (`id`),
			UNIQUE KEY `unique--order_invoice_id` (`order_invoice_id`),
			CONSTRAINT `order_invoice_id_FK_1`
				FOREIGN KEY (`order_invoice_id`)
				REFERENCES `{$this->getTable('sales/invoice')}` (`entity_id`)
				ON DELETE RESTRICT
		)Engine=InnoDb DEFAULT CHARSET=utf8;

    ");
$this->endSetup();