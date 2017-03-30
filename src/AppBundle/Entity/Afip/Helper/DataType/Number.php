<?php
	
	/**
	 * Helper for Numeric types.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	abstract class NumberDataTypeHelper
	
	{
		/* Public methods */
		
		/**
		 * Returns the given number formatted as a currency value.
		 *
		 * @param float $value
		 * @return float
		 */
		public static function currency($value)
		{
			$newValue = number_format(self::truncate($value, 2), 2, ",", ".");
			return $newValue;
		}
		
		/**
		 * Returns a truncated number for given value and digits.
		 *
		 * @param float $value
		 * @param int $digits [Defualt: 0]
		 * @return float | NULL
		 */
		public static function truncate($value, $digits = 0)
		{
			if (!is_numeric($value) || (!is_int($digits) || ($digits < 0)))
				return NULL;
			
			self::doTruncate($value, $digits);
			
			return $value;
		}
		
		
		
		/* Protected methods */
		
		/**
		 * Does the truncate over given parameters.
		 *
		 * @param float $value
		 * @param int $digits
		 * @return void
		 */
		protected static function doTruncate(&$value, $digits)
		{
			$numberPlaces = self::retrieveNumberPlaces($value);
			
			if ($numberPlaces["exponential"] == 0)
			{
				$partA = $numberPlaces["integer"];
				$partB = $numberPlaces["decimals"];
			}
			elseif ($numberPlaces["exponential"] > 0)
			{
				$aux = substr($numberPlaces["decimals"], 0, $numberPlaces["exponential"]);
				
				if (strlen($aux) == $numberPlaces["exponential"])
				{
					$partA = $numberPlaces["integer"] . $aux;
					$partB = substr($numberPlaces["decimals"], $numberPlaces["exponential"]);
				}
				else
				{
					$partA = $numberPlaces["integer"] . $aux . str_repeat("0", $numberPlaces["exponential"] - strlen($aux));
					$partB = "0";
				}
			}
			else
			{
				$partA = "0";
				$partB = str_repeat("0", abs($numberPlaces["exponential"] + 1)) . $numberPlaces["integer"] . $numberPlaces["decimals"];
			}
			
			$partB = substr($partB, 0, $digits);
			$partB = str_pad($partB, $digits, "0", STR_PAD_RIGHT);
			
			$value = floatval("$partA.$partB");
			
			unset($partA, $partB, $aux);
		}
		
		/**
		 * Retrieves the places of given number.
		 *
		 * @param float $value
		 * @return array Associative array of strings.
		 */
		protected static function retrieveNumberPlaces($value)
		{
			$places = array("integer" => "0", "decimals" => "0", "exponential" => "0");
			$value = strtolower(strval($value));
			
			$aux = explode("e", $value);
			
			if (isset($aux[1]))
				$places["exponential"] = $aux[1];
			
			$aux = explode(".", $aux[0]);
			
			if (isset($aux[1]))
				$places["decimals"] = $aux[1];
			
			$places["integer"] = $aux[0];
			
			unset($aux);
			
			return $places;
		}
	}
	
?>