<?php
/**
 * Barcode Item 
 * 
 * Represents an item of a barcode, a barcode is formed of one to several
 * barcode items.
 * 
 * @author Agustin Quiroga
 * @since 17/09/2007 
 * @package Barcode
 */

class Afip_Model_Pdf_Barcode_Lib_Item {
	
	/**
	 * Item Value
	 * 
	 * @var int
	 */
	private $value = 0;
	
	/**
	 * Amount of digit positions in the item
	 * 
	 * @var int
	 */
	private $positions = 1;
	
	/**
	 * Set the item value
	 * 
	 * @param int $value
	 */
	public function setValue($int) {
		$this->value = $int;
	}
	
	/**
	 * Set the digits positions in the item value
	 * 
	 * @param int $int
	 */
	public function setPositions($int) {
		$this->positions = $int;
	}
	
	/**
	 * Get item formated value
	 */
	public function getValue() {
		return (string) str_pad($this->value, $this->positions, "0", STR_PAD_LEFT);
	}
	
} 
?>
