<?PHP
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: login.php
	// 	Topic: Login-Checker
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.05.2007
	// 	Kommentar:
	//
	
	/**
	* Login file, checks if login is valid and generates session key
	*
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/		

	// Zu-viele-Fenster-Checker resetten
	$_SESSION[ROUNDID]['firstlog']=null;

	// Login-Verifikation prüfen
	if ((encryptCaptchaString($_POST['login_verification1'], md5($_SERVER['REMOTE_ADDR'].$_SERVER["HTTP_USER_AGENT"])) != $_POST['login_verification2'] && $_SERVER['REMOTE_ADDR']!="127.0.0.1") || $_POST['login_verification2']=="")
	{
		header("Location: ".LOGINSERVER_URL."?page=err&err=verification");
		echo "<h1>Falscher Bildcode</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=verification\">hier</a> klicken...";
		exit;
	}

	// Prüfen ob der Login-Button gedrückt wurde
	if ($_POST['login_submit']!="")
	{
		$_SESSION[ROUNDID]=null;
		
		// Login-Eingaben prüfen
		if ($_POST['login_nick']!="" && !stristr($_POST['login_nick'],"'") && !stristr($_POST['login_pw'],"'"))
		{
      $login_ok=false;
      $sitter_login_ok=false;

      $ures = dbquery("
      SELECT 
      	user_id,
      	user_registered 
      FROM 
      	".$db_table['users']." 
      WHERE 
      	LCASE(user_nick)='".strtolower($_POST['login_nick'])."' 

      ;");
			if (mysql_num_rows($ures)>0)
			{
				$uarr = mysql_fetch_row($ures);	
				$ures = dbquery("
	      SELECT 
	      	user_id
	      FROM 
	      	".$db_table['users']." 
	      WHERE 
	      	user_id=".$uarr[0]."
	      	AND user_password='".pw_salt($_POST['login_pw'],$uarr[1])."'
	      ;");
				if (mysql_num_rows($ures)>0)
				{
					$login_ok=true;
				}
			}

      //überprüft ob der sittmodus aktiviert ist und das passwort übereinstimmt
			$user_id = get_user_id(strtolower($_POST['login_nick']));
			$sres = dbquery("
			SELECT 
				user_sitting_id 
			FROM 
				".$db_table['user_sitting']." 
			WHERE 
				user_sitting_sitter_password='".md5($_POST['login_pw'])."' 
				AND user_sitting_user_id='".$user_id."' 
				AND user_sitting_active='1'
			;");
			if (mysql_num_rows($sres)>0)
      {
      	$sitter_login_ok=true;
      }


			if ($login_ok || $sitter_login_ok)
			{
        $res = dbquery("
        SELECT
            *
        FROM
            ".$db_table['users']."
        INNER JOIN
            ".$db_table['races']."
            ON user_race_id=race_id
            AND user_id='".$user_id."';");

				$arr = mysql_fetch_array($res);

				$_SESSION[ROUNDID]['user']['id']=$arr['user_id'];
				$_SESSION[ROUNDID]['user']['nick']=$arr['user_nick'];
				$_SESSION[ROUNDID]['user']['email']=$arr['user_email'];
				$_SESSION[ROUNDID]['user']['last_online']=$arr['user_last_online'];
				$_SESSION[ROUNDID]['user']['race_id']=$arr['user_race_id'];
				$_SESSION[ROUNDID]['user']['points']=$arr['user_points'];
				$_SESSION[ROUNDID]['user']['alliance_id']=$arr['user_alliance_id'];
				$_SESSION[ROUNDID]['user']['css_style']=$arr['user_css_style'];
				$_SESSION[ROUNDID]['user']['game_width']=$arr['user_game_width'];
				$_SESSION[ROUNDID]['user']['planet_circle_width']=$arr['user_planet_circle_width'];
				$_SESSION[ROUNDID]['user']['image_url']=$arr['user_image_url'];
				$_SESSION[ROUNDID]['user']['image_ext']=$arr['user_image_ext'];
				$_SESSION[ROUNDID]['user']['item_show']=$arr['user_item_show'];
				$_SESSION[ROUNDID]['user']['item_order_ship']=$arr['user_item_order_ship'];
				$_SESSION[ROUNDID]['user']['item_order_def']=$arr['user_item_order_def'];
				$_SESSION[ROUNDID]['user']['item_order_way']=$arr['user_item_order_way'];
				$_SESSION[ROUNDID]['user']['image_filter']=$arr['user_image_filter'];
				$_SESSION[ROUNDID]['user']['blocked_from']=$arr['user_blocked_from'];
				$_SESSION[ROUNDID]['user']['blocked_to']=$arr['user_blocked_to'];
				$_SESSION[ROUNDID]['user']['hmode_from']=$arr['user_hmode_from'];
				$_SESSION[ROUNDID]['user']['hmode_to']=$arr['user_hmode_to'];
				$_SESSION[ROUNDID]['user']['helpbox']=$arr['user_helpbox'];
				$_SESSION[ROUNDID]['user']['notebox']=$arr['user_notebox'];
				
				$_SESSION[ROUNDID]['user']['admin']=$arr['user_admin']==1 ? true : false;
				$_SESSION[ROUNDID]['user']['ip']=$_SERVER['REMOTE_ADDR'];
				$_SESSION[ROUNDID]['user']['msg_preview']=$arr['user_msg_preview'];
				$_SESSION[ROUNDID]['user']['msgcreation_preview']=$arr['user_msgcreation_preview'];
				$_SESSION[ROUNDID]['user']['msgsignature']=$arr['user_msgsignature'];
				$_SESSION[ROUNDID]['user']['msg_copy']=$arr['user_msg_copy'];
				$_SESSION[ROUNDID]['user']['msg_blink']=$arr['user_msg_blink'];
			

				$_SESSION[ROUNDID]['user']['specialist_time']=$arr['user_specialist_time'];
				$_SESSION[ROUNDID]['user']['specialist_id']=$arr['user_specialist_id'];

        $_SESSION[ROUNDID]['user']['spyship_count']=$arr['user_spyship_count'];
        $_SESSION[ROUNDID]['user']['spyship_id']=$arr['user_spyship_id'];
        $_SESSION[ROUNDID]['user']['havenships_buttons']=$arr['user_havenships_buttons'];
        $_SESSION[ROUNDID]['user']['show_adds']=$arr['user_show_adds'];
				 
				 
				 
				 
				if ($arr['user_alliance_application']!="")
				{
					$_SESSION[ROUNDID]['user']['alliance_application']=1;
				}
				else
				{
					$_SESSION[ROUNDID]['user']['alliance_application']=0;
				}

				if($sitter_login_ok==1 && $login_ok!=1)
				{
					$_SESSION[ROUNDID]['user']['sitter_active']=1;
				}
				else
				{
					$_SESSION[ROUNDID]['user']['sitter_active']=0;
				}

				$_SESSION[ROUNDID]['user']['tick']=true;
				$_SESSION[ROUNDID]['user']['login_time']=time();

				$_SESSION[ROUNDID]['user']['race']['name']=$arr['race_name'];
				$_SESSION[ROUNDID]['user']['race']['researchtime']=$arr['race_f_researchtime'];
				$_SESSION[ROUNDID]['user']['race']['buildtime']=$arr['race_f_buildtime'];
				$_SESSION[ROUNDID]['user']['race']['fleettime']=$arr['race_f_fleettime'];
				$_SESSION[ROUNDID]['user']['race']['metal']=$arr['race_f_metal'];
				$_SESSION[ROUNDID]['user']['race']['crystal']=$arr['race_f_crystal'];
				$_SESSION[ROUNDID]['user']['race']['plastic']=$arr['race_f_plastic'];
				$_SESSION[ROUNDID]['user']['race']['fuel']=$arr['race_f_fuel'];
				$_SESSION[ROUNDID]['user']['race']['food']=$arr['race_f_food'];
				$_SESSION[ROUNDID]['user']['race']['power']=$arr['race_f_power'];
				$_SESSION[ROUNDID]['user']['race']['population']=$arr['race_f_population'];

				$login_time=time();
				$_SESSION[ROUNDID]['key']=md5($login_time).md5($arr['user_id']).md5(ROUNDID).md5(gethostbyaddr($_SERVER['REMOTE_ADDR'])).md5($_SERVER['HTTP_USER_AGENT']).session_id();

				if($_SESSION[ROUNDID]['user']['sitter_active']==0)
				{
        	dbquery("
        	UPDATE
        	    ".$db_table['users']."
        	SET
        	    user_last_online='".$login_time."',
        	    user_logintime=".$login_time.",
        	    user_acttime=".$login_time.",
        	    user_ip='".$_SERVER['REMOTE_ADDR']."',
        	    user_client='".$_SERVER['HTTP_USER_AGENT']."',
        	    user_hostname='".gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
        	    user_session_key='".$_SESSION[ROUNDID]['key']."'
        	WHERE
        	    user_id='".$arr['user_id']."';");
        }
        elseif($_SESSION[ROUNDID]['user']['sitter_active']==1)
        {
        	dbquery("
        	UPDATE
        	    ".$db_table['users']."
        	SET
        	    user_last_online='".$login_time."',
        	    user_logintime=".$login_time.",
        	    user_acttime=".$login_time.",
        	    user_hostname='".gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
        	    user_client='".$_SERVER['HTTP_USER_AGENT']."',
        	    user_session_key='".$_SESSION[ROUNDID]['key']."'
        	WHERE
        	    user_id='".$arr['user_id']."';");
        	
        	dbquery("
        	UPDATE
        	    ".$db_table['user_sitting']."
        	SET
        	    user_sitting_sitter_ip='".$_SERVER['REMOTE_ADDR']."'
        	WHERE
        	    user_sitting_user_id='".$arr['user_id']."';");
        }

				// Log hinzufügen
        dbquery("
        INSERT INTO
        ".$db_table['user_log']."
            (log_user_id,
            log_logintime,
            log_ip,
            log_hostname,
            log_client,
            log_session_key)
        VALUES
            (".$arr['user_id'].",
            ".time().",
            '".$_SERVER['REMOTE_ADDR']."',
            '".gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
            '".$_SERVER["HTTP_USER_AGENT"]."',
            '".$_SESSION[ROUNDID]['key']."')
            ;");

        //lädt sitter id
        $sittung_check_res = dbquery("
        SELECT
            user_sitting_sitter_user_id
        FROM
        	".$db_table['user_sitting']."
        WHERE
        	user_sitting_user_id='".$arr['user_id']."'
        	AND user_sitting_active='1';");
        $sittung_check_arr=mysql_fetch_array($sittung_check_res);

        //überprüft ob sich ein "anderer" user mit der gleichen ip eingeloggt hat (sitter ausgeschlossen)
        $ip_check_res = dbquery("
        SELECT
            user_id,
            user_nick,
            user_ip,
            user_hostname
        FROM
        	".$db_table['users']."
        WHERE
        	user_ip='".$_SERVER['REMOTE_ADDR']."'
        	AND user_id!='".$arr['user_id']."';");

        //überprüfung positiv...
        if (mysql_num_rows($ip_check_res)>0)
				{
					$multis="";
					$extrem_multi=0;
					$count=mysql_num_rows($ip_check_res);


					while($ip_check_arr=mysql_fetch_array($ip_check_res))
					{
                          //überprüft, ob diese users in der "multi liste" eingetragen sind...
                          $multi_check_res = dbquery("
                          SELECT
                              user_multi_id
                          FROM
                              ".$db_table['user_multi']."
                          WHERE
                              (user_multi_user_id='".$arr['user_id']."' OR user_multi_user_id='".$ip_check_arr['user_id']."')
                              AND (user_multi_multi_user_id='".$ip_check_arr['user_id']."' OR user_multi_multi_user_id='".$arr['user_id']."');");

						if($count<2)
						{
                              //wenn nicht: logt den regelverstoss
                              if ((mysql_num_rows($multi_check_res)<=1 && $_SESSION[ROUNDID]['user']['sitter_active']==0) || ($_SESSION[ROUNDID]['user']['sitter_active']==1 && $sittung_check_arr['user_sitting_sitter_user_id']!=$ip_check_arr['user_id']))
                              {
                                  //Der eigentliche sitte logt sich mit dem account pw ein
                                  if($sittung_check_arr['user_sitting_sitter_user_id']==$ip_check_arr['user_id'])
                                  {
                                      			add_log(9,"[URL=?page=user&sub=edit&user_id=".$ip_check_arr['user_id']."][B]".$ip_check_arr['user_nick']."[/B][/URL] hat sich bei [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$arr['user_nick']."[/B][/URL] mit dem Accountpasswort eingeloggt obwohl ".$ip_check_arr['user_nick']." momentan als Sitter tätig wäre. Dies ist regelwidrig!\nIP's: ".$_SERVER['REMOTE_ADDR']."",time());
                                  }
                                  //ein fremder logt sich mit dem sitter pw ein
                                  elseif($_SESSION[ROUNDID]['user']['sitter_active']==1 && $sittung_check_arr['user_sitting_sitter_user_id']!=$ip_check_arr['user_id'])
                                  {
                                      add_log(9,"[URL=?page=user&sub=edit&user_id=".$ip_check_arr['user_id']."][B]".$ip_check_arr['user_nick']."[/B][/URL] hat sich bei [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$arr['user_nick']."[/B][/URL] mit dem Sitterpasswort eingeloggt doch ".$ip_check_arr['user_nick']." ist nicht der eingetragene Sitter. Dies ist regelwidrig!\nIP's: ".$_SERVER['REMOTE_ADDR']."",time());
                                  }
                                  //ein fremder der nicht in der multi liste eingetragen ist logt sich mit dem account pw ein
                                  else
                                  {
                                  	add_log(9,"[URL=?page=user&sub=edit&user_id=".$ip_check_arr['user_id']."][B]".$ip_check_arr['user_nick']."[/B][/URL] hat sich bei [URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$arr['user_nick']."[/B][/URL] eingeloggt!\nIP's: ".$_SERVER['REMOTE_ADDR']."",time());
                                  }
                              }
                          }
                          else
                          {
                              if ((mysql_num_rows($multi_check_res)<=1 && $_SESSION[ROUNDID]['user']['sitter_active']==0) || ($_SESSION[ROUNDID]['user']['sitter_active']==1 && $sittung_check_arr['user_sitting_sitter_user_id']!=$ip_check_arr['user_id']))
                              {
                              	$extrem_multi=1;
                                  $multis.="[URL=?page=user&sub=edit&user_id=".$ip_check_arr['user_id']."] [B]".$ip_check_arr['user_nick']."[/B] [/URL]\n";
                              }
                          }


                      }

                      if($extrem_multi==1)
                      {
                      	add_log(9,"Ein User hat sich unerlaubt in mehrer Accounts eingelogt. Folgene Accounts sind betroffen:\n[URL=?page=user&sub=edit&user_id=".$arr['user_id']."][B]".$arr['user_nick']."[/B][/URL]\n".$multis."\nIP's: ".$_SERVER['REMOTE_ADDR']."",time());
                      }

				}


				//dbclose();
			}
			else
			{
				$res = dbquery("
				SELECT
					user_id
				FROM
					users
				WHERE
	      	LCASE(user_nick)='".strtolower($_POST['login_nick'])."' 
				");
				if (mysql_num_rows($res)>0) 
				{
					$arr = mysql_Fetch_row($res);
					dbquery("
					INSERT INTO
						login_failures
					(
					 	failure_time,
					 	failure_ip,
					 	failure_host,
					 	failure_user_id
					 )
					 VALUES
					 (
					 	".time().",
					 	'".$_SERVER['REMOTE_ADDR']."',
					 	'".gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
					 	".$arr[0]."					 	
					 )					 	
					;");
				}
				
				header("Location: ".LOGINSERVER_URL."?page=err&err=pass");
				echo "<h1>Nickname oder Passwort unbekannt</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=pass\">hier</a> klicken...";
				exit;
			}
		}
		else
		{
			header("Location: ".LOGINSERVER_URL."?page=err&err=name");
			echo "<h1>Nickname oder Password fehlerhaft</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=name\">hier</a> klicken...";
			exit;
		}
	}
	else
	{
		header("Location: ".LOGINSERVER_URL."?page=err&err=general");
		echo "<h1>Login fehlerhaft</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=general\">hier</a> klicken...";
		exit;
	}

	// Prüfen ob Session mit Inhalt da ist
	if (count($_SESSION[ROUNDID]['user'])==0)
	{
		header("Location: ".LOGINSERVER_URL."?page=err&err=session");
		echo "<h1>Session fehlerhaft</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=session\">hier</a> klicken...";
		exit;
	}


	//	header("Location: .");
	//	echo "<h1>Login erfolgreich</h1>Falls die Weiterleitung nicht klappt, <a href=\"index.php\">hier</a> klicken...";
	//	exit;
	//}
	//else
	//{
	$firstview = true;

?>
