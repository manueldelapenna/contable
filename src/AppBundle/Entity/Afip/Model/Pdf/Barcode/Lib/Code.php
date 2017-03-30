<?php
/**
 * Code 
 * 
 * Represents a Barcode code, a barcode code is formed of one to several barcode items.
 * 
 * @author Agustin Quiroga
 * @since 17/09/2007 
 * @package Barcode
 */
class Quanbit_Afip_Model_Pdf_Barcode_Lib_Code {
	
	/**
	 * Items
	 * 
	 * @var array
	 */
	private $items = array();
		
	/**
	 * Add an item to the code
	 * 
	 * @param Barcode_Item $item item to be added
	 * @param int $pos the desired slot in the code to place it
	 */
	public function addItem(Quanbit_Afip_Model_Pdf_Barcode_Lib_Item $item, $pos = 0) {
		$pos = ($pos!=0) ? $pos : count($this->items);
		$this->items[$pos] = $item;
	}
	
	/**
	 * Returns the complete formated final code
	 * 
	 * @param string
	 */
	public function getCode() {
		$code = '';
		ksort($this->items);
		foreach ($this->items as $item) {
			$code .= $item->getValue();
		}
		return $code;
	}
	
	/**
	 * Bridge to the barcode generator
	 * 
	 * @return string
	 */
	private function getEncodedImage($enc="I2O5") {
		$bar = new Quanbit_Afip_Model_Pdf_Barcode_Lib_Encoder($enc);
		$bar->setSymblogy($enc);
	    $bar->setHeight(60);
	    $bar->setFont("arial");
	    $bar->setScale(1);
	    $bar->setHexColor("#000000","#FFFFFF");
	    $bar->setColor(255,255,255);
	    $bar->setBGColor(0,0,0);
	    return $bar->genBarCode($this->getCode());
	}
	
	
	
} 
?>
