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

	// Prüfen ob der Login-Button gedrückt wurde
	if ($_POST['login_submit']!="")
	{
		$s = array();
		
		// Login-Eingaben prüfen
		if ($_POST['login_nick']!="" && $_POST['login_pw']!="" && !stristr($_POST['login_nick'],"'") && !stristr($_POST['login_pw'],"'"))
		{
      $login_ok=false;
      $sitter_login_ok=false;
			$login_time = time();

      // User-Nick und ID Laden
      $ures = dbquery("
      SELECT 
      	user_id,
      	user_registered,
      	user_nick 
      FROM 
      	users 
      WHERE 
      	LCASE(user_nick)='".strtolower($_POST['login_nick'])."' 
      ;");
			if (mysql_num_rows($ures)>0)
			{
				$uarr = mysql_fetch_row($ures);	
				$userId = $uarr[0];
				$userNick = $uarr[2];
				
				// Login with normal password
				$ures = dbquery("
	      SELECT 
	      	user_id
	      FROM 
	      	users 
	      WHERE 
	      	user_id=".$userId."
					AND user_password='".pw_salt($_POST['login_pw'],$uarr[1])."'
				");
				if (mysql_num_rows($ures)>0)
				{
					$login_ok=true;
				}				
				
				// Login with temporary plain text password
				$ures = dbquery("
	      SELECT 
	      	user_id
	      FROM 
	      	users 
	      WHERE 
	      	user_id=".$userId."		
	      	AND user_password_temp!=''	
					AND user_password_temp='".$_POST['login_pw']."'
	      ;");
				if (mysql_num_rows($ures)>0)
				{
					$login_ok=true;
					add_log(3,"In den Account ".$userNick." (".$userId.") wurde mit dem temporären Passwort ".$_POST['login_pw']." von ".$_SERVER['REMOTE_ADDR']." aus eingeloggt!");
				}		
				
	      // überprüft ob der sittmodus aktiviert ist und das passwort übereinstimmt
				$sres = dbquery("
				SELECT 
					user_sitting_id 
				FROM 
					user_sitting 
				WHERE 
					user_sitting_sitter_password='".md5($_POST['login_pw'])."' 
					AND user_sitting_user_id='".$userId."' 
					AND user_sitting_active='1'
				;");
				if (mysql_num_rows($sres)>0)
	      {
	      	$sitter_login_ok=true;
	      }					      
			}

			$userIp = $_SERVER['REMOTE_ADDR'];
			$userHost = resolveIp($_SERVER['REMOTE_ADDR']);
			$userAgent = $_SERVER['HTTP_USER_AGENT'];

			if ($login_ok || $sitter_login_ok)
			{
				$s['login_time'] = $login_time;			
				$s['user_id']	= $userId;
				$s['user_nick']	= $userNick;
				// Create session key: 
				// Byte 0-31: Logintime
				// Byte 32-63: User-Id
				// Byte 64-95: Round-Key
				// Byte 96-127: User-Host
				// Byte 128-159: User-Agent
				// Byte 161: Session-ID
				$s['key']=md5($s['login_time']).md5($s['user_id']).md5(ROUNDID).md5($userIp).md5($userAgent).session_id();
				
				// Do some sitter stuff
				if($sitter_login_ok==1 && $login_ok!=1)
				{
					$s['sitter_active']=1;
        	dbquery("
        	UPDATE
        	    users
        	SET
        	    user_last_online='".$login_time."',
        	    user_logintime=".$login_time.",
        	    user_acttime=".$login_time.",
        	    user_hostname='".$userHost."',
        	    user_client='".$userAgent."',
        	    user_session_key='".$s['key']."'
        	WHERE
        	    user_id='".$userId."';");
        	
        	dbquery("
        	UPDATE
        	    user_sitting
        	SET
        	    user_sitting_sitter_ip='".$userIp."'
        	WHERE
        	    user_sitting_user_id='".$userId."';");					
				}
				
				// Update user account with session information
				else
				{
					$s['sitter_active']=0;
        	dbquery("
        	UPDATE
        	    users
        	SET
        	    user_last_online='".$login_time."',
        	    user_logintime=".$login_time.",
        	    user_acttime=".$login_time.",
        	    user_ip='".$userIp."',
        	    user_client='".$userAgent."',
        	    user_hostname='".$userHost."',
        	    user_session_key='".$s['key']."'
        	WHERE
        	    user_id='".$userId."';");
				}

				// Log hinzufügen
        dbquery("
        INSERT INTO
        	user_sessionlog
            (log_user_id,
            log_logintime,
            log_ip,
            log_hostname,
            log_client,
            log_session_key)
        VALUES
            (".$userId.",
            ".$login_time.",
            '".$userIp."',
            '".$userHost."',
            '".$userAgent."',
            '".$s['key']."')
            ;");
            
        // Todo: Please Check
        //lädt sitter id
        $sittung_check_res = dbquery("
        SELECT
            user_sitting_sitter_user_id
        FROM
        	user_sitting
        WHERE
        	user_sitting_user_id='".$userId."'
        	AND user_sitting_active='1';");
        $sittung_check_arr=mysql_fetch_assoc($sittung_check_res);

        //überprüft ob sich ein "anderer" user mit der gleichen ip eingeloggt hat (sitter ausgeschlossen)
        $ip_check_res = dbquery("
        SELECT
            user_id,
            user_nick,
            user_ip,
            user_hostname
        FROM
        	users
        WHERE
        	user_ip='".$userIp."'
        	AND user_id!='".$userId."';");

        // überprüfung positiv...
        if (mysql_num_rows($ip_check_res)>0)
				{
					$multis="";
					$extrem_multi=0;
					$count=mysql_num_rows($ip_check_res);

					while($ip_check_arr=mysql_fetch_assoc($ip_check_res))
					{
            //überprüft, ob diese users in der "multi liste" eingetragen sind...
            $multi_check_res = dbquery("
            SELECT
                user_multi_id
            FROM
                user_multi
            WHERE
                (user_multi_user_id='".$userId."' OR user_multi_user_id='".$ip_check_arr['user_id']."')
                AND (user_multi_multi_user_id='".$ip_check_arr['user_id']."' OR user_multi_multi_user_id='".$userId."');");

						if($count<2)
						{
              //wenn nicht: logt den regelverstoss
              if ((mysql_num_rows($multi_check_res)<=1 && $s['sitter_active']==0) || ($s['sitter_active']==1 && $sittung_check_arr['user_sitting_sitter_user_id']!=$ip_check_arr['user_id']))
              {
                //Der eigentliche sitte logt sich mit dem account pw ein
                if($sittung_check_arr['user_sitting_sitter_user_id']==$ip_check_arr['user_id'])
                {
									add_log(9,"[URL=?page=user&sub=edit&user_id=".$ip_check_arr['user_id']."][B]".$ip_check_arr['user_nick']."[/B][/URL] hat sich bei [URL=?page=user&sub=edit&user_id=".$userId."][B]".$userNick."[/B][/URL] mit dem Accountpasswort eingeloggt obwohl ".$ip_check_arr['user_nick']." momentan als Sitter tätig wäre. Dies ist regelwidrig!\nIP's: ".$_SERVER['REMOTE_ADDR']);
                }
                //ein fremder logt sich mit dem sitter pw ein
                elseif($s['sitter_active']==1 && $sittung_check_arr['user_sitting_sitter_user_id']!=$ip_check_arr['user_id'])
                {
									add_log(9,"[URL=?page=user&sub=edit&user_id=".$ip_check_arr['user_id']."][B]".$ip_check_arr['user_nick']."[/B][/URL] hat sich bei [URL=?page=user&sub=edit&user_id=".$userId."][B]".$userNick."[/B][/URL] mit dem Sitterpasswort eingeloggt doch ".$ip_check_arr['user_nick']." ist nicht der eingetragene Sitter. Dies ist regelwidrig!\nIP's: ".$_SERVER['REMOTE_ADDR']);
                }
                //ein fremder der nicht in der multi liste eingetragen ist logt sich mit dem account pw ein
                else
                {
                	add_log(9,"[URL=?page=user&sub=edit&user_id=".$ip_check_arr['user_id']."][B]".$ip_check_arr['user_nick']."[/B][/URL] hat sich bei [URL=?page=user&sub=edit&user_id=".$userId."][B]".$userNick."[/B][/URL] eingeloggt!\nIP's: ".$_SERVER['REMOTE_ADDR']);
                }
              }
            }
            else
            {
              if ((mysql_num_rows($multi_check_res)<=1 && $s['sitter_active']==0) || ($s['sitter_active']==1 && $sittung_check_arr['user_sitting_sitter_user_id']!=$ip_check_arr['user_id']))
              {
              	$extrem_multi=1;
               	$multis.="[URL=?page=user&sub=edit&user_id=".$ip_check_arr['user_id']."] [B]".$ip_check_arr['user_nick']."[/B] [/URL]\n";
              }
            }
          }

          if($extrem_multi==1)
          {
          	add_log(9,"Ein User hat sich unerlaubt in mehrer Accounts eingelogt. Folgene Accounts sind betroffen:\n[URL=?page=user&sub=edit&user_id=".$userId."][B]".$userNick."[/B][/URL]\n".$multis."\nIP's: ".$_SERVER['REMOTE_ADDR']);
          }
				}
			}
			
			
			// Register Login failure
			else
			{
				$res = dbquery("
				SELECT
					user_id,
					user_email_fix,
					user_nick
				FROM
					users
				WHERE
	      	LCASE(user_nick)='".strtolower($_POST['login_nick'])."' 
				");
				if (mysql_num_rows($res)>0) 
				{
					$arr = mysql_fetch_row($res);
					dbquery("
					INSERT INTO
						login_failures
					(
					 	failure_time,
					 	failure_ip,
					 	failure_host,
					 	failure_user_id,
					 	failure_client
					 )
					 VALUES
					 (
					 	".$login_time.",
					 	'".$userIp."',
					 	'".$userHost."',
					 	".$arr[0].",
					 	'".$_SERVER['HTTP_USER_AGENT']."'					 	
					 )					 	
					;");
					$mres = dbquery("
					SELECT
						COUNT(failure_user_id)
					FROM
						login_failures
					WHERE
						failure_user_id=".$arr[0]."
						AND failure_time>".(time() - 6000)."
					");
					$marr = mysql_fetch_row($mres);
					if ($marr[0] > 3)
					{
						$text = "Hallo ".$arr[2]."\n\nSoeben haben wir 3 oder mehr fehlerhafte Loginversuche in deinen Account *".$arr[2]."* in *".ROUNDID."* festgestellt, 
zuletzt vom Computer ".$userIp." (".$userHost.") aus mit dem Passwort ".$_POST['login_pw'].". 
Sollten diese Logins nicht von dir verursacht worden sein dann nimm bitte *so bald wie möglich* Kontakt mit einem Admin auf 
(InGame auf Kontakt klicken oder im Forum anschreiben) damit wir Nachforschungen zu diesem Account-Hackversuch anstellen können.\n
Sind diese Logins von dir selbst verursacht, dann lösche bitte diese Mail unverzüglich, da darin Teile deines Passworts stehen könnten. \n
Freundliche Grüsse\nDas EtoA-Team\n\n
*Dies ist eine automatisch generierte Nachricht!*						
						";
						send_mail('',$arr[1],'Fehlerhafte Logins bei Escape to Andromeda',$text,'','');
					}
					
					
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
	if ($s['user_id']==0)
	{
		header("Location: ".LOGINSERVER_URL."?page=err&err=session");
		echo "<h1>Session fehlerhaft</h1>Falls die Weiterleitung nicht klappt, <a href=\"".LOGINSERVER_URL."?page=err&err=session\">hier</a> klicken...";
		exit;
	}
	
	$firstview = true;
?>