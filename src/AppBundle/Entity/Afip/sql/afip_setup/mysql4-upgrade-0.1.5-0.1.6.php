<?php
$this->startSetup();
$this->run("
			ALTER TABLE `afip_invoice` ADD `neto_0250` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `observations`;
		    ALTER TABLE `afip_invoice` ADD `iva_0250` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `neto_0250`;
			
			ALTER TABLE `afip_invoice` ADD `neto_0500` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `iva_0250`;
			ALTER TABLE `afip_invoice` ADD `iva_0500` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `neto_0500`;
			
			ALTER TABLE `afip_invoice` ADD `neto_1050` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `iva_0500`;
			ALTER TABLE `afip_invoice` ADD `iva_1050` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `neto_1050`;
			
			ALTER TABLE `afip_invoice` ADD `neto_2100` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `iva_1050`;
			ALTER TABLE `afip_invoice` ADD `iva_2100` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `neto_2100`; 
			
			ALTER TABLE `afip_invoice` ADD `neto_2700` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `iva_2100`;
			ALTER TABLE `afip_invoice` ADD `iva_2700` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `neto_2700`;
			
			ALTER TABLE `afip_invoice` ADD `neto_exento` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `iva_2700`;
					
			ALTER TABLE `afip_invoice` ADD `neto_no_gravado` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `neto_exento`;
				
				    ");
$this->endSetup();