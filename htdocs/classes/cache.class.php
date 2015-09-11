<?php

/**
* Provides functions for the cache directory
* such as permission checking
*/
class Cache 
{
	private static $errMsg;
	
	/**
	* The constructor
	*/
    function Cache($path=".") 
    {
    	if (file_exists( $path."/".$this->cacheDir))
    	{
    		$this->cacheDir = $path."/".$this->cacheDir;
    	}
    	else
    	{
    		self::$errMsg = "Fehlerhafter Pfad $path";
    	}
    }
    
  	/**
  	* Checks for correct (Unix) permissions of the cache directory
  	*/
    static function checkPerm($type="")
    {
    	if (UNIX) {    	
	    	$path = $type!="" ? CACHE_ROOT."/".$type : CACHE_ROOT;

			if (file_exists($path)) {
				if (is_writable($path)) {
					return true;
				}
				self::$errMsg = "Das Cache-Verzeichnis ".$type." hat falsche Berechtigungen!";
				return false;	
			}
			self::$errMsg = "Das Cache-Verzeichnis ".$type." ".$path." wurde nicht gefunden!";
			return false;
    	}
    	elseif(WINDOWS) {
    		return true;
    	} else 	{
    		return false;    		
    	}    	
    }
	
	/** 
	* Returns the latest error message
	*/
	static function getErrMsg() {
		return self::$errMsg;
	}
}
?>