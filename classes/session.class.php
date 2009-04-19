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
	// Singleton code
	//
	protected static $instance;
	public static function getInstance()
	{
		if (empty(self::$instance))
		{
			$className = __CLASS__;
			self::$instance = new $className(func_get_args());
		}
		return self::$instance;
	}
	final function __clone() {}

	//
	// Class variables and constants
	//
	protected $lastError;
	protected $firstView = false;
	protected $namePrefix = "etoa";

	/**
	 * The constructor defines the session hash function to be used
	 * and names and initiates the session
	 */
	protected function __construct()
	{
		ini_set('session.hash_function', 1); // Use SHA1 hash
		session_name($this->namePrefix.ROUNDID); // Set session name based on round name
		session_start();	// Start the session
	}

	function __get($field)
	{
		if ($field=="lastError")
			return ($this->lastError!=null) ? $this->lastError : "";
		if ($field=="id")
			return session_id();
		if ($field=="firstView")
			return $this->firstView;
		if (isset($_SESSION[$field]))
			return $_SESSION[$field];
		return null;
	}

	function __isset($field)
	{
		return isset($_SESSION[$field]);
	}

	function __unset($field)
	{
		unset($_SESSION[$field]);
	}
	
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

	abstract function login($data);
	abstract function logout();
	abstract function validate();
	abstract function registerSession();

	abstract static function unregisterSession($sid=null,$logoutPressed=1);
	abstract static function cleanup();
	abstract static function kick($sid);




}
?>
