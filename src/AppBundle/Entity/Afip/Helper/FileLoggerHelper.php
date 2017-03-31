<?php
	
	/**
	 * FileLogger class.
	 *
	 
	 * @author Eduardo Casey
	 */
	class FileLoggerHelper
	{
		/* Constants and Variables */
		
		/**
		 * @var string
		 */
		protected $columnSeparator;
		
		/**
		 * @var resource
		 */
		protected $file;
		
		/**
		 * @var string
		 */
		protected $filePath;
		
		/**
		 * @var boolean
		 */
		protected $isOpen;
		
		/**
		 * @var string
		 */
		protected $namespace;
		
		
		
		/* Public methods */
		
		/**
		 * Returns a new Instance.
		 *
		 * @param string $fileName [Default: NULL]
		 * @param string $fileDirectory [Defualt: current directory]
		 * @param string $columnSeparator [Default: " "]
		 * @return FileLoggerHelper
		 */
		public static function getInstance($fileName = NULL, $fileDirectory = ".", $columnSeparator = " ", $namespace = "system")
		{
			return new self($fileName, $fileDirectory, $columnSeparator, $namespace);
		}
		
		
		/**
		 * Constructor.
		 *
		 * @param string $fileName [Default: NULL]
		 * @param string $fileDirectory [Defualt: current directory]
		 * @param string $columnSeparator [Default: " "]
		 * @return FileLoggerHelper
		 */
		public function __construct($fileName = NULL, $fileDirectory = ".", $columnSeparator = " ", $namespace = "system")
		{
			$this->setColumnSeparator($columnSeparator);
			$this->setFilePath($fileName, $fileDirectory);
			$this->setNamespace($namespace);
			
			$this->open();
		}
		
		
		/**
		 * Returns the column separator
		 *
		 * @return string
		 */
		public function getColumnSeparator()
		{
			return $this->columnSeparator;
		}
		
		/**
		 * Returns the file path.
		 *
		 * @return string
		 */
		public function getFilePath()
		{
			return $this->filePath;
		}
		
		/**
		 * Returns the namespace.
		 *
		 * @return string
		 */
		public function getNamespace()
		{
			return $this->namespace;
		}
		
		/**
		 * Indicates if the file is open.
		 *
		 * @return boolean
		 */
		public function isOpen()
		{
			return $this->isOpen;
		}
		
		
		/**
		 * Opens the file.
		 *
		 * @return void
		 */
		public function open()
		{
			if (!$this->isOpen())
			{
				$dir = dirname($this->getFilePath());
			    if (!is_dir($dir))
      				mkdir($dir, 0777, 1);
				
				$this->file = fopen($this->getFilePath(), "a+");
				$this->isOpen = ($this->file !== false);
			}
		}
		
		/**
		 * Closes the file.
		 *
		 * @return void
		 */
		public function close()
		{
			if ($this->isOpen() && fclose($this->file))
				$this->isOpen = false;
		}
		
		/**
		 * Adds a new entry in the log.
		 *
		 * @param string $line
		 * @param string $namespace If is not defined it will be the default namespace of logger.
		 * @return boolean
		 */
		public function addRecord($line, $namespace = NULL)
		{
			if ($this->isOpen())
			{
				$namespace = $this->chooseNamespace($namespace);
				$line = str_replace(array(chr(13) . chr(10), "\n"), "\\n", $line);
				
				return fwrite($this->file, date("c") . $this->getColumnSeparator() . $namespace . $this->getColumnSeparator() . $line . "\n");
			}
			else
				return false;
		}
		
		/**
		 * Adds a new empty entry in the log.
		 *
		 * @return boolean
		 */
		public function addEmptyRecord()
		{
			if ($this->isOpen())
				return fwrite($this->file, "\n");
			else
				return false;
		}
		
		
		
		/* Protected methods */
		
		/**
		 * Chooses a namespace between given value and stored value.
		 *
		 * @param string $namespace
		 * @return string
		 */
		protected function chooseNamespace($namespace)
		{
			if ($namespace == NULL)
				return $this->getNamespace();
			else
				return $namespace;
		}
		
		/**
		 * Sets the column separator.
		 *
		 * @param string $columnSeparator
		 * @return void
		 */
		protected function setColumnSeparator($columnSeparator)
		{
			if (is_string($columnSeparator) && (strlen($columnSeparator) > 0))
				$this->columnSeparator = $columnSeparator[0];
			else
				$this->columnSeparator = " ";
		}
		
		/**
		 * Sets the file path.
		 *
		 * @param string $fileName
		 * @param string $fileDirectory [Default: current directory]
		 */
		protected function setFilePath($fileName, $fileDirectory)
		{
			$fileName = trim($fileName);
			if ($fileName == "")
				$fileName = (date("Y-m-d") . ".log");
			
			$fileDirectory = trim($fileDirectory);
			if ($fileDirectory == "")
				$fileDirectory = ".";
			
			$this->filePath = "$fileDirectory/$fileName";
		}
		
		/**
		 * Sets the namespace.
		 *
		 * @param string $namespace
		 */
		protected function setNamespace($namespace)
		{
			$this->namespace = $namespace;
		}
	}
	
?>