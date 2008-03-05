<?php
	/**
	* Class for getting and setting config values
	* from the config database table
	* It use the singleton design pattern, so 
	* use this class by calling
	* $cfg = Config::getInstance();
	* The first really useful [TM] class in EtoA ;)
	*
	* @autor MrCage <mrcage@etoa.ch>
	*
	*/
	class Config
	{
	    static private $instance;
	    private $values;
	    private $params1;
	    private $params2;
	    private $keys;
	
			/**
			* Get instance with this Singleton pattern
			*/
	    static public function getInstance()
	    {
	        if (!self::$instance)
	        {
	            self::$instance = new Config();
	        }
	        return self::$instance;
	    }
	
			/**
			* The constructor
			*/
	    private function Config()
	    {
	    	$this->values = array();
	    	$this->params1 = array();
	    	$this->params2 = array();
	    	
	    	$res = dbquery("
	    	SELECT 
	    		config_name,
	    		config_value,
	    		config_param1,
	    		config_param2 	 
	    	FROM 
	    		config;");
	    	while ($arr = mysql_fetch_array($res))
	    	{
	    		$this->keys[] = $arr['config_name'];
	    		$this->values[$arr['config_name']] = $arr['config_value'];
	    		$this->params1[$arr['config_name']] = $arr['config_param1'];
	    		$this->params2[$arr['config_name']] = $arr['config_param2'];
	    	}
	    }
	    
	    /**
	    * Adds a given value to a keyword
	    */
	    public function add($name,$val,$param1="",$param2="")
	    {
	    	if (in_array($name,$this->keys))
	    	{
	    		return false;
	    	}
	    	
	    	$this->values[$name]=$val;
	    	$this->params1[$name]=$param1;
	    	$this->params2[$name]=$param2;
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
	    	);
	    	");
	    	echo "Debug: Zur $name = $val config hinzugefügt!<br/>";
	    	return true;
	    }
	    
	    /**
	    * Changes a value
	    */
	    public function set($name,$val,$param1="",$param2="")
	    {
	    	$this->values[$name]=$val;
	    	$this->params1[$name]=$param1;
	    	$this->params2[$name]=$param2;
	    	dbquery("
	    	UPDATE
	    		config
	    	SET
	    		config_value='".$val."',
	    		config_param1='".$param1."',
	    		config_param2='".$param2."'    	
				WHERE
	 	    	config_name='".$name."'
	    	");
	    	if (mysql_affected_rows()==0)
	    	{
	    		$this->add($name,$val,$param1,$param2);
	    	}
	    }
	    	    
	    /**
	    * Getter for value
	    */
	    public function get($key)
	    {
	    	if (isset($this->values[$key]))
	    	{
	    		return $this->values[$key];
	    	}
	    	return false;
	    }
	    
	    /**
	    * Getter for value (alias)
	    */
	    public function value($key)
	    {
    		return $this->get($key);
	    }
	
	    /**
	    * Getter for parameter 1
	    */
	    public function param1($key)
	    {
	    	if (isset($this->params1[$key]))
	    	{
	    		return $this->params1[$key];
	    	}
	    	return false;
	    }
	    
	    /**
	    * Getter for parameter 1
	    */
	    public function p1($key)
	    {
    		return $this->param1($key);
	    }	    
	    
	    /**
	    * Getter for parameter 2
	    */
	    public function param2($key)
	    {
	    	if (isset($this->params2[$key]))
	    	{
	    		return $this->params2[$key];
	    	}
	    	return false;
	    }	 

	    /**
	    * Getter for parameter 2
	    */
	    public function p2($key)
	    {
    		return $this->param2($key);
	    }	    	    
	    
			/**
			* Wrapper for saving all values in an array (classic-style)
			*/
			public function getArray()
			{
				$conf = array();
				foreach ($this->keys as $key)
				{
					$conf[$key]['v'] = $this->values[$key];
					$conf[$key]['p1'] = $this->params1[$key];
					$conf[$key]['p2'] = $this->params2[$key];
				}
				return $conf;
			}	       	    	    
	    
	}
?>