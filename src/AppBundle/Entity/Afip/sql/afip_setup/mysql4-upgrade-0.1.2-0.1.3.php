<?php
$this->startSetup();
$this->run("
			ALTER TABLE `afip_invoice` ADD `is_pdf_created` BOOLEAN NOT NULL DEFAULT '0' AFTER `observations` 
		    ");
$this->endSetup();