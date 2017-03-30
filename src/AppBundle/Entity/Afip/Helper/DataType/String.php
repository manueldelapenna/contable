<?php
	
	/**
	 * Helper for String types.
	 *
	 * @author Quanbit Software SA
	 * @author Eduardo Casey
	 */
	abstract class StringDataTypeHelper
	
	{
		const CONVMODE__NONE = 0;
		const CONVMODE__UTF8_TO_ISO88591 = 1;
		const CONVMODE__ISO88591_TO_UTF8 = 2;
		
		
		/**
		 * Transforms the hexadecimal encoding in the given HTML.
		 *
		 * @param string $html
		 * @param int $convertionMode [Default: StringDataTypeHelper::CONVMODE__NONE]
		 * @return string | NULL
		 */
		public static function htmlHexEncodingTransform($html, $convertionMode = self::CONVMODE__NONE)
		{
			if (is_string($html))
			{
				try
				{
					preg_match_all("/=[0-9A-F]{2}/", $html, $result);
					if (count(@$result[0]) > 0)
					{
						$result = array_unique($result[0]);
						$oldChars = array();
						$newChars = array();
						
						foreach($result as $hex)
						{
							$oldChars[] = $hex;
							$newChars[] = chr(hexdec($hex));
						}
						
						$html = str_replace("=3D", "iq!3D", $html);
						$html = str_replace($oldChars, $newChars, $html);
						$html = str_replace(array("=\r\n", "=\n", "=\r"), "", $html);
						$html = str_replace("iq!3D", "=", $html);
						
						switch ($convertionMode)
						{
							case self::CONVMODE__ISO88591_TO_UTF8:
								return utf8_encode($html);
							break;
							case self::CONVMODE__UTF8_TO_ISO88591:
								return utf8_decode($html);
							break;
							default:
								return $html;
							break;
						}
					}
					else
						return $html;
				}
				catch (Exception $e)
				{
				}
			}
			
			return NULL;
		}
		
		/**
		 * Normalizes the given value for ID.
		 *
		 * @param string $value
		 * @return string | NULL
		 */
		public static function normalizeForId($value, $useCaseSensitive = false)
		{
			if (is_string($value))
			{
				$value = trim($value);
				if ($value != "")
				{
					if ($useCaseSensitive)
						$pattern = "/[^a-zA-Z0-9_]/";
					else
					{
						$pattern = "/[^a-z0-9_]/";
						$value = strtolower($value);
					}
					
					/*** Deletes empty char group ***/
					$value = str_replace(array("()", "[]", "{}", "<>"), "", $value);
					
					/*** Deletes closing char of char group ***/
					$value = str_replace(array(")", "]", "}", ">"), "", $value);
					
					/*** Replaces opening char of char group ***/
					$value = str_replace(array("(", "[", "{", "<", " "), "_", $value);
					
					/*** Applies pattern ***/
					$value = preg_replace($pattern, "", $value);
					
					if ($value == "")
						return NULL;
					else
					{
						preg_match("/[0-9]/", $value[0], $matches);
						if (isset($matches[0]) || isset($matches[1]))
							return "_$value";
						else
							return $value;
					}
				}
			}
			
			return NULL;
		}
		
		/**
		 * Normalizes the given value for username.
		 *
		 * @param string $value
		 * @return string | NULL
		 */
		public static function normalizeForUsername($value)
		{
			if (is_string($value))
			{
				$value = trim($value);
				if ($value != "")
					return preg_replace("/[^a-z0-9\._-]/", "", str_replace(" ", ".", strtolower($value)));
			}
			
			return NULL;
		}
		
		/**
		 * Returns an array of chars for given value.
		 *
		 * @param string $value
		 * @return array | NULL
		 */
		public static function toChars($value)
		{
			if (is_string($value))
			{
				try
				{
					$chars = array();
					for ($x = 0, $y = strlen($value); $x < $y; $x++)
						$chars[] = array("ord" => ord($value[$x]), "chr" => $value[$x]);
					
					return $chars;
				}
				catch (Exception $e)
				{
				}
			}
			
			return NULL;
		}
		
		/**
		 * Indicates whether the given value is a valid email.
		 *
		 * @param string $value
		 * @return boolean
		 */
		public static function validateEmail($value)
		{
			return (preg_match("/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i", $value) != 0);
		}
	}
	
?>