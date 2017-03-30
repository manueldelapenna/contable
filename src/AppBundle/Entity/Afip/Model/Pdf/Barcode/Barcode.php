<?php
/**
 * Rapipago barcode implementation.
 *
 * Bridge to generate barcodes for payment bils
 *
 * @author Agustin Quiroga
 * @since 13/09/2007
 * @package Rapipago
 */

//require_once 'Barcode/Code.php';
//require_once 'Math/Verification/ModuleTenDigit.php';
//require_once 'Barcode/Encoder.php';

/**
 * Barcode generation class
 *
 * Uses a barcode generation class to produce the barcode image.
 * Also calculates and adds the corresponding verification
 * digit at the end of the barcode. The verification digit rutine
 * can be changed or shaped at will.
 * Usage:
 * <code>
 * $barcode = new Rapipago_Barcode();
 * $barcode->setCommerceId = 000;
 * $barcode->setClientId = 000;
 * $barcode->setPaymentId = 000;
 * $barcode->setPaymentAmount = 000.00;
 * $barcode->setDueDate = 0000000;
 * $barcode->getBarcodeImage();
 * </code>
 *
 * @package Rapipago
 * @author Agustin Quiroga
 * @since 13/09/2007
 */
class Afip_Model_Pdf_Barcode_Barcode {

	/**
	 * Barcode
	 *
	 * @var Barcode_Code
	 */
	private $code = null;

	/**
	 * Rapipago commerceId, int, 000 format
	 *
	 * @var int
	 */


	/**
	 * Create Barcode Object to work
	 */
	public function __construct() {
		$this->code = new Afip_Model_Pdf_Barcode_Lib_Code();
	}


	/**
	 * format client id
	 *
	 * @param int $int
	 */
	public function addCUIT($cuit) {
		$item = new Afip_Model_Pdf_Barcode_Lib_Item();
		$item->setValue($cuit);
		$item->setPositions(11);
		$this->code->addItem($item,0);
	}

	/**
	 * format payment id
	 *
	 * @param int $int
	 */
	public function addInvoiceType($type) {
		$item = new Afip_Model_Pdf_Barcode_Lib_Item();
		$item->setValue($type);
		$item->setPositions(2);
		$this->code->addItem($item,1);
	}

	public function addPOS($pos) {
		$item = new Afip_Model_Pdf_Barcode_Lib_Item();
		$item->setValue($pos);
		$item->setPositions(4);
		$this->code->addItem($item,2);
	}

	/**
	 * Format payment amount
	 *
	 * @param float $float
	 */
	public function addCAE($cae) {
		$item = new Afip_Model_Pdf_Barcode_Lib_Item();
		$item->setValue($cae);
		$item->setPositions(14);
		$this->code->addItem($item,3);
	}

	/**
	 * Format due date from a timestamp
	 *
	 * @param timestamp $date
	 */
	public function addGenerationTime($date) {
		$item = new Afip_Model_Pdf_Barcode_Lib_Item();
		$dueDate = (string) date("Ymd",strtotime($date));
		$item->setValue($dueDate);
		$item->setPositions(8);
		$this->code->addItem($item,4);
	}

	/**
	 * Retrieve final barcode value: original + verificatioon digit
	 *
	 * @return int
	 */
	public function getBarcodeValue() {
		return $this->code->getCode() . $this->getVerificationDigit($this->code->getCode());
	}

	/**
	 * HTML generation method
	 *
	 * @param unknown_type $value
	 */
	public function getBarcodeImage() {
		$bar = new Afip_Model_Pdf_Barcode_Lib_Encoder('I25');
		$bar->setHeight(34);
		$bar->setScale(0.9);
		$bar->genBarCode($this->getBarcodeValue(),'png',$this->getBarcodeValue());
		return $bar->fileName;
	}
	public function getVerificationDigit($code){
		$code_values = str_split($code);
		//Etapa 1: Comenzar desde la izquierda, sumar todos los caracteres ubicados en las posiciones impares.
		$odd_chars = 0;
		for($i=0; $i<count($code_values);$i+=2){
			$odd_chars +=$code_values[$i];
		}
		//Etapa 2: Multiplicar la suma obtenida en la etapa 1 por el n�mero 3.
		$odd_chars *=3;
		//Etapa 3: Comenzar desde la izquierda, sumar todos los caracteres que est�n ubicados en las posiciones pares.
		$even_chars = 0;
		for($i=1; $i<count($code_values);$i+=2){
			$even_chars +=$code_values[$i];
		}
		//Etapa 4: Sumar los resultados obtenidos en las etapas 2 y 3.
		$all_chars = $odd_chars+$even_chars;
		//Etapa 5: Buscar el menor n�mero que sumado al resultado obtenido en la etapa 4 d� un n�mero m�ltiplo de 10. Este ser� el valor del d�gito verificador del m�dulo 10.
		$verification = 10-($all_chars%10);
		return $verification;
	}

}
?>