<?php
$this->startSetup();
$this->run("
			ALTER TABLE `afip_invoice` ADD `subtotal` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `neto_no_gravado`;

			ALTER TABLE `afip_invoice` ADD `total_iva` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `subtotal`;
			
			ALTER TABLE `afip_invoice` ADD `total` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `total_iva`;
		
			ALTER TABLE `afip_invoice` ADD `moneda` VARCHAR(15) NULL DEFAULT '' AFTER `total`;
						
			ALTER TABLE `afip_invoice` ADD `cotizacion_moneda_extranjera_pesos` DECIMAL( 12, 4 ) NULL DEFAULT 0 AFTER `moneda`;
				
				    ");
$this->endSetup();