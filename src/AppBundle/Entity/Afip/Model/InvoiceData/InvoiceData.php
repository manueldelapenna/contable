<?php
	
	/**
	 * Helper for invoice data for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class AfipInvoiceData_InvoiceData
	{
		/* Constants and Variables */
		
		/**
		 * The alicuotas.
		 * @var Afip_Model_Alicuota_AlicuotaCollector instance
		 */
		protected $alicuotas;
		
		/**
		 * The authorization date.
		 * @var string | NULL
		 */
		protected $authDate;
		
		/**
		 * The given CAE from AFIP.
		 * @var string | NULL
		 */
		protected $cae;
		
		/**
		 * The CAE due date.
		 * @var string | NULL
		 */
		protected $caeDueDate;
		
		/**
		 * The concept.
		 * @var int | NULL
		 */
		protected $concept;
		
		/**
		 * The asociated credit note target.
		 * @var QbAfipBillingTargetHelper | NULL
		 */
		protected $billingTarget;
		
		/**
		 * The document number of customer.
		 * @var float
		 */
		protected $documentNumber;
		
		/**
		 * The document type of customer.
		 * @var int | NULL
		 */
		protected $documentType;
		
		/**
		 * The errors.
		 * @var ErrorCollectionHelper instance
		 */
		protected $errors;
		
		/**
		 * The ID that identify the invoice in your system.
		 * @var int | NULL
		 */
		protected $id;
		
		/**
		 * The invoice date.
		 * @var string
		 */
		protected $invoiceDate;
		
		/**
		 * The invoice number.
		 * @var int | NULL
		 */
		protected $invoiceNumber;
		
		/**
		 * The invoice type.
		 *
		 * @var int | NULL
		 */
		protected $invoiceType;
		
		/**
		 * The authorization status.
		 * @var string
		 */
		protected $status;
		
		/**
		 * The taxable net amount.
		 * @var float
		 */
		protected $taxableNetAmount;
		
		/**
		 * The tax amount.
		 * @var float
		 */
		protected $taxAmount;
		
		/**
		 * The tax-exempt amount.
		 * @var float
		 */
		protected $taxExemptAmount;
		
		/**
		 * The untaxed net amount.
		 * @var float
		 */
		protected $untaxedNetAmount;
		
		/**
		 * The store ID from Invoice.
		 * @var int
		 */
		protected $storeId;
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new instance.
		 *
		 * @return AfipInvoiceData_InvoiceData intance
		 */
		public static function getInstance()
		{
			return new self();
		}
		
		public function __construct()
		{
			$this->initialize();
		}
		
		
		/**
		 * Stores the given CAE and changes the status to accepted.
		 *
		 * @param string $cae
		 * @param string $caeDueDate
		 * @param string $authDate
		 * @return void
		 * @throws Afip_Exception_Lib_Exception Throws an exception whether given CAE is empty or is invalid.
		 */
		public function accepted($cae, $caeDueDate, $authDate)
		{
			if ($this->status == Afip_Model_Enums_DataAuthorizationStatusEnum::VALID)
			{
				$this->setCae($cae);
				if (isset($this->cae) && ($this->cae != ""))
				{
					$this->status = Afip_Model_Enums_DataAuthorizationStatusEnum::ACCEPTED;
					$this->caeDueDate = $this->getDateFrom($caeDueDate);
					$this->authDate = $this->getDateFrom($authDate);
				}
				else
					Afip_Exception_ExceptionFactory::throwFor("No valid CAE given.");
			}
		}
		
		/**
		 * Adds an alicuota.
		 *
		 * @param Afip_Model_Alicuota_Alicuota $alicuota
		 * @return void
		 */
		public function addAlicuota(Afip_Model_Alicuota_Alicuota $alicuota)
		{
			$this->alicuotas->add($alicuota);
		}
		
		/**
		 * Transfors the inner information for AFIP Webservice (WSFE).
		 *
		 * @param string $pointOfSale
		 * @return array
		 */
		public function asWebserviceInput($pointOfSale)
		{
			$input = array();
			$input["Concepto"] = $this->getConcept();
			$input["DocTipo"] = $this->getDocumentType();
			$input["DocNro"] = $this->getDocumentNumber();
			$input["CbteDesde"] = $this->getInvoiceNumber();
			$input["CbteHasta"] = $this->getInvoiceNumber();
			$input["CbteFch"] = $this->getInvoiceDate();
			$input["ImpTotal"] = $this->getTotalAmount();
			$input["ImpTotConc"] = $this->getUntaxedNetAmount();
			$input["ImpNeto"] = $this->getTaxableNetAmount();
			$input["ImpOpEx"] = $this->getTaxExemptAmount();
			$input["ImpIVA"] = $this->getTaxAmount();
			$input["ImpTrib"] = 0;
			$input["MonId"] = $this->getCurrencyName();
			$input["MonCotiz"] = $this->getCurrencyValue();
			
			if ($this->hasBillingTarget())
				$input["CbtesAsoc"]["CbteAsoc"][] = $this->getBillingTarget()->asWebserviceInput($pointOfSale);
			
			if ($this->getTaxableNetAmount() > 0)
				$input["Iva"] = $this->getAlicuotas()->asWebserviceInput();
			
			return $input;
		}
		
		/**
		 * Returns the alicuota collection.
		 *
		 * @return Afip_Model_Alicuota_AlicuotaCollector instance
		 */
		public function getAlicuotas()
		{
			return $this->alicuotas;
		}
		
		/**
		 * Returns the authorizarion date.
		 *
		 * @return string | NULL
		 */
		public function getAuthDate()
		{
			return $this->authDate;
		}
		
		/**
		 * Returns the CAE.
		 *
		 * @return string | NULL
		 */
		public function getCae()
		{
			return $this->cae;
		}
		
		/**
		 * Returns the CAE due date.
		 *
		 * @return string | NULL
		 */
		public function getCaeDueDate()
		{
			return $this->caeDueDate;
		}
		
		/**
		 * Returns the invoice concept.
		 *
		 * @return int | NULL
		 */
		public function getConcept()
		{
			return $this->concept;
		}
		
		/**
		 * Returns the currency name.
		 *
		 * @return string
		 */
		public function getCurrencyName()
		{
			return Afip_Model_Enums_CurrencyEnum::PESOS;
		}
		
		/**
		 * Returns the currency value.
		 *
		 * @return float
		 */
		public function getCurrencyValue()
		{
			return 1;
		}
		
		/**
		 * Returns the customer document number.
		 *
		 * @return float
		 */
		public function getDocumentNumber()
		{
			return $this->documentNumber;
		}
		
		/**
		 * Returns the customer document type.
		 *
		 * @return int | NULL
		 */
		public function getDocumentType()
		{
			return $this->documentType;
		}
		
		/**
		 * Returns the asociated credit note target.
		 *
		 * @return QbAfipBillingTargetHelper | NULL;
		 */
		public function getBillingTarget()
		{
			return $this->billingTarget;
		}
		
		/**
		 * Returns the errors.
		 *
		 * @return ErrorCollectionHelper instance
		 */
		public function getErrors()
		{
			$errors = ErrorCollectionHelper::getInstance();
			$errors->addFrom($this->errors);
			$errors->addFrom($this->alicuotas->getErrors());
			
			return $errors;
		}
		
		/**
		 * Returns the ID that identify the invoice in your system.
		 *
		 * @return int | NULL
		 */
		public function getId()
		{
			return $this->id;
		}
		
		/**
		 * Returns the invoice date as a formated string (YYYYMMDD).
		 *
		 * @return string | NULL
		 */
		public function getInvoiceDate()
		{
			return $this->invoiceDate;
		}
		
		/**
		 * Returns the invoice number.
		 *
		 * @return int | NULL
		 */
		public function getInvoiceNumber()
		{
			return $this->invoiceNumber;
		}
		
		/**
		 * Returns the invoice type.
		 *
		 * @return int | NULL
		 */
		public function getInvoiceType()
		{
			return $this->invoiceType;
		}
		
		/**
		 * Returns the taxable net amount.
		 *
		 * @return number
		 */
		public function getTaxableNetAmount()
		{
			return $this->taxableNetAmount;
		}
		
		/**
		 * Returns the authorization status.
		 *
		 * @return string
		 */
		public function getStatus()
		{
			return $this->status;
		}
		
		/**
		 * Returns the tax amount.
		 *
		 * @return float
		 */
		public function getTaxAmount()
		{
			return $this->taxAmount;
		}
		
		/**
		 * Returns the tax-exempt amount.
		 *
		 * @return float
		 */
		public function getTaxExemptAmount()
		{
			return $this->taxExemptAmount;
		}
		
		/**
		 * Returns the total amount.
		 *
		 * @return float
		 */
		public function getTotalAmount()
		{
			$amount = NumberDataTypeHelper::truncate($this->getTaxableNetAmount() + $this->getUntaxedNetAmount() + $this->getTaxExemptAmount() + $this->getTaxAmount(), 2);
			return $amount;
		}
		
		/**
		 * Returns the untaxed net amount.
		 *
		 * @return float
		 */
		public function getUntaxedNetAmount()
		{
			return $this->untaxedNetAmount;
		}
		
		/**
		 * Returns the store id from Invoice.
		 *
		 * @return int
		 */
		public function getStoreId()
		{
			return $this->storeId;
		}
		
		/**
		 * Indicates if it associated with a credit note target.
		 *
		 * @return boolean
		 */
		public function hasBillingTarget()
		{
			$result = ($this->getBillingTarget() !== NULL);
			return $result;
		}
		
		/**
		 * Indicates whether the stored information is valid.
		 *
		 * @return boolean
		 */
		public function isValid()
		{
			$this->validate();
			
			if ($this->errors->isEmpty())
			{
				$this->status = Afip_Model_Enums_DataAuthorizationStatusEnum::VALID;
				return true;
			}
			else
			{
				$this->status = Afip_Model_Enums_DataAuthorizationStatusEnum::INVALID;
				return false;
			}
		}
		
		/**
		 * Stores the given error and changes the status to rejected.
		 *
		 * @param array $errors [Default: array()]
		 * @return void
		 */
		public function rejected($errors = array())
		{
			if ($this->status == Afip_Model_Enums_DataAuthorizationStatusEnum::VALID)
			{
				if (is_array($errors) && (count($errors) > 0))
				{
					foreach ($errors as $error)
						$this->errors->add($error);
				}
				else
					$this->errors->add("UNKNOWN");
				
				$this->status = Afip_Model_Enums_DataAuthorizationStatusEnum::REJECTED;
			}
		}
		
		/**
		 * Sets the concept.
		 *
		 * @param int $concept
		 * @return void
		 */
		public function setConcept($concept)
		{
			$this->concept = $concept;
		}
		
		/**
		 * Sets the customer document number.
		 *
		 * @param float $number
		 * @return void
		 */
		public function setDocumentNumber($number)
		{
			$this->documentNumber = floatval($number);
		}
		
		/**
		 * Sets the customer document type.
		 *
		 * @param int $type
		 * @return void
		 */
		public function setDocumentType($type)
		{
			$this->documentType = $type;
		}
		
		/**
		 * Set the asociated credit note target.
		 *
		 * @param QbAfipBillingTargetHelper $billingTarget
		 * @return void
		 */
		public function setBillingTarget($billingTarget)
		{
			$this->billingTarget = $billingTarget;
		}
		
		/**
		 * Sets the ID that identify the invoice in your system.
		 *
		 * @param int $id
		 * @return void
		 */
		public function setId($id)
		{
			$this->id = intval($id);
		}
		
		/**
		 * Sets the invoice date.
		 *
		 * @param string $date
		 * @return void
		 */
		public function setInvoiceDate($date)
		{
			$date = trim(strval($date));
			
			if ($date == "")
				$this->invoiceDate = NULL;
			else
				$this->invoiceDate = date("Ymd", strtotime($date));
		}
		
		/**
		 * Sets the invoice number.
		 *
		 * @param int $number
		 * @return void
		 */
		public function setInvoiceNumber($number)
		{
			$this->invoiceNumber = intval($number);
		}
		
		/**
		 * Sets the invoice type.
		 *
		 * @param int $type
		 * @return void
		 */
		public function setInvoiceType($type)
		{
			$this->invoiceType = $type;
		}
		
		/**
		 * Sets the taxable net amount.
		 *
		 * @param float $amount
		 * @return void
		 */
		public function setTaxableNetAmount($amount)
		{
			$this->taxableNetAmount = NumberDataTypeHelper::truncate(floatval($amount), 2);
		}
		
		/**
		 * Sets the tax amount.
		 *
		 * @param float $taxAmount
		 * @return void
		 */
		public function setTaxAmount($amount)
		{
			$this->taxAmount = NumberDataTypeHelper::truncate(floatval($amount), 2);
		}
		
		/**
		 * Sets the tax-exempt amount.
		 *
		 * @param float $taxExempAmount
		 * @return void
		 */
		public function setTaxExemptAmount($amount)
		{
			return $this->taxExemptAmount = NumberDataTypeHelper::truncate(floatval($amount), 2);
		}
		
		/**
		 * Sets the untaxed net amount.
		 *
		 * @param float $amount
		 * @return void
		 */
		public function setUntaxedNetAmount($amount)
		{
			$this->untaxedNetAmount = NumberDataTypeHelper::truncate(floatval($amount), 2);
		}
		
		/**
		 * Sets the store id for Invoice.
		 *
		 * @param int $id
		 * @return void
		 */
		public function setStoreId($id)
		{
			$this->storeId = $id;
		}
		
		
		/* Protected methods */
		
		/**
		 * Initializes the instance.
		 *
		 * @return void
		 */
		protected function initialize()
		{
			$this->alicuotas = Afip_Model_Alicuota_AlicuotaCollector::getInstance();
			$this->authDate = NULL;
			$this->authStatus = Afip_Model_Enums_DataAuthorizationStatusEnum::SCHEDULED;
			$this->cae = NULL;
			$this->concept = NULL;
			$this->billingTarget = NULL;
			$this->documentNumber = 0;
			$this->documentType = NULL;
			$this->errors = ErrorCollectionHelper::getInstance();
			$this->id = NULL;
			$this->invoiceDate = NULL;
			$this->invoiceNumber = NULL;
			$this->invoiceType = NULL;
			$this->taxableNetAmount = 0;
			$this->taxAmount = 0;
			$this->taxExemptAmount = 0;
			$this->untaxedNetAmount = 0;
		}
		
		/**
		 * Returns a date from given compact date.
		 *
		 * @param string $date
		 * @return string
		 */
		protected function getDateFrom($date)
		{
			$date = trim(strval($date));
			if ($date == "")
				return date("Y-m-d");
			else
				return substr($date, 0, 4) . "-" . substr($date, 4, 2) . "-" . substr($date, 6, 2);
		}
		
		/**
		 * Sets the given CAE from AFIP.
		 *
		 * @param string $cae
		 * @return void
		 */
		protected function setCae($cae)
		{
			$this->cae = str_replace(" ", "", strval($cae));
		}
		
		/**
		 * Validates the stored data.
		 *
		 * @return void
		 */
		protected function validate()
		{
			$this->errors->clean();
			
			
			/* Checking basic fields */
			
			$documentHelper = Afip_Model_Enums_DocumentTypeEnum::getInstance();
			
			if (!Afip_Model_Enums_TypeEnum::getInstance()->isValidKey($this->invoiceType))
				$this->errors->add("El tipo de facturación no es una opción válida.");
			
			if (!Afip_Model_Enums_ConceptEnum::getInstance()->isValidKey($this->concept))
				$this->errors->add("El concepto de facturacón no es válido.");
			
			if (!$documentHelper->isValidKey($this->documentType))
				$this->errors->add("El tipo de documento no es válido.");
			
			if (($this->documentNumber < 0) || ($this->documentNumber > 99999999999))
				$this->errors->add("El número de documento debe ser un número entero entre 0 y 99999999999.");
			
			if ($this->taxableNetAmount < 0)
				$this->errors->add("El monto gravado no puede ser inferior a 0.");
			elseif ($this->taxableNetAmount > 0)
			{
				if ($this->alicuotas->count() > 0)
				{
					$absoluteError = ($this->alicuotas->getTaxAmount() - $this->getTaxAmount());
					$absoluteError = NumberDataTypeHelper::truncate($absoluteError, 2);
					
					if (abs($absoluteError) > 0.01)
						$this->errors->add("El monto de las alicuotas no coincide con el monto de IVA de la Factura. Error Absoluto: $absoluteError");
				}
				else
					$this->errors->add("Es obligatorio declarar Alicuotas (no existen alicuotas declaradas).");
			}
			
			if ($this->untaxedNetAmount < 0)
				$this->errors->add("El monto no gravado no puede ser inferior a 0.");
			
			if ($this->taxAmount < 0)
				$this->errors->add("El monto de IVA no puede ser inferior a 0.");
			
			if ($this->taxExemptAmount < 0)
				$this->errors->add("El monto Exento no puede ser inferior a 0.");
			
			if ($this->getTotalAmount() <= 0)
				$this->errors->add("La sumatoria de todos los montos declarados no puede ser menor o igual a 0.");
			elseif ($this->getTotalAmount() > 1000000)
				$this->errors->add("La sumatoria de todos los montos no puede superar los $1.000.000.");
			
			if (($this->invoiceDate === NULL) || ($this->invoiceDate == ""))
				$this->errors->add("La factura debe tener una fecha.");
			
			if (!is_int($this->id))
				$this->errors->add("No se definió un id de factura.");
			
			
			/* Checking fields related with billing type */
			if (in_array($this->invoiceType, Afip_Model_Enums_TypeEnum::getTypesForBlockA()))
			{
				if ($this->getStoreId() != 1){
					$this->errors->add("Únicamente tienda ARG puede realizar comprobantes de tipo A.");
				}
				
				if ($this->documentType != Afip_Model_Enums_DocumentTypeEnum::CUIT)
					$this->errors->add("El tipo de documento debe ser '" . $documentHelper->getValueFor(Afip_Model_Enums_DocumentTypeEnum::CUIT) . "' para una factura, nota de débito o nota de crédito de tipo 'A'.");
				
				if ($this->documentNumber == 0)
					$this->errors->add("El número de documento es obligatorio para facturas, notas de crédito y notas de débito de tipo A.");
			}
			elseif (in_array($this->invoiceType, Afip_Model_Enums_TypeEnum::getTypesForBlockB()))
			{
				if ($this->getTotalAmount() >= 1000)
				{
					if ($this->documentType == Afip_Model_Enums_DocumentTypeEnum::UNKNOWN)
						$this->errors->add("El tipo de documento no puede ser '" . $documentHelper->getValueFor(Afip_Model_Enums_DocumentTypeEnum::UNKNOWN) . "' para facturas, notas de crédito o notas de débito de tipo B con un monto mayor o igual a $1.000.");
					
					if ($this->documentNumber == 0)
						$this->errors->add("El número de documento debe estar definido para facturas, notas de crédito y débito de tipo B con un monto mayor o igual a $1.000");
				}
				else
				{
					if ($this->documentType == Afip_Model_Enums_DocumentTypeEnum::UNKNOWN)
					{
						if ($this->documentNumber != 0)
							$this->errors->add(
								"El número de documento debe ser 0 (cero) para facturas, notas de crédito o notas de débito de tipo B con monto inferior a $1.000 y tipo de documento igual a '" .
								$documentHelper->getValueFor(Afip_Model_Enums_DocumentTypeEnum::UNKNOWN) . "'."
							);
					}
					else
					{
						if ($this->documentNumber == 0)
							$this->errors->add(
								"El número de documento debe ser mayor a 0 (cero) para facturas, notas de crédito o notas de débito de tipo B con monto inferior a $1.000 y tipo de documento distinto de '" .
								$documentHelper->getValueFor(Afip_Model_Enums_DocumentTypeEnum::UNKNOWN) . "'."
							);
					}
				}
			}
			
			if (Afip_Model_Enums_TypeEnum::canHasBillingTarget($this->invoiceType))
			{
				if ($this->hasBillingTarget())
				{
					if ($this->getBillingTarget()->isValid())
					{
						if (!Afip_Model_Enums_TypeEnum::areCompatible($this->getInvoiceType(), $this->getBillingTarget()->getType()))
							$this->errors->add("El tipo del comprobante a autorizar y el comprobante asociado no son compatibles.");
					}
					else
						$this->errors->addFrom($this->getBillingTarget()->getErrors());
				}
				else
					$this->errors->add("Las notas de crédito y/o débito deben estar asociadas a un comprobante");
			}
			else
			{
				if ($this->hasBillingTarget())
					$this->errors->add("Una factura no puede tener un comprobante asociado: únicamente las notas de crédito y/o débito pueden tener uno.");
			}
		}
	}
	
?>