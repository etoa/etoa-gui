<?PHP
	// Sitterzeit pr�fen
  $date_res = dbquery("
  SELECT
   	COUNT(*) AS cnt
  FROM
      user_sitting_date
    INNER JOIN
      user_sitting
    	ON user_sitting_date_user_id=user_sitting_user_id
  	AND	user_sitting_user_id='".$cu->id."'
    AND user_sitting_active='1'
    AND user_sitting_date_from<".time()."
		AND user_sitting_date_to>".time().";");

	$date_arr = mysql_fetch_row($date_res);
	// Wenn der Sittingmodus aktiv ist, aber zur Zeit kein keine Loginzeit definitert ist -> Logout
	if($s['sitter_active']==1 && $date_arr[0]==0)
	{
    session_destroy();
    unset($s);
    header("Location: ".Config::getInstance()->loginurl->v."?page=err&err=sitting");
    echo "<h1>Sittingzeit abgelaufen!</h1>Falls die Weiterleitung nicht klappt, <a href=\"".Config::getInstance()->loginurl->v."?page=err&err=sitting\">hier</a> klicken...";
    exit;
	}
?>