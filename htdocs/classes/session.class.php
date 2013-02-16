<?php
/**
 * Providess session and authentication management
 * for user login areas
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
abstract class Session implements ISingleton
{
	//
	// Singleton mechanism
	//

	/**
	 * @var Session Singleton innstance container
	 */
	protected static $instance;

	/**
	 * Returns the single instance of this class (Singleton design pattern)
	 * 
	 * @return Session Instance of the session class
	 */
	public static function getInstance($className = null)
	{
		if (empty(self::$instance))
		{
			// PHP >= 5.3
			if (function_exists("get_called_class"))
				$className = get_called_class();
			// PHP < 5.3
			elseif ($className==null)
				$className = __CLASS__;
			self::$instance = new $className(func_get_args());
		}
		return self::$instance;
	}

	/**
	 * The Clone operator. Cloning is prohibitet by this definition
	 */
	final function __clone() {}

	//
	// Class variables and constants
	//

	/**
	 * @var string Message of the last error
	 */
	protected $lastError;
	/**
	 * @var string Short string (one word/abreviation) that characterizes the last error
	 */
	protected $lastErrorCode;
	/**
	 * @var bool True if this is the first run (page) of the session, that means if the user hast just logged in
	 */
	protected $firstView = false;
	/**
	 * @var string Prefix string for the session-id seeding. Must be unique for each inheriting class
	 */
	protected $namePrefix = "";

	//
	// Abstract methds
	//

	/**
	 * Tries to login the user with the given dataand to create a session
	 * 
	 * @param array $data Login data like username and password
	 */
	abstract function login($data);

	/**
	 * Logs the current user out and destroys the session
	 */
	abstract function logout();

	/**
	 * Validates if the user is currently logged in and if his session is valid
	 */
	abstract function validate();

	/**
	 * Registers a new session
	 */
	abstract function registerSession();

	//
	// Common class methods
	//

	/**
	 * The constructor defines the session hash function to be used
	 * and names and initiates the session
	 */
	protected function __construct()
	{
		// Use SHA1 hash
		ini_set('session.hash_function', 1);

		// Set session name based on round name.
		// MD5 is needed because spaces in roundname cause problems
		$sname = md5($this->namePrefix.Config::getInstance()->roundname->v);
		session_name($sname);
		@session_start();	// Start the session
	}

	/**
	 * Getter that returns session variables or some class properties
	 * 
	 * @param <type> $field
	 * @return mixed Requested variable or null if field was not found
	 */
	function __get($field)
	{
		if ($field=="lastError")
			return ($this->lastError!=null) ? $this->lastError : "";
		if ($field=="lastErrorCode")
			return ($this->lastErrorCode!=null) ? $this->lastErrorCode : "general";
		if ($field=="id")
			return session_id();
		if ($field=="firstView")
			return $this->firstView;

		if (isset($_SESSION[$field]))
		{
			return $_SESSION[$field];
		}
		return null;
	}

	/**
	 * Checks if a session property exists
	 *
	 * @param string $field Property name
	 * @return bool True if property exists
	 */
	function __isset($field)
	{
		return isset($_SESSION[$field]);
	}

	/**
	 * Sets a session property
	 *
	 * @param string $field Property name
	 * @param mixed $value Property value
	 * @return bool True if setting was successfull
	 */
	function __set($field,$value)
	{
		if ($field=="lastError" || $field=="id")
		{
			error_msg("Private Variable!");
			return false;
		}
		else
		{
			$_SESSION[$field] = $value;
		}
		return true;
	}

	/**
	 * Unsets a session property
	 *
	 * @param string $field Property name
	 */
	function __unset($field)
	{
		unset($_SESSION[$field]);
	}
}
?>
