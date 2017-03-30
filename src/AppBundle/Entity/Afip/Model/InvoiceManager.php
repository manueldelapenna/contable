<?php
	
	/**
	 * Helper for AFIP authorization of invoices.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */

	final class InvoiceManager
	{
		/* Constants and Variables */
		
		/**
		 * The access ticket for webservice (WSFE).
		 * @var array | NULL
		 */
		protected $accessTicket ;
		
		/**
		 * The life of access ticket for webservice (WSFE), in seconds since the Unix Epoch.
		 * @var int | NULL
		 */
		protected $accessTicketLife;
		
		/**
		 * The SOAP Client for webservice (WSFE).
		 * @var SoapClient instance | NULL
		 */
		protected $wsfeClient;
		
		/**
		 * The environment.
		 * @var Environment instance | NULL
		 */
		protected $environment;
		
		/**
		 * Indicates whether it is authenticated on webservice (WSAA).
		 * @var boolean
		 */
		protected $isAuthenticated;
		
		/**
		 * The logger.
		 * @var FileLoggerHelper instance | NULL
		 */
		protected $logger;
		
		/**
		 * The last result of webservice operation.
		 * @var stdClass
		 */
		protected $lastResult;
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new instance.
		 *
		 * @param Environment $environment
		 * @param FileLoggerHelper $logger
		 * @return InvoiceManager instance
		 */
		public static function getInstance($environment, $logger)
		{
			$temp = new self();
			$temp->setEnvironment($environment);
			$temp->setLogger($logger);
			$temp->authenticate();
			$temp->login();
			
			return $temp;
		}
		
		public function __construct()
		{
			$this->accessTicket = NULL;
			$this->accessTicketLife = NULL;
			$this->environment = NULL;
			$this->isAuthenticated = false;
			$this->logger = NULL;
			$this->wsfeClient = NULL;
		}
		
		public function __destruct()
		{
			$this->logger->close();
		}
		
		public function __toString()
		{
			return __CLASS__ . " (Environment: " . (isset($this->environment) ? $this->environment : "not defined") . ")";
		}
		
		
		/**
		 * Authenticates with AFIP.
		 *
		 * @return void
		 */
		public function authenticate()
		{
			if (!$this->isAuthenticated)
			{
				ini_set("soap.wsdl_cache_enabled", "0");
				
				$this->exceptionIfNoEnvironment();
				
				$this->loadAccessTicket();
				if ($this->isAccessTicketExpired())
					$this->generateAccessTicket();
				
				$this->isAuthenticated = true;
			}
		}
		
		/**
		 * Authorizes the given billing data on Webservice (WSFE).
		 *
		 * @param AfipInvoiceDataCollector $collector
		 * @return void
		 * @throws Afip_Exception_Lib_Exception Throws an exception when Webservice (WSFE) is not running or given collector is not an instance of AfipInvoiceDataCollector.
		 */
		public function authorize($collector)
		{
			$this->exceptionIfNoWebservice();
			$this->exceptionIfClassMismatch($collector, "AfipInvoiceDataCollector");
			
			$this->log("BEGIN", __FUNCTION__);
			
			try
			{
				if ($collector->count() > 0)
				{
					/* Initializing the new block on logger. */
					
					$this->log(" * Environment: " . $this->environment->getEnvironmentKey(), __FUNCTION__);
					$this->log(" * Point of Sales: " . $this->environment->getPointOfSale(), __FUNCTION__);
					$this->log(" * Taxpayer CUIT: " . $this->environment->getTaxpayerCuit(), __FUNCTION__);
					$this->log(" * Type: " . TypeEnum::getInstance()->getValueFor($collector->getType()), __FUNCTION__);
					
					
					/* Retrieving last authorized number from Webservice (WSFE). */
					
					$lastAuthorizedNumber = $this->getLastAcceptedNumberFor($collector->getType());
					$this->log(" * Last authorized number (in AFIP): $lastAuthorizedNumber", __FUNCTION__);
					
					
					/* Processing data to be sent to Webservice (WSFE). */
					
					$billingData = $this->prepareBillingData($collector);
					$countBillingData = count($billingData);
					
					$this->log(" * Data in collector: " . $collector->count(), __FUNCTION__);
					$this->log("   |_ valid:   $countBillingData", __FUNCTION__);
					$this->log("   |_ invalid: " . ($collector->count() - $countBillingData), __FUNCTION__);
					
					if ($countBillingData == 0)
					{
						/* Aborting process because there are no valid data. */
						
						$this->log(" * No data were sent to AFIP.", __FUNCTION__);
						$collector->setStatus(AuthorizationStatusEnum::NO_VALID_DATA);
					}
					else
					{
						/* Preparing data to be sent to Webservice (WSFE). */
						
						$operationData = array();
						$operationData["Auth"] = $this->getAuthKey();
						$operationData["FeCAEReq"]["FeCabReq"] = array("CantReg" => $countBillingData, "PtoVta" => $this->environment->getPointOfSale(), "CbteTipo" => $collector->getType());
						$operationData["FeCAEReq"]["FeDetReq"] = $billingData;
						
						
						/* Sending data to Webservice (WSFE). */
						
						$this->log(" * Requested number range: from " . ($lastAuthorizedNumber + 1) . " to " . ($lastAuthorizedNumber + $countBillingData), __FUNCTION__);
						$this->log(" * Sending data to AFIP...", __FUNCTION__);
						$this->log("   |_ request (base64): {$this->encodeForLogger($operationData)}", __FUNCTION__);
						
						$this->lastResult = $this->wsfeClient->FECAESolicitar($operationData);
						
						$this->log("   |_ response (base64): {$this->encodeForLogger($this->lastResult)}", __FUNCTION__);
						
						
						/* Processing the given result from Webservice (WSFE). */
						
						try
						{
							$this->exceptionIfOperationHasErrors($this->lastResult);
							$status = $this->lastResult->FECAESolicitarResult->FeCabResp->Resultado;
						}
						catch (Afip_Exception_Lib_SoapFaultException $e)
						{
							$status = AuthorizationStatusEnum::NO_RESULT_GIVEN;
						}
						
						$this->log(" * Authorization result: " . AuthorizationStatusEnum::getInstance()->getValueFor($status), __FUNCTION__);
						
						if ($status == AuthorizationStatusEnum::NO_RESULT_GIVEN)
						{
							/* Updating the status of collection. */
							$collector->setStatus(AuthorizationStatusEnum::NO_RESULT_GIVEN);
						}
						else
						{
							/* Updating the status of collection and its billing data. */
							
							$collector->setStatus($status);
							
							$result = $this->lastResult->FECAESolicitarResult->FeDetResp->FECAEDetResponse;
							
							if (!is_array($result))
								$result = array($result);
							
							$this->updateBillingData($collector, $result);
							
							$this->log(" * Status of data in collector:", __FUNCTION__);
							$this->log("   |_ accepted: " . $collector->getNumberOfAcceptedInvoiceData(), __FUNCTION__);
							$this->log("   |_ rejected: " . $collector->getNumberOfRejectedInvoiceData(), __FUNCTION__);
							$this->log("   |_ valid:    " . $collector->getNumberOfValidInvoiceData(), __FUNCTION__);
							$this->log("   |_ invalid:  " . $collector->getNumberOfInvalidInvoiceData(), __FUNCTION__);
						}
					}
					
					unset($lastAuthorizedNumber, $billingData, $countBillingData, $operationData, $status);
				}
			}
			catch (Exception $e)
			{
				/* Updating the status of collection on caught exception. */
				
				$collector->setStatus(AuthorizationStatusEnum::EXCEPTION);
				
				$this->log("Exception detected -> " . $e->getMessage(), __FUNCTION__);
				$this->log("  |_ content (base64): {$this->encodeForLogger($e)}", __FUNCTION__);
			}
			
			$this->log("END", __FUNCTION__);
		}
		
		/**
		 * Returns a collection of currencies from Webservice.
		 *
		 * @return array
		 * @throws Afip_Exception_Lib_Exception
		 */
		public function getCurrencyCollection()
		{
			return $this->getCollectionFor("FEParamGetTiposMonedas");
		}
		
		/**
		 * Returns a collection of document types from Webservice.
		 *
		 * @return array
		 * @throws Afip_Exception_Lib_Exception
		 */
		public function getDocumentTypeCollection()
		{
			return $this->getCollectionFor("FEParamGetTiposDoc");
		}
		
		/**
		 * Returns a collection of invoice types from Webservice.
		 *
		 * @return array
		 * @throws Afip_Exception_Lib_Exception
		 */
		public function getInvoiceTypeCollection()
		{
			return $this->getCollectionFor("FEParamGetTiposCbte");
		}
		
		/**
		 * Returns the last authorized invoice number for given invoice type.
		 *
		 * @param int $type
		 * @return int
		 * @throws Afip_Exception_Lib_Exception
		 */
		public function getLastAcceptedNumberFor($type)
		{
			$this->exceptionIfNoWebservice();
			
			TypeEnum::getInstance()->validateKey($type);
			
			$this->lastResult = $this->wsfeClient->FECompUltimoAutorizado(array("Auth" => $this->getAuthKey(), "PtoVta" => $this->environment->getPointOfSale(), "CbteTipo" => $type));
			$this->exceptionIfOperationHasErrors($this->lastResult);
			
			return $this->lastResult->FECompUltimoAutorizadoResult->CbteNro;
		}
		
		/**
		 * Returns the associated logger.
		 *
		 * @return FileLoggerHelper
		 */
		public function getLogger()
		{
			return $this->logger;
		}
		
		/**
		 * Returns a collection of tax types from Webservice.
		 *
		 * @return array
		 * @throws Afip_Exception_Lib_Exception
		 */
		public function getTaxTypeCollection()
		{
			return $this->getCollectionFor("FEParamGetTiposIva");
		}
		
		/**
		 * Returns the maximun number of invoices data per request to Webservice (WSFE).
		 *
		 * @return int
		 */
		public function getSendingDataLimit()
		{
			$this->exceptionIfNoWebservice();
			
			$this->lastResult = $this->wsfeClient->FECompTotXRequest(array("Auth" => $this->getAuthKey()));
			$this->exceptionIfOperationHasErrors($this->lastResult);
			
			return $this->lastResult->FECompTotXRequestResult->RegXReq;
		}
		
		/**
		 * Returns a report about its status in AFIP (WSFE).
		 *
		 * @return array
		 */
		public function getStatusReport()
		{
			$report = array();
			
			$report["environment"] = $this->environment->getEnvironmentKey();
			$report["pointOfSales"] = $this->environment->getPointOfSale();
			$report["taxpayerCuit"] = $this->environment->getTaxpayerCuit();
			$report["sendingDataLimit"] = $this->getSendingDataLimit();
			$report["lastNumber"]["invoiceA"] = $this->getLastAcceptedNumberFor(TypeEnum::A);
			$report["lastNumber"]["invoiceB"] = $this->getLastAcceptedNumberFor(TypeEnum::B);
			$report["lastNumber"]["creditNoteA"] = $this->getLastAcceptedNumberFor(TypeEnum::A_CREDIT_NOTE);
			$report["lastNumber"]["creditNoteB"] = $this->getLastAcceptedNumberFor(TypeEnum::B_CREDIT_NOTE);
			$report["lastNumber"]["debitNoteA"] = $this->getLastAcceptedNumberFor(TypeEnum::A_DEBIT_NOTE);
			$report["lastNumber"]["debitNoteB"] = $this->getLastAcceptedNumberFor(TypeEnum::B_DEBIT_NOTE);
			
			return $report;
		}
		
		/**
		 * Indicates whether it is authenticated on webservice.
		 *
		 * @return boolean
		 */
		public function isAuthenticated()
		{
			return $this->isAuthenticated;
		}
		
		/**
		 * Indicates whether it is logged on webservice.
		 *
		 * @return boolean
		 */
		public function isLoggedIn()
		{
			return ($this->wsfeClient !== NULL);
		}
		
		/**
		 * Indicates whether the webservice is running.
		 *
		 * @return boolean
		 */
		public function isWebserviceRunning()
		{
			$this->lastResult = $this->wsfeClient->FEDummy();
			$this->exceptionIfOperationHasErrors($this->lastResult);
			
			if ($this->lastResult)
				return (($this->lastResult->FEDummyResult->AppServer == "OK") && ($this->lastResult->FEDummyResult->DbServer == "OK") && ($this->lastResult->FEDummyResult->AuthServer == "OK"));
			else
				return false;
		}
		
		/**
		 * Logins with AFIP.
		 *
		 * @return void
		 */
		public function login()
		{
			if ($this->isAuthenticated())
			{
				if (!$this->isLoggedIn())
				{
					$this->wsfeClient =
						new SoapClient($this->environment->getWsfeFilePath(), array("soap_version" => SOAP_1_2, "location" => $this->environment->getWsfeUrl(), "exceptions" => 0, "trace" => 1));
					
					if ($this->wsfeClient === NULL)
						Afip_Exception_ExceptionFactory::throwFor("Cannot create a new SoapClient instance.");
				}
			}
			else
				Afip_Exception_ExceptionFactory::throwFor("Cannot login when it is not authenticated.");
		}
		
		/**
		 * Retrieves the billing data on Webservice (WSFE) for given parameters.
		 *
		 * @param int $type The billing type.
		 * @param int $number The billing number.
		 * @return stdClass
		 */
		public function retrieveDataFor($type, $number)
		{
			$this->exceptionIfNoWebservice();
			TypeEnum::getInstance()->validateKey($type);
			
			$operationData = array();
			$operationData["Auth"] = $this->getAuthKey();
			$operationData["FeCompConsReq"] = array("CbteTipo" => $type, "CbteNro" => $number, "PtoVta" => $this->environment->getPointOfSale());
			
			$this->lastResult = $this->wsfeClient->FECompConsultar($operationData);
			$this->exceptionIfOperationHasErrors($this->lastResult);
			
			$result = $this->lastResult->FECompConsultarResult->ResultGet;
			
			return $result;
		}
		
		/**
		 * Sets the environment.
		 *
		 * @param Environment $environment
		 * @return void
		 * @throws Afip_Exception_Lib_Exception Throws an exception whether the given environment is invalid.
		 */
		public function setEnvironment($environment)
		{
			if ($this->environment === NULL)
			{
				$this->exceptionIfClassMismatch($environment, "Environment");
				$this->environment = $environment;
			}
		}
		
		/**
		 * Sets the logger.
		 *
		 * @param FileLoggerHelper $logger
		 * @return void
		 * @throws Afip_Exception_Lib_Exception Throws an exception whether the given logger is invalid.
		 */
		public function setLogger($logger)
		{
			if ($this->logger === NULL)
			{
				$this->exceptionIfClassMismatch($logger, "FileLoggerHelper");
				
				$this->logger = $logger;
				$this->logger->open();
			}
		}
		
		
		
		/* Protected methods */
		
		/**
		 * Authorizates the access ticket with AFIP.
		 *
		 * @param string $cmsContent
		 * @param string &$output
		 * @return void
		 */
		protected function authorizateAccessTicket($cmsContent, &$output)
		{
			$context =
				stream_context_create(array("ssl" => array("CN_match" => $this->environment->getWsaaCommonName(), "cafile" => $this->environment->getAfipCertificate(),
				                                           "allow_self_signed" => false, "verify_peer" => false)));
			
			$wsaaClient = new SoapClient($this->environment->getWsaaFilePath(), array("stream_context" => $context, "soap_version" => SOAP_1_2, "location" => $this->environment->getWsaaUrl(), "exceptions" => 0));
			
			$results = $wsaaClient->loginCms(array("in0" => $cmsContent));
			$this->exceptionIfOperationHasErrors($results);
			
			$output = $results->loginCmsReturn;
			
			unset($context, $wsaaClient, $results);
		}
		
		/**
		 * Creates and saves a temporary ticket with given filename on disk.
		 *
		 * @param string $tempTicketFilename
		 * @return void
		 */
		protected function createAccessTicket($tempTicketFilename)
		{
			$dom = new DOMDocument("1.0", "UTF-8");
			$dom->appendChild($dom->createElement("loginTicketRequest"));
			
			$date = date("U");
			
			$domChild = $dom->createElement("header");
			$domChild->appendChild($dom->createElement("uniqueId", $date));
			$domChild->appendChild($dom->createElement("generationTime", date("c", $date - 600)));
			$domChild->appendChild($dom->createElement("expirationTime", date("c", $date + 600)));
			
			$dom->documentElement->appendChild($domChild);
			$dom->documentElement->appendChild($dom->createElement("service", "wsfe"));
			$dom->save($tempTicketFilename);
			
			unset($dom, $domChild, $date);
		}
		
		/**
		 * Serializes and encodes (on base64) the given source and returns as string.
		 *
		 * @param mixed $source
		 * @return string
		 */
		protected function encodeForLogger($source)
		{
			return base64_encode(serialize($source));
		}
		
		/**
		 * Throws an exception whether given source is not an instance of given class name.
		 *
		 * @param object $source
		 * @param string $className
		 * @return void
		 * @throws Afip_Exception_Lib_ClassMismatchException
		 */
		protected function exceptionIfClassMismatch($source, $className)
		{
			if (!(is_object($source) && ($source instanceof $className)))
				Afip_Exception_ExceptionFactory::throwClassMismatch($className);
		}
		
		/**
		 * Throws an exception whether the Webservices (WSFE) is not logged in or it is not running.
		 *
		 * @return void
		 * @throws Afip_Exception_Lib_Exception
		 */
		protected function exceptionIfNoWebservice()
		{
			if ($this->isLoggedIn())
			{
				if (!$this->isWebserviceRunning())
					Afip_Exception_ExceptionFactory::throwFor("Webservice is not running.");
			}
			else
				Afip_Exception_ExceptionFactory::throwFor("Must be logged into Webservice before continuous.");
		}
		
		/**
		 * Throws an exception whether there is not an environment.
		 *
		 * @return void
		 * @throws Afip_Exception_Lib_ClassMismatchException
		 */
		protected function exceptionIfNoEnvironment()
		{
			$this->exceptionIfClassMismatch($this->environment, "Environment");
		}
		
		/**
		 * Throws an exception whether given object is a SOAP fault or it has errors from Webservice (WSAA or WSFE).
		 *
		 * @param $source
		 * @return void
		 * @throws Afip_Exception_Lib_SoapFaultException
		 * @throws Afip_Exception_Lib_Exception
		 */
		protected function exceptionIfOperationHasErrors($source)
		{
			if (is_soap_fault($source))
				Afip_Exception_ExceptionFactory::throwSoapFaultException($source);
			else
			{
				if (is_object($source) && ($source instanceof stdClass))
				{
					$operation = array_keys((array) $source);
					if (isset($source->$operation[0]->Errors))
					{
						$errors = array();
						foreach ($source->$operation[0]->Errors as $error)
							$errors[$error->Code] = $this->normalizeStringValueForHtml($error->Msg);
						
						$errors = implode(" || ", $errors);
						Afip_Exception_ExceptionFactory::throwFor("There are Webservice errors: <$errors>.");
					}
				}
			}
		}
		
		/**
		 * Generates a new access ticket.
		 *
		 * @return void
		 */
		protected function generateAccessTicket()
		{
			$tmpTicketFilename = $this->environment->getTempPathFor("temp.accessTicket.xml");
			
			$this->createAccessTicket($tmpTicketFilename);
			$this->signAccessTicket($tmpTicketFilename, $cmsContent);
			$this->authorizateAccessTicket($cmsContent, $newAccessTicketContent);
			
			
			$tempFile = fopen($this->environment->getAccessTicketFilePath(), "w");
			fwrite($tempFile, $newAccessTicketContent);
			fclose($tempFile);
			
			$this->readAccessTicket($newAccessTicketContent);
			
			unlink($tmpTicketFilename);
			unset($tmpTicketFilename, $cmsContent, $newAccessTicketContent, $tempFile);
		}
		
		/**
		 * Returns the key for operations on webservice (WSFE).
		 *
		 * @return array
		 */
		protected function getAuthKey()
		{
			return array("Token" => $this->accessTicket["token"], "Sign" => $this->accessTicket["sign"], "Cuit" => $this->environment->getTaxpayerCuit());
		}
		
		/**
		 * Returns a collection data for given webservice operation (WSFE).
		 *
		 * @param string $operation
		 * @return array
		 * @throws Afip_Exception_Lib_Exception
		 */
		protected function getCollectionFor($operation)
		{
			$this->exceptionIfNoWebservice();
			
			$operation = trim((string) $operation);
			if ($operation == "")
				Afip_Exception_ExceptionFactory::throwFor("Cannot retrieve collection data. Null operation name given.");
			
			$this->lastResult = $this->wsfeClient->$operation(array("Auth" => $this->getAuthKey()));
			$this->exceptionIfOperationHasErrors($this->lastResult);
			
			$collection = array();
			$operationResult = $operation . "Result";
			$result = $this->lastResult->$operationResult->ResultGet;
			$entry = array_keys((array) $result);
			
			foreach ($result->$entry[0] as $current)
				$collection[$current->Id] = $this->normalizeStringValueForHtml($current->Desc);
			
			ksort($collection);
			
			return $collection;
		}
		
		/**
		 * Indicates whether the access ticket is expired.
		 *
		 * @return boolean
		 */
		protected function isAccessTicketExpired()
		{
			return (($this->accessTicket == NULL) || ((time() - $this->accessTicketLife) >= 0));
		}
		
		/**
		 * Loads the currente access ticket from disk.
		 *
		 * @return boolean
		 */
		protected function loadAccessTicket()
		{
			if (file_exists($this->environment->getAccessTicketFilePath()))
			{
				$contents = file_get_contents($this->environment->getAccessTicketFilePath());
				if ($contents)
				{
					$this->readAccessTicket(file_get_contents($this->environment->getAccessTicketFilePath()));
					return true;
				}
			}
			
			return false;
		}
		
		/**
		 * Adds a new record into inner logger.
		 *
		 * @param string $line
		 * @param string $namespace
		 * @return void
		 */
		protected function log($line, $namespace)
		{
			$this->logger->addRecord($line, "[$namespace]");
		}
		
		/**
		 * Normalizes the given string value and returns a new one for HTML format.
		 *
		 * @param string $value
		 * @return string
		 */
		protected function normalizeStringValueForHtml($value)
		{
			return htmlentities(utf8_decode(utf8_decode($value)));
		}
		
		/**
		 * Processes the billing data in given collector and returns a collection of valid billing data with their invoice numbers for Webservice (WSFE).
		 *
		 * @param AfipInvoiceDataCollector $collector
		 * @return array
		 */
		protected function prepareBillingData($collector)
		{
			$validData = array();
			
			$collector->rewind();
			
			while ($collector->valid())
			{
				if ($collector->current()->isValid())
					$validData[] = $collector->current()->asWebserviceInput($this->environment->getPointOfSale());
				
				$collector->next();
			}
			
			return $validData;
		}
		
		/**
		 * Reads the access ticket.
		 *
		 * @param string $xml
		 * @return void
		 */
		protected function readAccessTicket($xml)
		{
			$xml = trim($xml);
			
			if ($xml != "")
			{
				$dom = new DOMDocument("1.0", "UTF-8");
				if ($dom->loadXML($xml, LIBXML_NOBLANKS))
				{
					$expirationTime = $dom->getElementsByTagName("expirationTime");
					$token = $dom->getElementsByTagName("token");
					$sign = $dom->getElementsByTagName("sign");
					
					if (($expirationTime->length > 0) && ($token->length > 0) && ($sign->length > 0))
					{
						$this->accessTicket = array("token" => $token->item(0)->nodeValue, "sign" => $sign->item(0)->nodeValue);
						$this->accessTicketLife = strtotime($expirationTime->item(0)->nodeValue);
						
						return;
					}
				}
			}
			
			$this->accessTicket = NULL;
			$this->accessTicketLife = NULL;
		}
		
		/**
		 * Signs the temporary ticket file (with given filename) with PKCS#7.
		 *
		 * @param string $tempTicketFilename
		 * @param string &$output
		 * @return void
		 */
		protected function signAccessTicket($tempTicketFilename, &$output)
		{
			$tempTicketFilenameDraff = "$tempTicketFilename-draff";
			$pkcs7 =
				openssl_pkcs7_sign($tempTicketFilename, $tempTicketFilenameDraff, "file://{$this->environment->getConsumerCertificate()}", array("file://{$this->environment->getConsumerPrivateKey()}", ""),
				                   array(), !PKCS7_DETACHED);
			
			if (!$pkcs7)
				Afip_Exception_ExceptionFactory::throwFor("Cannot generate a PKCS#7 signature.");
			
			$draffFile = fopen($tempTicketFilenameDraff, "r");
			$x = 0;
			$output = "";
			while (!feof($draffFile))
			{
				$buffer = fgets($draffFile);
				if ($x >= 4)
					$output .= $buffer;
				else
					$x++;
			}
			
			fclose($draffFile);
			
			unlink($tempTicketFilenameDraff);
			unset($tempTicketFilenameDraff, $pkcs7, $draffFile, $x, $buffer);
		}
		
		/**
		 * Updates billing data with the given result from Webservice (WSFE).
		 *
		 * @param AfipInvoiceDataCollector $collector
		 * @param array $resultCollection
		 * @return void
		 * @throws Afip_Exception_Lib_Exception Throws an exception whether the given collection of results is not an array or it is empty.
		 */
		protected function updateBillingData($collector, $resultCollection)
		{
			if (is_array($resultCollection) && (count($resultCollection) > 0))
			{
				$resultPointer = 0;
				
				$collector->rewind();
				while ($collector->valid())
				{
					if (($collector->current()->getStatus() != DataAuthorizationStatusEnum::INVALID) && ($collector->current()->getInvoiceNumber() == $resultCollection[$resultPointer]->CbteDesde))
					{
						if ($resultCollection[$resultPointer]->Resultado == DataAuthorizationStatusEnum::ACCEPTED)
							$collector->current()->accepted($resultCollection[$resultPointer]->CAE, $resultCollection[$resultPointer]->CAEFchVto, $resultCollection[$resultPointer]->CbteFch);
						elseif ($resultCollection[$resultPointer]->Resultado == DataAuthorizationStatusEnum::REJECTED)
						{
							$errors = array();
							
							if (property_exists($resultCollection[$resultPointer], 'Observaciones')){
								$observations = $resultCollection[$resultPointer]->Observaciones->Obs;
								
								if (isset($observations))
								{
									if (is_array($observations))
									{
										foreach ($observations as $current)
											$errors[] = "[" . $current->Code . "] " . $current->Msg;
									}
									else
										$errors[] = "[" . $observations->Code . "] " . $observations->Msg;
								}
							}
							
							if (count($errors) > 0)
								$collector->current()->rejected($errors);
						}
						
						$resultPointer++;
					}
					
					$collector->next();
				}
			}
			else
				Afip_Exception_ExceptionFactory::throwFor("The given collection of results is not an array or it is empty.");
		}
	}
	
?>