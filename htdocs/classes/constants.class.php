<?php
/**
* Gives access to constants defined in config file
*
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/
class Constants implements ISingleton
{
	private $_items;
	const configFile = "constants.conf";

	static private $instance;

	/**
	* Get instance with this very nice singleton design pattern
	*/
	static public function getInstance()
	{
		if (!self::$instance)
		{
			$className = __CLASS__;
			self::$instance = new $className();
		}
		return self::$instance;
	}

	/**
	* Disables cloning
	*/
	public function __clone()
	{
		throw new EException(__CLASS__." ist nicht klonbar!");
	}

	/**
	* The constructor (is private so getInstance() must be used)
	*/
	private function __construct()
	{
		$this->_items = fetchJsonConfig(self::configFile);
	}

	public function __isset($name)
	{
		return isset($this->_items[$name]);
	}

	public function __get($name)
	{
		if (isset($this->_items[$name]))
		{
			return $this->_items[$name];
		}
		throw new EException("Konstante $name existiert nicht!");
	}
}
?>
