<?php
	
	/**
	 * Helper for alicuotas collection for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class AlicuotaCollector implements Iterator, Countable
	{
		/* Constants and Variables */
		
		/**
		 * The number of elements in repository.
		 * @var int
		 */
		protected $count;
		
		/**
		 * The errors.
		 * @var ErrorCollectionHelper instance
		 */
		protected $errors;
		
		/**
		 * XXX
		 * @var array
		 */
		protected $flags;
		
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
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new instance.
		 *
		 * @return AlicuotaCollector instance
		 */
		public static function getInstance()
		{
			return new self();
		}
		
		
		public function __construct()
		{
			$this->count = 0;
			$this->errors = ErrorCollectionHelper::getInstance();
			
			$this->flags = array();
			$this->flags[TaxTypeEnum::IVA_0000] = NULL;
			$this->flags[TaxTypeEnum::IVA_0250] = NULL;
			$this->flags[TaxTypeEnum::IVA_0500] = NULL;
			$this->flags[TaxTypeEnum::IVA_1050] = NULL;
			$this->flags[TaxTypeEnum::IVA_2100] = NULL;
			$this->flags[TaxTypeEnum::IVA_2700] = NULL;
			
			$this->repository = array();
			
			$this->rewind();
		}
		
		public function __toString()
		{
			return __CLASS__ . " (Items: {$this->count})";
		}
		
		
		/**
		 * Adds the given alicuota to collector.
		 *
		 * @param Alicuota $alicuota
		 * @return void
		 * @throws Afip_Exception_Lib_ClassMismatchException Throws an exception whether the given alicuota is not an instance of Alicuota.
		 */
		public function add($alicuota)
		{
			if (is_object($alicuota) && ($alicuota instanceof Alicuota))
			{
				if ($alicuota->isValid())
				{
					$storedAlicuota = $this->getStoredAlicuotaFor($alicuota);
					$storedAlicuota->setBaseAmount($alicuota->getBaseAmount() + $storedAlicuota->getBaseAmount());
					$storedAlicuota->setTaxAmount($alicuota->getTaxAmount() + $storedAlicuota->getTaxAmount());
				}
			}
			else
				ExceptionFactory::throwClassMismatch("Alicuota");
		}
		
		/**
		 * Transfors the inner information for AFIP Webservice (WSFE).
		 *
		 * @return array
		 */
		public function asWebserviceInput()
		{
			$input = array();
			
			$this->rewind();
			while ($this->valid())
			{
				$input["AlicIva"][] = $this->current()->asWebserviceInput();
				$this->next();
			}
			
			return $input;
		}
		
		/**
		 * Returns the number of stored alicuota.
		 *
		 * @see Countable::count()
		 * @return int
		 */
		public function count()
		{
			return $this->count;
		}
		
		/**
		 * Returns the current alicuota.
		 *
		 * @see Iterator::current()
		 * @return Alicuota instance
		 */
		public function current()
		{
			return $this->repository[$this->pointer];
		}
		
		/**
		 * Returns the errors.
		 *
		 * @return ErrorCollectionHelper instance
		 */
		public function getErrors()
		{
			return $this->errors;
		}
		
		public function getTaxAmount()
		{
			$amount = 0;
			
			if ($this->count != 0)
			{
				foreach($this->repository as $alicuota)
				{
					if ($alicuota->isValid())
						$amount += $alicuota->getTaxAmount();
					else
					{
						$amount = -1;
						$this->errors->addFrom($alicuota->getErrors());
					}
				}
			}
			
			$amount = NumberDataTypeHelper::truncate($amount, 2);
			
			return $amount;
		}
		
		/**
		 * Returns the key of the current alicuota.
		 *
		 * @see Iterator::key()
		 * @return int
		 */
		public function key()
		{
			return $this->pointer;
		}
		
		/**
		 * Moves forward it to next alicuota.
		 *
		 * @see Iterator::next()
		 * @return void
		 */
		public function next()
		{
			$this->pointer++;
		}
		
		/**
		 * Rewinds it to the first alicuota.
		 *
		 * @see Iterator::rewind()
		 * @return void
		 */
		public function rewind()
		{
			$this->pointer = 0;
		}
		
		/**
		 * Checks whether current position is valid.
		 *
		 * @see Iterator::valid()
		 * @return boolean
		 */
		public function valid()
		{
			return ($this->pointer < $this->count);
		}
		
		
		
		/* Protected methods */
		
		/**
		 * Returns the stored alicuota for given alicuota.
		 *
		 * @param Alicuota $alicuota
		 * @return Alicuota instance
		 */
		protected function getStoredAlicuotaFor($alicuota)
		{
			$flag = $alicuota->getTaxType();
			$class = get_class($alicuota);
			
			if ($this->flags[$flag] === NULL)
			{
				$this->flags[$flag] = $this->count;
				$this->count++;
			}
			
			if (!array_key_exists($this->flags[$flag], $this->repository))
				$this->repository[$this->flags[$flag]] = new $class();
			
			return $this->repository[$this->flags[$flag]];
		}
	}
	
?>