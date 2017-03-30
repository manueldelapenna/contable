<?php
	
	/**
	 * Abstract enumerative for AFIP Invoice Manager.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	abstract class Quanbit_Afip_Model_Enums_AbstractEnum
	{
		/* Public methods */
		
		/**
		 * Returns the default key.
		 *
		 * @return mixed
		 */
		abstract public function getDefaultKey();
		
		/**
		 * Returns the default value.
		 *
		 * @return mixed
		 */
		public function getDefaultValue()
		{
			return $this->getValueFor($this->getDefaultKey());
		}
		
		/**
		 * Returns the key for given value.
		 *
		 * @param mixed $value
		 * @return mixed | NULL
		 */
		public function getKeyFor($value)
		{
			if ($this->isValidValue($value))
				return array_search($value, $this->getList());
			else
				return NULL;
		}
		/**
		 * Returns the item collection of enumerative.
		 *
		 * @return array
		 */
		abstract public function getList();
		
		/**
		 * Returns the value for given key.
		 *
		 * @param mixed $key
		 * @return mixed | NULL
		 */
		public function getValueFor($key)
		{
			if ($this->isValidKey($key))
			{
				$tmp = $this->getList();
				return $tmp[$key];
			}
			else
				return NULL;
		}
		
		/**
		 * Indicates whether given key is valid or not.
		 *
		 * @param mixed $key
		 * @return boolean
		 */
		public function isValidKey($key)
		{
			return array_key_exists($key, $this->getList());
		}
		
		/**
		 * Indicates whether given value is valid or not.
		 *
		 * @param mixed $key
		 * @return boolean
		 */
		public function isValidValue($value)
		{
			if (is_string($value))
				$value = trim($value);
			
			return in_array($value, $this->getList());
		}
		
		/**
		 * Validates the given key.
		 *
		 * @param mixed $key
		 * @return void
		 * @throws Quanbit_Afip_Exception_Lib_Exception Throws an exception whether the given key it is invalid.
		 */
		public function validateKey($key)
		{
			if (!$this->isValidKey($key))
				Quanbit_Afip_Exception_ExceptionFactory::throwFor("The given {$this->getNameForPeople()} is not a valid option: <$key>.");
		}
		
		
		
		/* Protected methods */
		
		/**
		 * Returns the name of enumerative, readable for people.
		 */
		abstract protected function getNameForPeople();
	}
	
?>