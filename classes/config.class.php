<?php
	/**
	* Class for getting and setting config values
	* from the config database table
	* It use the singleton design pattern, so 
	* use this class by calling
	* $cfg = Config::getInstance();
	* The first really useful [TM] class in EtoA ;)
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/
	class Config implements ISingleton
	{
	    static private $instance;
	    private $_items;
	
			/**
			* Get instance with this very nice singleton design pattern
			*/
	    static public function getInstance()
	    {
        if (!self::$instance)
        {
            self::$instance = new Config();
        }
        return self::$instance;
	    }
	
			public function __clone()
			{
				throw new EException("Config ist nicht klonbar!");
			}
	
			/**
			* The constructor (is private so getInstance() must be used)
			*/
	    private function __construct()
	    {
	    	$this->_items = array();
	    	
	    	$res = dbquery("
	    	SELECT 
	    		config_name,
	    		config_value,
	    		config_param1,
	    		config_param2 	 
	    	FROM 
	    		config;");
	    	while ($arr = mysql_fetch_assoc($res))
	    	{
	    		$this->_items[$arr['config_name']] = new ConfigItem($arr['config_value'],$arr['config_param1'],$arr['config_param2']);
	    	}
	    }
	    
	    /**
	    * Adds a given value to a keyword
	    */
	    public function add($name,$val,$param1="",$param2="")
	    {
    		$this->_items[$name] = new ConfigItem($val,$param1,$param2);
	    	dbquery("
	    	INSERT INTO
	    		config
	    	(
	 	    	config_name,
	    		config_value,
	    		config_param1,
	    		config_param2    	
	    	)
	    	VALUES
	    	(
	    		'".$name."',
	    		'".$val."',
	    		'".$param1."',
	    		'".$param2."'
	    	)
	    	ON DUPLICATE KEY UPDATE 
	    		config_value='".$val."',
	    		config_param1='".$param1."',
	    		config_param2='".$param2."'
	    	;");
	    	return true;
	    }
	    
	    /**
	    * Changes a value
	    */
	    public function set($name,$val,$param1="",$param2="")
	    {
    		$this->add($name,$val,$param1,$param2);
	    }
	    	    
	    /**
	    * Getter for value
	    */
	    public function get($key)
	    {
    		return $this->_items[$key]->v;
	    }
	    
	    /**
	    * Getter for value (alias)
	    */
	    public function value($key)
	    {
    		return $this->_items[$key]->v;
	    }
	
	    /**
	    * Getter for parameter 1
	    */
	    public function param1($key)
	    {
				return $this->_items[$key]->p1;
	    }
	    
	    /**
	    * Getter for parameter 1 (alias)
	    */
	    public function p1($key)
	    {
				return $this->_items[$key]->p1;
	    }	    
	    
	    /**
	    * Getter for parameter 2
	    */
	    public function param2($key)
	    {
				return $this->_items[$key]->p2;

	    }	 

	    /**
	    * Getter for parameter 2 (alias)
	    */
	    public function p2($key)
	    {
				return $this->_items[$key]->p2;
	    }	    	    
	    
			/**
			* Wrapper for saving all values in an array (classic-style)
			*/
			public function & getArray()
			{
				$conf = array();
				foreach ($this->_items as $key => &$i)
				{
					$conf[$key]['v'] = $i->v;
					$conf[$key]['p1'] = $i->p1;
					$conf[$key]['p2'] = $i->p2;
				}
				unset($i);
				return $conf;
			}	       	    	    
	   
	  public function __get($name)
  	{
  		try
  		{
				if (isset($this->_items[$name]))
				{
					return $this->_items[$name];
				}
				else
				{
					throw new EException("Konfigurationsvariable $name existiert nicht!");
					return null;
				}
			}
			catch (EException $e)
			{
				echo $e;
	    	return null;
			}			   		
    }	    
	}
	
	class ConfigItem
	{
		private $_v,$_p1,$_p2;
		function __construct($v,$p1,$p2)
		{
			$this->_v = $v;
			$this->_p1 = $p1;
			$this->_p2 = $p2;
		}
		
		function __toString()
		{
			return $this->_v;
		}
		
	  public function __get($name)
  	{
  		try
  		{
	    	if ($name=="p1")		
	    		return $this->_p1;
	    	if ($name=="p2")		
	    		return $this->_p2;
	    	if ($name=="v")		
	    		return $this->_v;
				throw new EException("Property $name der Klasse  ".__CLASS__." existiert nicht!");
				return null;
			}
			catch (EException $e)
			{
				echo $e;
	    	return null;
			}			   		
    }
	}
?>