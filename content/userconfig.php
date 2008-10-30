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
	// 	File: userconfig.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Configure personal settings
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	echo "<h1>Einstellungen</h1>"; 
		
/****************/
/* Menu			*/
/****************/

	$mode = (isset($_GET['mode']) && $_GET['mode']!="") ? $_GET['mode'] : 'general';
	
 	show_tab_menu("mode",array("general"=>"Profil",
 														"game"=>"Spiel",
 														"messages"=>"Nachrichten",
 														"design"=>"Design",
 														"sitting"=>"Sitting",
 														"password"=>"Passwort",
 														"logins"=>"Logins",
 														"misc"=>"Sonstiges"));
 	echo '<br/>';

/********************************/
/* Formular Bearbeitungen		*/
/********************************/
	// Änderung beantragen
	//

	if (isset($_GET['request']) && $_GET['request']!="")
	{
		if ($_POST['submit']!="")
		{
    		if($_POST['value']!="" && $_POST['comment'])
        {
            switch ($_GET['request'])
            {
                //Normaler Name
                case "change_name":
                    if(mysql_num_rows(dbquery("SELECT request_id FROM user_requests WHERE request_user_id='".$cu->id()."' AND request_field='user_name';"))>0)
                    {
                        echo "Du hast schon einen Anfrage geschrieben!<br>";
                    }
                    else
                    {
                        dbquery("
                        INSERT INTO 
                        user_requests 
                          (request_user_id,
                          request_field,
                          request_value,
                          request_comment,
                          request_timestamp) 
                        VALUES
                          ('".$cu->id()."',
                          'user_name',
                          '".addslashes($_POST['value'])."',
                          '".addslashes($_POST['comment'])."',
                          '".time()."');");
                        echo "Deine Anfrage wurde &uuml;bermittelt!<br>";
                    }
                		break;
                
                //Nickname
                case "change_nick":
                    if(mysql_num_rows(dbquery("SELECT request_id FROM user_requests WHERE request_user_id='".$cu->id()."' AND request_field='user_nick';"))>0)
                    {
                        echo "Du hast schon einen Anfrage geschrieben!<br>";
                    }
                    else
                    {
                        dbquery("
                        INSERT INTO 
                        user_requests 
                          (request_user_id,
                          request_field,
                          request_value,
                          request_comment,
                          request_timestamp) 
                        VALUES
                          ('".$cu->id()."',
                          'user_nick',
                          '".addslashes($_POST['value'])."',
                          '".addslashes($_POST['comment'])."',
                          ".time().");");
                        echo "Deine Anfrage wurde &uuml;bermittelt!<br>";
                    }
               			break;
                
                //Email
                case "change_email":
                    if(mysql_num_rows(dbquery("SELECT request_id FROM user_requests WHERE request_user_id='".$cu->id()."' AND request_field='user_email_fix';"))>0)
                    {
                        echo "Du hast schon einen Anfrage geschrieben!<br>";
                    }
                    else
                    {
                        dbquery("
                        INSERT INTO 
                        user_requests 
                          (request_user_id,
                          request_field,
                          request_value,
                          request_comment,
                          request_timestamp) 
                        VALUES
                          ('".$cu->id()."',
                          'user_email_fix',
                          '".addslashes($_POST['value'])."',
                          '".addslashes($_POST['comment'])."',
                          ".time().");");
                        echo "Deine Anfrage wurde &uuml;bermittelt!<br>";
                    }
                		break;
                
                //Falsche Eingabe
                default:
                    die("Fehler! Falsche Aktion eingegeben!");
            }
            echo "<br><input type=\"button\" value=\"Zu den Einstellungen\" onclick=\"document.location='?page=$page'\" />";
        }
        else
        {
          echo "Du must alle Felder ausf&uuml;llen!<br><br>";
          echo "<input type=\"button\" value=\"Zu den Einstellungen\" onclick=\"document.location='?page=$page'\" />";
        }
		}
		//Änderung beantragen
		else
		{
			echo "<form action=\"?page=$page&amp;request=".$_GET['request']."\" method=\"post\">";

			switch ($_GET['request'])
			{
				case "change_name":
					tableStart("&Auml;nderungsantrag: Vollst&auml;ndiger Name");
					$res=dbquery("SELECT user_name FROM users WHERE user_id='".$cu->id()."';");
					$arr=mysql_fetch_row($res);
					$oldval=$arr[0];
					break;
				case "change_nick":
					tableStart("&Auml;nderungsantrag: Benutzername");
					$res=dbquery("SELECT user_nick FROM users WHERE user_id='".$cu->id()."';");
					$arr=mysql_fetch_row($res);
					$oldval=$arr[0];
					break;
				case "change_email":
					tableStart("&Auml;nderungsantrag: Fixe E-Mail");
					$res=dbquery("SELECT user_email_fix FROM users WHERE user_id='".$cu->id()."';");
					$arr=mysql_fetch_row($res);
					$oldval=$arr[0];
					break;
				default:
					die("Fehler! Falsche Aktion eingegeben!");
			}
			echo "<tr><th class=\"tbltitle\">Alter Wert:</th><td class=\"tbldata\">".$oldval."</td></tr>";
			echo "<tr><th class=\"tbltitle\">Neuer Wert:</th><td class=\"tbldata\"><input type=\"text\" name=\"value\" value=\"\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Bemerkungen:</th><td class=\"tbldata\"><textarea name=\"comment\" cols=\"70\" rows=\"5\"></textarea></td></tr>";
			tableEnd();
			echo "<input type=\"submit\" name=\"submit\" value=\"&Uuml;bermitteln\" /> &nbsp; <input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
		}
	}




/************************/
/* Seiten Ansicht		*/
/************************/

	//
	// Allgemein
	//

	else
	{
    /****************/
    /* Spiel    */
    /****************/
		if($mode=='game')
		{
 			require("content/userconfig/game.php");
		}
		
    /****************/
    /* Nachrichten    */
    /****************/
		elseif($mode=='messages')
		{
      if($s['sitter_active']==0)
      {
      	require("content/userconfig/messages.php");
      }
      else
      {
        echo "Im Sittermodus ist dieser Bereich gesperrt!";
      }
		}

    /****************/
    /* Design     	*/
    /****************/
		elseif($mode=='design')
		{
			require("content/userconfig/design.php");
    }
     
		/****************/
		/* Sitting			*/
		/****************/
   	elseif($mode=='sitting')
    {
      if($s['sitter_active']==0)
      {
      	require("content/userconfig/sitting.php");
      }
      else
      {
        echo "Im Sittermodus ist dieser Bereich gesperrt!<br><br>";
        
        // Aktive Sittingdaten Anzeigen
        tableStart("Sitter Einstellungen");
        echo "<tr><td class=\"tbldata\"><div align=\"center\"><b>Modus aktiv!</b></div></td></tr>";

        $date_res = dbquery("
        SELECT
            *
        FROM
            user_sitting_date
        WHERE
            user_sitting_date_user_id='".$cu->id()."'
            AND user_sitting_date_from!=0
            AND user_sitting_date_to!=0
        ORDER BY
            user_sitting_date_from;");

        echo "<tr><td class=\"tbldata\"><div align=\"center\">";
        while ($date_arr=mysql_fetch_array($date_res))
        {
            if($date_arr['user_sitting_date_to']<time())
            {
                echo "<span style=\"color:#f00\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</span><br>";
            }
            elseif($date_arr['user_sitting_date_from']<time() && $date_arr['user_sitting_date_to']>time())
            {
                echo "<span style=\"color:#0f0\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</span><br>";
            }
            else
            {
                echo "Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."<br>";
            }
        }
        echo "</td></tr>";
        tableEnd();
      }
    }

		/****************/
		/* Passwort			*/
		/****************/
		elseif($mode=='password')
  	{
      if($s['sitter_active']==0)
      {
      	require("content/userconfig/password.php");
    	}
      else
      {
      	echo "Im Sittermodus ist dieser Bereich gesperrt!";
      }
    }

		/****************/
		/* Sonstiges		*/
		/****************/
		elseif($mode=='misc')
		{
        if($s['sitter_active']==0)
        {
        	require("content/userconfig/misc.php");
      	}
        else
        {
        	echo "Im Sittermodus ist dieser Bereich gesperrt!";
        }
		}
		
		/****************/
		/* Logins		*/
		/****************/
		
		elseif ($mode=="logins")
		{
    	require("content/userconfig/logins.php");
		}
		
    /****************/
    /* Userdaten    */
    /****************/
		else
		{
        if($s['sitter_active']==0)
        {
					require("content/userconfig/general.php");
        }
        else
        {
          echo "Im Sittermodus ist dieser Bereich gesperrt!";
        }
		}
		
	}

?>