<?PHP
	class User
	{
		private $id;
		private $setup;
		
		function User($id)
		{
			$this->id = $id;
		}
		
		function id()
		{
			return $this->id;
		}
		
		function isSetup()
		{
			return $this->setup;
		}		
		
		function setRace($raceid)
		{
	    $sql = "
	    UPDATE
	    	users
	    SET
				user_race_id=".$raceid."
	    WHERE
	    	user_id='".$this->id."';";
	    dbquery($sql);					
		}

		function setSetupFinished($raceid)
		{
	    $sql = "
	    UPDATE
	    	users
	    SET
				user_setup=1
	    WHERE
	    	user_id='".$this->id."';";
	    dbquery($sql);
	    $this->setup=true;					
		}

		
	}

?>