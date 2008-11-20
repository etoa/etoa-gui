<?PHP
	// Write all changes of $s to the session variable
	$_SESSION[SESSION_NAME]=$s;
			
	dbclose();
?>