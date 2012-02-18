<?php
/**
*
*
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/
class Constants implements ISingleton
{
	private $_items;
	const configFile = "config/constants.conf";

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
		throw new EException("Config ist nicht klonbar!");
	}

	/**
	* The constructor (is private so getInstance() must be used)
	*/
	private function __construct()
	{
		if (!is_file(RELATIVE_ROOT.self::configFile))	{
			throw new EException("Konfigurationsdatei ".self::configFile." existiert nicht!");			
		}
		$this->_items = json_decode(file_get_contents(RELATIVE_ROOT.self::configFile),true);
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
		return null;
	}		
}
?>