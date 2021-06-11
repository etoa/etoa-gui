<?php
	if (!interface_exists('ISingleton'))
	{
	  /**
	   * Singleton interface
	   * @author Nicolas Perrenoud <mrcage@etoa.ch>
	   */
	  interface ISingleton
	  {
	    /**
	    * Singleton setter.
	    * @access public
	    * @static
	    */
	    public static function getInstance();
	    /**
	    * Must override clone method
	    * to throw exeption when it's call
	    */
	    public function __clone();
	  }
	}
?>