<?PHP
	
	/**
	* MySQL Database Connector class
	*
  * @author Nicolas Perrenoud, mail@dysign.ch
  * @version 0.11
	*/	
	class MySQL
	{
		private $handle;
		private $queryCounter;
		
		/**
		* Constructor
		*
		* Initializes DB connection
		*/
		public function MySQL($server,$user,$password,$db)
		{
			if ($this->handle = @mysql_connect($server,$user,$password) or die($this->printError("Could not connect!")))
			{
				if (mysql_select_db($db,$this->handle) or die($this->printError("Could not connect!")))
				{
					$this->queryCounter=0;
					$this->query("SET NAMES 'utf8';");
				}
				else
				{
					printError("Kann Datenbank nicht wählen!");
					exit;
				}
			}
			else
			{
				printError("Keine Serververbindung möglich!");
				exit;
			}
		}
		
		/**
		* Destructor
		*
		* Closes DB connection
		*/
		function __destruct()
		{
			@mysql_close($this->handle);
			//echo "<br/><br/>Database closed, ".$this->queryCounter." Queries executed";
		}
		
   /**
    * Performs the given query on the database 
    * and returns the result
    */
   	public function query($query)
   	{				
     	if ($res=mysql_query($query, $this->handle))
     	{
     		$this->queryCounter++;
     		return $res;
    	}
    	else
    	{
    		die($this->printError("Errors in Query!",$query));
    	}
   	}
   	
   	/**
   	* Returns the number of rows of a given query result
   	*/
   	public function numRows($res)
   	{
   		if ($nr = mysql_num_rows($res))
   		{
   			return $nr;
   		}
   		else
   		{
   			return 0;
   		}
   	}
   	
   	/**
   	* Counts number of rows of a given result set
   	*
   	* @param $fetch MySQL result
   	* @return Number of rows
   	*/
   	public function num_rows($res)
   	{
   		return $this->numRows($res);
   	}
   	
   	/**
   	* Fetches a row and returns it as an array
   	*
   	* @param $fetch MySQL result
   	* @return Row as array
   	*/
   	public function fetch($res)
   	{
   		return mysql_fetch_array($res);
   	}
   	
   	/**
   	* Prints a mysql error message
   	*/
   	private function printError($string, $query="")	
   	{
   		echo "<b>Database returns:</b> $string<br/><b>Error:</b> ".mysql_error()."<br/>";
   		if ($query!="")
   		{
   			echo "<b>Query:</b> $query<br/>";
   		}
   	}		
	}

?>