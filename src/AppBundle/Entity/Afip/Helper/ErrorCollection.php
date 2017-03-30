<?php
	
	/**
	 * Class for error list.
	 * 
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class Quanbit_Afip_Helper_ErrorCollection extends Mage_Core_Helper_Abstract
	{
		protected $errors;
		
		
		
		/**
		 * Returns a new instance.
		 * 
		 * @return Quanbit_Afip_Helper_ErrorCollection object
		 */
		public static function getInstance()
		{
			return new self();
		}
		
		/**
		 * Constructor.
		 * 
		 * @return Quanbit_Afip_Helper_ErrorCollection object
		 */
		public function __construct()
		{
			$this->clean();
		}
		
		
		/**
		 * Returns the entry with the given key.
		 * 
		 * @param string $key
		 * @return string | NULL
		 */
		public function getEntry($key)
		{
			$key = trim($key);
			return (($key == "") ? NULL : @$this->errors[$key]);
		}
		
		/**
		 * Returns the errors.
		 * 
		 * @return array
		 */
		public function getList()
		{
			return $this->errors;
		}
		
		/**
		 * Returns the errors as array of strings.
		 * 
		 * @return string | NULL
		 */
		public function getListAsString()
		{
			if ($this->getCount() > 0)
			{
				$aux = array();
				foreach ($this->errors as $key => $value)
				{
					if (is_string($key))
						$aux[] = "* {" . $key . "} $value";
					else
						$aux[] = "* $value";
				}
				
				return implode("\n", $aux);
			}
			else
				return NULL;
		}
		
		/**
		 * Returns the number of errors.
		 * 
		 * @return int
		 */
		public function getCount()
		{
			return count($this->getList());
		}
		
		/**
		 * Indicates whether there aren't errors.
		 * 
		 * @return boolean
		 */
		public function isEmpty()
		{
			return ($this->getCount() == 0);
		}
		
		
		/**
		 * Adds an error.
		 * 
		 * @param string $value
		 * @param string $key [Default: NULL]
		 * @return void
		 */
		public function add($value, $key = NULL)
		{
			if (is_string($key))
			{
				$key = trim($key);
				if ($key == "")
					$key = NULL;
			}
			else
				$key = NULL;
			
			if ($key)
				$this->errors[$key] = trim(strval($value));
			else
				$this->errors[] = trim(strval($value));
		}
		
		/**
		 * Adds errors form given Quanbit_Afip_Helper_ErrorCollection class.
		 * 
		 * @param Quanbit_Afip_Helper_ErrorCollection $errorCollection
		 * @return void
		 */
		public function addFrom(Quanbit_Afip_Helper_ErrorCollection $errorCollection)
		{
			if (!$errorCollection->isEmpty())
			{
				foreach($errorCollection->getList() as $error)
					$this->add($error);
			}
		}
		
		/**
		 * Cleans the errors.
		 * 
		 * @return void.
		 */
		public function clean()
		{
			$this->errors = array();
		}
	}
	
?>