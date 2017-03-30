<?php
/**
 * Description of FileSemaphore
 *
 * @author Eduardo Casey
 */

class FileSemaphore
{  

	/* Variables and Constants */
	const EXTINCT_TIME_SECONDS = 240;
	protected $semFile;
	protected $lifeTime;
	protected $warningTime;
	
	/* Public methods */
	
	/**
	 * Creates a file semaphore.
	 *
	 * @param string $name
	 * @param string $prefix
	 * @param int $lifeTime Determines the lifetime of the semaphore, in seconds. [Default: FileSemaphore::EXTINCT_TIME_SECONDS]
	 * @param int $warningTime Determines the time in reporting the continued existence of the semaphore, in seconds. [Default: 0]
	 * @return FileSemaphore instance
	 * @throws Exception The semaphore name is invalid.
	 */
	public function __construct($name, $prefix = "", $lifeTime = self::EXTINCT_TIME_SECONDS, $warningTime = 0)
	{
		$this->setSemFile($name, $prefix);
		$this->setLifeTime($lifeTime);
		$this->setWarningTime($warningTime);
			
		if ($this->isLock() && $this->isLifeTimeExpired())
			$this->unlock();
	}
	
	/**
	 * Sleep a process.
	 *
	 * @return void
	 */
	public function lock()
	{
		while ($this->isLock());
			
		$file = fopen($this->semFile, "w+");
		fclose($file);
			
		chmod($this->semFile, 0777);
	}
	
	/**
	 * Awakens a sleeping process.
	 *
	 * @return boolean
	 */
	public function unlock()
	{
		return @unlink($this->semFile);
	}
	
	/**
	 * Returns whether the semaphore is locked.
	 *
	 * @return boolean
	 */
	public function isLock()
	{
		return file_exists($this->semFile);
	}
	
	/**
	 * Executes the action associated with the expiration of warning time.
	 *
	 * @return boolean
	 */
	public function executeWarningTimeAction()
	{
		try
		{
			if ($this->isWarningTimeExpired())
			{
				Mage::log("Se bloqueó el semáforo " . $this->semFile);
 				
 				$templateId = 'semaphore_notification';
 				$emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);
 				 
 				$vars = array('semaphore' => $this->semFile, 'date' => $this->getSemFileLifeForHuman());
 				
 				$emailTemplate->getProcessedTemplate($vars);
 				 
 				$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_support/email', 1));
 				$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_support/name', 1));
 				 
 				$receiveEmail = Mage::getStoreConfig('afip/config/support_email');
 				$receiveName = "Quanbit Software SA";
 				
 				$emailTemplate->setTemplateSubject('Semáforo bloqueado.');
 				
 				$emailTemplate->send($receiveEmail,$receiveName, $vars);
 				
				return true;
			}
			else
				return false;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	/**
	 * Indicates whether there is a traffic lights to the requested name.
	 *
	 * @param string $name
	 * @param string $prefix
	 * @return boolean
	 */
	public static function existsSemaphore($name, $prefix = "")
	{
		try
		{
			if (isset($name) && (trim($name) != ""))
				return file_exists(self::generateSemFileName($name, $prefix));
			else
				return false;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	
	
	/* Protected methods */
	
	/**
	 * Returns the active current time of the semaphore, in seconds.
	 *
	 * @return int
	 */
	protected function getSemFileLifeAsSeconds()
	{
		return time() - filemtime($this->semFile);
	}
	
	/**
	 * Returns the active current time of the semaphore, in array.
	 *
	 * @return string
	 */
	protected function getSemFileLifeForHuman()
	{
		$life = $this->getSemFileLifeAsSeconds();
		$value = array("days" => 0, "hours" => 0, "minutes" => 0, "seconds" => 0);
			
		$value["seconds"] = $life % 60;
		$life = intval($life / 60);
			
		$value["minutes"] = $life % 60;
		$life = intval($life / 60);
			
		$value["hours"] = $life % 24;
			
		$value["days"] = intval($life / 24);
			
		return $value["days"] . "d " . $value["hours"] . "h " . $value["minutes"] . "m " . $value["seconds"] . "s";
	}
	
	/**
	 * Sets the semphore file.
	 *
	 * @param string $name
	 * @param string $prefix
	 * @return void
	 */
	protected function setSemFile($name, $prefix = "")
	{
		if (isset($name) && (trim($name) != ""))
			$this->semFile = self::generateSemFileName($name, $prefix);
		else
			throw new Exception("The semaphore name is invalid.");
	}
	
	/**
	 * Sets the semaphore lifetime, in seconds.
	 *
	 * @param int $lifeTime
	 * @return void
	 */
	protected function setLifeTime($lifeTime)
	{
		$this->lifeTime = ($lifeTime >= 0) ? $lifeTime : 0;
	}
	
	/**
	 * Sets the semaphore warning time, in seconds.
	 *
	 * @param int $warningTime
	 * @return void
	 */
	protected function setWarningTime($warningTime)
	{
		$this->warningTime = ($warningTime >= 0) ? $warningTime : 0;
	}
	
	/**
	 * Returns whether the semaphore lifetime is expired.
	 *
	 * @return boolean
	 */
	protected function isLifeTimeExpired()
	{
		return ($this->lifeTime != 0) && ($this->getSemFileLifeAsSeconds() > $this->lifeTime);
	}
	
	/**
	 * Returns whether the semaphore warning time is expired.
	 *
	 * @return boolean
	 */
	protected function isWarningTimeExpired()
	{
		return ($this->warningTime != 0) && ($this->getSemFileLifeAsSeconds() > $this->warningTime);
	}
	
	/**
	 * Generates a returns a name for the semaphore file.
	 *
	 * @param string $name
	 * @param string $prefix
	 * @return string
	 */
	protected static function generateSemFileName($name, $prefix)
	{
		$dir = Mage::getBaseDir('var') . '/qb_locks';
		if(!is_dir($dir)){
			mkdir($dir, 0777, true);
		}
		$result = sprintf("%s/%s/%s.sem", $dir, self::normalizeName($prefix), self::normalizeName($name));
		return $result;
	}
	
	/**
	 * Noramlizes given name.
	 *
	 * @param string $name
	 * @return string
	 */
	protected static function normalizeName($name)
	{
		$name = str_replace(" ", "-", trim(strval($name)));
		return $name;
	}

   
}
