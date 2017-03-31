<?php
	
	/**
	 * Helper for invoice data collection for AFIP Invoice Manager.
	 *
	 
	 * @author Eduardo Casey
	 */
	class AfipInvoiceDataCollector implements Iterator, Countable
	{
		/* Constants and Variables */
		
		/**
		 * The number of elements in repository.
		 * @var int
		 */
		protected $count;
		
		/**
		 * The stored data type.
		 * @var int
		 */
		protected $invoiceType;
		
		/**
		 * The stored data type name.
		 * @var string
		 */
		protected $invoiceTypeName;
		
		/**
		 * The current position in repository.
		 * @var int
		 */
		protected $pointer;
		
		/**
		 * The stored data.
		 * @var array
		 */
		protected $repository;
		
		/**
		 * The status of AFIP operation.
		 * @var string
		 */
		protected $status;
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new instance.
		 *
		 * @param int $type The type of stored invoice data (e.g. TypeEnum::A).
		 * @return AfipInvoiceDataCollector instance
		 */
		public static function getInstance($type)
		{
			return new self($type);
		}
		
		
		public function __construct($type)
		{
			$helper = TypeEnum::getInstance();
			$helper->validateKey($type);
			
			$this->status = AuthorizationStatusEnum::SCHEDULED;
			$this->count = 0;
			$this->invoiceType = $type;
			$this->invoiceTypeName = $helper->getValueFor($this->invoiceType);
			$this->repository = array();
			
			$this->rewind();
		}
		
		public function __toString()
		{
			return __CLASS__ . " ({$this->invoiceTypeName}: {$this->count})";
		}
		
		
		/**
		 * Adds the given invoice data to collector.
		 *
		 * @param AfipInvoiceData $data
		 * @return void
		 * @throws Afip_Exception_Lib_ClassMismatchException Throws an exception whether the given invoice data is not an instance of AfipInvoiceData.
		 * @throws Afip_Exception_Lib_Exception Throws an exception whether the given invoice data type mismatch with collector type.
		 */
		public function add(AfipInvoiceData $data)
		{
			if (is_object($data) && ($data instanceof AfipInvoiceData))
			{
				if ($data->getInvoiceType() == $this->invoiceType)
				{
					$this->repository[] = $data;
					$this->count++;
				}
				else
					ExceptionFactory::throwFor("The type of given invoice data does not match with the type of collector. Expected <{$this->invoiceTypeName}>.");
			}
			else
				ExceptionFactory::throwClassMismatch("AfipInvoiceData");
		}
		
		/**
		 * Returns the number of stored invoice data.
		 *
		 * @return int
		 */
		public function count()
		{
			return $this->count;
		}
		
		/**
		 * Returns the current invoice data.
		 *
		 * @return AfipInvoiceData instance
		 */
		public function current()
		{
			return $this->repository[$this->pointer];
		}
		
		/**
		 * Returns the number of invoice data that are accepted.
		 *
		 * @return int
		 */
		public function getNumberOfAcceptedInvoiceData()
		{
			return $this->getNumberOfInvoiceDataInStatus(DataAuthorizationStatusEnum::ACCEPTED);
		}
		
		/**
		 * Returns the number of invoice data that are invalid.
		 *
		 * @return int
		 */
		public function getNumberOfInvalidInvoiceData()
		{
			return $this->getNumberOfInvoiceDataInStatus(DataAuthorizationStatusEnum::INVALID);
		}
		
		/**
		 * Returns the number of invoice data that are rejected.
		 *
		 * @return int
		 */
		public function getNumberOfRejectedInvoiceData()
		{
			return $this->getNumberOfInvoiceDataInStatus(DataAuthorizationStatusEnum::REJECTED);
		}
		
		/**
		 * Returns the number of invoice data that are scheduled.
		 *
		 * @return int
		 */
		public function getNumberOfScheduledInvoiceData()
		{
			return $this->getNumberOfInvoiceDataInStatus(DataAuthorizationStatusEnum::SCHEDULED);
		}
		
		/**
		 * Returns the number of invoice data that are valid.
		 *
		 * @return int
		 */
		public function getNumberOfValidInvoiceData()
		{
			return $this->getNumberOfInvoiceDataInStatus(DataAuthorizationStatusEnum::VALID);
		}
		
		/**
		 * Returns the status.
		 *
		 * @return string
		 */
		public function getStatus()
		{
			return $this->status;
		}
		
		/**
		 * Returns the status name.
		 *
		 * @return string
		 */
		public function getStatusName()
		{
			return AuthorizationStatusEnum::getInstance()->getValueFor($this->status);
		}
		
		/**
		 * Returns the invoice data type.
		 *
		 * @return int
		 */
		public function getType()
		{
			return $this->invoiceType;
		}
		
		/**
		 * Indicates whether its status is not an exception or no given result.
		 *
		 * @return boolean
		 */
		public function hasNormalEndingStatus()
		{
			return in_array($this->status, AuthorizationStatusEnum::normalEndingStatus());
		}
		
		/**
		 * Returns the key of the current invoice data.
		 *
		 * @return int
		 */
		public function key()
		{
			return $this->pointer;
		}
		
		/**
		 * Moves forward it to next invoice data.
		 *
		 * @return void
		 */
		public function next()
		{
			$this->pointer++;
		}
		
		/**
		 * Rewinds it to the first invoice data.
		 *
		 * @return void
		 */
		public function rewind()
		{
			$this->pointer = 0;
		}
		
		/**
		 * Sets the status of AFIP operation.
		 *
		 * @param mixed $status
		 * @throws Afip_Exception_Lib_Exception
		 */
		public function setStatus($status)
		{
			AuthorizationStatusEnum::getInstance()->validateKey($status);
			$this->status = $status;
		}
		
		/**
		 * Checks whether current position is valid.
		 *
		 * @return boolean
		 */
		public function valid()
		{
			return ($this->pointer < $this->count);
		}
		
		/**
		 * XXX
		 * 
		 * @param int $lastAuthorizedNumber
		 * @return void
		 */
		public function assignInvoiceNumbersFrom($lastAuthorizedNumber)
		{
			$this->rewind();
			
			while ($this->valid())
			{
				if ($this->current()->isValid())
				{
					$lastAuthorizedNumber++;
					$this->current()->setInvoiceNumber($lastAuthorizedNumber);
				}

				$this->next();
			}
			
			$this->rewind();
		}
		
		
		
		/* Protected methods */
		
		/**
		 * Returns the number of invoice data in the given status.
		 *
		 * @param int $status
		 * @return int
		 */
		protected function getNumberOfInvoiceDataInStatus($status)
		{
			$counter = 0;
			
			for ($x = 0; $x < $this->count; $x++)
			{
				if ($this->repository[$x]->getStatus() == $status)
					$counter++;
			}
			
			return $counter;
		}
	}
	
?>