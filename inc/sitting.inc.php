<?PHP
	// Sitterzeit prüfen
  $date_res = dbquery("
  SELECT
   	COUNT(*) AS cnt
  FROM
      user_sitting_date
    INNER JOIN
      user_sitting
    	ON user_sitting_date_user_id=user_sitting_user_id
  WHERE
  	user_sitting_user_id='".$cu->id()."'
    AND user_sitting_active='1'
    AND user_sitting_date_from<".time()."
		AND user_sitting_date_to>".time().";");

	// Wenn der Sittingmodus aktiv ist, aber zur Zeit kein keine Loginzeit definitert ist -> Logout
	if($s['sitter_active']==1 && mysql_result($date_res,0)==0)
	{
		//if($_SESSION[ROUNDID]['user']['sitter_active']==NULL && $_SESSION[ROUNDID]['user']['sitter_active']!='0')
    session_destroy();
    unset($s);
    $_SESSION[ROUNDID]=Null;
    header("Location: ".LOGINSERVER_URL."?page=err&err=sitting");
    echo "<h1>Sittingzeit abgelaufen!</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=sitting\">hier</a> klicken...";
    exit;
	}
?>