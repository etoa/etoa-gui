<?PHP
	/**
	* Default fleet action (return fleed immediately)
	*/ 
	
	// Select correct action
	if(strlen($arr['fleet_action'])>0)
		$action = substr($arr['fleet_action'],0,1).'r';
	else
		$action="_r";

  // Flotte zurückschicken
  fleet_return($arr,$action);
?>            