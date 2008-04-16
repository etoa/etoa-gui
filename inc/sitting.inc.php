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
    	AND user_sitting_user_id='".$cu->id()."'
    	AND user_sitting_active='1'
  WHERE
     user_sitting_date_from<".time()."
		 AND user_sitting_date_to>".time().";");

	// Wenn der Sittingmodus aktiv ist, aber zur Zeit kein keine Loginzeit definitert ist -> Logout
	if($s['sitter_active']==1 && mysql_result($data_res,0)==0)
	{
		//if($_SESSION[ROUNDID]['user']['sitter_active']==NULL && $_SESSION[ROUNDID]['user']['sitter_active']!='0')
    session_destroy();
    unset($s);
    $_SESSION[ROUNDID]=Null;
    header("Location: ".LOGINSERVER_URL."?page=err&err=sitting");
    echo "<h1>Sittingzeit abgelaufen!</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=sitting\">hier</a> klicken...";
    exit;
	}

	//Abgelaufene Sittings löschen
  $check_res = dbquery("
  SELECT
      user_sitting_date_to
  FROM
      user_sitting_date
    INNER JOIN
      user_sitting
    ON user_sitting_date_user_id=user_sitting_user_id
    AND user_sitting_user_id='".$cu->id()."'
    AND user_sitting_active='1'
  ORDER BY
    user_sitting_date_to DESC
  LIMIT 1;");
  $check_arr=mysql_fetch_assoc($check_res);

    if(mysql_num_rows($check_res)>0 && $check_arr['user_sitting_date_to']<time())
    {
        dbquery("
        UPDATE
            user_sitting
        SET
            user_sitting_active='0',
            user_sitting_sitter_user_id='0',
            user_sitting_sitter_password='0',
            user_sitting_date='0'
        WHERE
            user_sitting_user_id='".$cu->id()."';");

        //löscht alle gespeichertet Sittingdaten des users
        dbquery("DELETE FROM user_sitting_date WHERE user_sitting_date_user_id='".$cu->id()."';");
    }
?>