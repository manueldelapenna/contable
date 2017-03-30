<?php
	
	/**
	 * Helper for date types.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	class DateDataTypeHelper
	{
		/* Constants and Variables */
		
		/**
		 * @var int
		 */
		const LARGE = 0;

		/**
		 * @var int
		 */
		const SHORT = 1;
		
		
		/**
		 * @var int
		 */
		const CAPITAL = 0;
		
		/**
		 * @var int
		 */
		const LOWERCASE = 1;
		
		/**
		 * @var int
		 */
		const UPPERCASE = 2;
		
		
		/**
		 * @var int
		 */
		const JANUARY = 1;

		/**
		 * @var int
		 */
		const FEBRUARY = 2;

		/**
		 * @var int
		 */
		const MARCH = 3;

		/**
		 * @var int
		 */
		const APRIL = 4;

		/**
		 * @var int
		 */
		const MAY = 5;

		/**
		 * @var int
		 */
		const JUNE = 6;

		/**
		 * @var int
		 */
		const JULY = 7;

		/**
		 * @var int
		 */
		const AUGUST = 8;

		/**
		 * @var int
		 */
		const SEPTEMBER = 9;

		/**
		 * @var int
		 */
		const OCTOBER = 10;

		/**
		 * @var int
		 */
		const NOVEMBER = 11;
		
		/**
		 * @var int
		 */
		const DECEMBER = 12;
		
		
		/**
		 * The name of months in two versions: large and short.
		 * @var array
		 */
		protected static $monthsName = array(
			self::SHORT => array(
				self::JANUARY => "jan", self::FEBRUARY => "feb", self::MARCH => "mar", self::APRIL => "apr", self::MAY => "may", self::JUNE => "jun",
				self::JULY => "jul", self::AUGUST => "aug", self::SEPTEMBER => "sep", self::OCTOBER => "oct", self::NOVEMBER => "nov", self::DECEMBER => "dec"
			),
			self::LARGE => array(
				self::JANUARY => "january", self::FEBRUARY => "february", self::MARCH => "march", self::APRIL => "april", self::MAY => "may", self::JUNE => "june",
				self::JULY => "july", self::AUGUST => "august", self::SEPTEMBER => "september", self::OCTOBER => "october", self::NOVEMBER => "november", self::DECEMBER => "december"
			),
		);
		
		/**
		 * A singleton instance.
		 * @var DateDataTypeHelper
		 */
		protected static $singleton;
		
		/**
		 * The length type of year: large and short.
		 * @var array
		 */
		protected static $yearLengthTypes = array(self::SHORT => "y", self::LARGE => "Y");
		
		
		
		/* Public methods */
		
		/**
		 * Returns a singleton instance.
		 *
		 * @return DateDataTypeHelper
		 */
		public static function getInstance()
		{
			if (is_null(self::$singleton))
				self::$singleton = new self();
			
			return self::$singleton;
		}
		
		
		/**
		 * Returns the current date.
		 *
		 * @param string $format
		 * @return string
		 */
		public function getCurrentDate($format = "Y-m-d")
		{
			$date = date($format, $this->getCurrentTimestamp());
			return $date;
		}
		
		/**
		 * Returns the current month.
		 *
		 * @return string
		 */
		public function getCurrentMonth()
		{
			$month = intval($this->getCurrentDate("m"));
			return $month;
		}
		
		/**
		 * Returns the current month name.
		 *
		 * @param int $letterType
		 * @param int $lengthType
		 * @return string
		 * @throws InvalidArgumentException If given letter type or given length type are invalid.
		 */
		public function getCurrentMonthName($letterType = self::CAPITAL, $lengthType = self::LARGE)
		{
			$name = $this->getMonthName($this->getCurrentMonth(), $lengthType, $lengthType);
			return $name;
		}
		
		/**
		 * Returns the current timestamp.
		 *
		 * @return number
		 */
		public function getCurrentTimestamp()
		{
			$timestamp = time();
			return $timestamp;
		}
		
		/**
		 * Returns the current year.
		 *
		 * @return string
		 */
		public function getCurrentYear($lengthType = self::LARGE)
		{
			$this->validateLengthTypeOn(self::$yearLengthTypes, $lengthType);
			
			$year = intval($this->getCurrentDate(self::$yearLengthTypes[$lengthType]));
			return $year;
		}
		
		/**
		 * Returns the name of given month.
		 *
		 * @param int $number
		 * @param int $letterType
		 * @param int $lengthType
		 * @return string
		 * @throws InvalidArgumentException If given month number, letter type or length type are invalid.
		 */
		public function getMonthName($number, $letterType = self::CAPITAL, $lengthType = self::LARGE)
		{
			$this->validateMonthNumberOn($number);
			$this->validateLengthTypeOn(self::$monthsName, $lengthType);
			
			$name = self::$monthsName[$lengthType][$number];
			$this->applyLetterTypeOn($name, $letterType);
			
			return $name;
		}
		
		
		
		/* Protected methods */
		
		/**
		 * Applies on given value the given letter type.
		 *
		 * @param string $value
		 * @param int $letterType
		 * @return void
		 * @throws InvalidArgumentException If given letter type is invalid.
		 */
		protected function applyLetterTypeOn(&$value, $letterType)
		{
			if ($letterType === self::CAPITAL)
				$value = ucfirst($value);
			elseif ($letterType === self::LOWERCASE)
				$value = strtolower($value);
			elseif ($letterType === self::UPPERCASE)
				$value = strtoupper($value);
			else
			{
				$message = sprintf("Invalid letter type detected!");
				throw new InvalidArgumentException($message);
			}
		}
		
		/**
		 * Throws an exception if given length type does not exists on given collection.
		 *
		 * @param array $collection
		 * @param int $lengthType
		 * @return void
		 * @throws InvalidArgumentException
		 */
		protected function validateLengthTypeOn($collection, $lengthType)
		{
			if (!array_key_exists($lengthType, $collection))
				throw new InvalidArgumentException("Invalid length type detected!");
		}
		
		/**
		 * Throws an exception if given month number is invalid.
		 *
		 * @param int $number
		 * @return void
		 * @throws InvalidArgumentException
		 */
		protected function validateMonthNumberOn($number)
		{
			if (($number < self::JANUARY) || ($number > self::DECEMBER))
				throw new InvalidArgumentException("Invalid month number detected!");
		}
	}
	
?>