<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: userconfig.php												//
	// Topic: Benutzer-Einstellungen								//
	// Version: 0.1																	//
	// Letzte Änderung: 10.05.2006 Lamborghini									//
	//////////////////////////////////////////////////

	echo "<h1>Einstellungen</h1>"; 
		
/****************/
/* Menu			*/
/****************/

	$mode = (isset($_GET['mode']) && $_GET['mode']!="") ? $_GET['mode'] : 'general';
 	show_tab_menu("mode",array("general"=>"Allgemein","design"=>"Design","sitting"=>"Sitting","password"=>"Passwort","logins"=>"Logins","misc"=>"Sonstiges"));
 	echo '<br/>';

/********************************/
/* Formular Bearbeitungen		*/
/********************************/

	//
	// Urlaubsmodus einschalten
	//

	if ($_GET['hmod']==1)
	{
		if (mysql_num_rows(dbquery("SELECT shiplist_id FROM ".$db_table['shiplist']." WHERE shiplist_user_id='".$s['user']['id']."' AND shiplist_build_count>0;"))==0)
		{
			if (mysql_num_rows(dbquery("SELECT deflist_id FROM ".$db_table['deflist']." WHERE deflist_user_id='".$s['user']['id']."' AND deflist_build_count>0;"))==0)
			{
				if (mysql_num_rows(dbquery("SELECT buildlist_id FROM ".$db_table['buildlist']." WHERE buildlist_user_id='".$s['user']['id']."' AND buildlist_build_start_time>0;"))==0)
				{
					if (mysql_num_rows(dbquery("SELECT techlist_id FROM ".$db_table['techlist']." WHERE techlist_user_id='".$s['user']['id']."' AND techlist_build_start_time>0;"))==0)
					{
						if (mysql_num_rows(dbquery("SELECT fleet_id FROM ".$db_table['fleet']." WHERE fleet_user_id='".$s['user']['id']."';"))==0)
						{
							$hfrom=time();
							$hto=$hfrom+(MIN_UMOD_TIME*24*3600);
							if (dbquery("UPDATE ".$db_table['users']." SET user_hmode_from='$hfrom',user_hmode_to='$hto' WHERE user_id='".$s['user']['id']."';"))
							{
								dbquery ("
								UPDATE 
									".$db_table['planets']." 
								SET 
									planet_last_updated='0' 
								WHERE 
									planet_user_id='".$s['user']['id']."';");
								
								$s['user']['hmode_from']=$hfrom;
								$s['user']['hmode_to']=$hto;
								ok_msg("Du bist nun im Urlaubsmodus bis ".date("d.m.Y H:i",$hto)."");
							}
						}
						else
							err_msg("Es sind noch Flotten unterwegs!");
					}
					else
						err_msg("Es sind noch Technologien in Entwicklung!");
				}
				else
					err_msg("Es sind noch Geb&auml;ude im Bau!");
			}
			else
				err_msg("Es sind noch Verteidigungsanlagen im Bau!");
		}
		else
			err_msg("Es sind noch Schiffe im Bau!");
	}

	//
	// Urlaubsmodus aufheben
	//

	if ($_GET['hmod']==2)
	{
		if ($s['user']['hmode_from']>0 && $s['user']['hmode_from']<time() && $s['user']['hmode_to']<time())
		{
			dbquery("UPDATE ".$db_table['users']." SET user_hmode_from=0,user_hmode_to=0 WHERE user_id='".$s['user']['id']."';");
			dbquery ("UPDATE ".$db_table['planets']." SET planet_last_updated=".time()." WHERE planet_user_id='".$s['user']['id']."';");
			$s['user']['hmode_from']=0;
			$s['user']['hmode_to']=0;
			ok_msg("Urlaubsmodus aufgehoben!");
		}
		else
		{
			err_msg("Urlaubsmodus kann nicht aufgehoben werden!");
		}
	}


	//
	// User löschen
	//

	if ($_POST['remove_submit']!="")
	{
		if (mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_password='".md5($_POST['remove_password'])."' AND user_id=".$s['user']['id'].";"))>0)
		{
			if (delete_user($s['user']['id'],true))
			{
				$sc=Null;
				session_destroy();
				echo "Deine Daten wurden gel&ouml;scht! Wir w&uuml;nschen weiterhin viel Erfolg im Netz!<br/><br/>
				<a href=\"index.php\" target=\"_top\">Zur Startseite</a>";
			}
			else
				echo "Es trat ein Fehler auf und deine Daten konnten nicht gel&ouml;scht werden!<br/><br/><a href=\"?page=$page\">Zur&uuml;ck</a>";
		}
		else
			echo "Falsches Passwort!<br/><br/><a href=\"?page=$page\">Zur&uuml;ck</a>";
	}

	//
	// Änderung beantragen
	//

	elseif ($_GET['request']!="")
	{
		if ($_POST['submit']!="")
		{
    		if($_POST['value']!="" && $_POST['comment'])
        {
            switch ($_GET['request'])
            {
                //Normaler Name
                case "change_name":
                    if(mysql_num_rows(dbquery("SELECT request_id FROM ".$db_table['user_requests']." WHERE request_user_id='".$s['user']['id']."' AND request_field='user_name';"))>0)
                    {
                        echo "Du hast schon einen Anfrage geschrieben!<br>";
                    }
                    else
                    {
                        dbquery("
                        INSERT INTO 
                        ".$db_table['user_requests']." 
                          (request_user_id,
                          request_field,
                          request_value,
                          request_comment,
                          request_timestamp) 
                        VALUES
                          ('".$s['user']['id']."',
                          'user_name',
                          '".addslashes($_POST['value'])."',
                          '".addslashes($_POST['comment'])."',
                          '".time()."');");
                        echo "Deine Anfrage wurde &uuml;bermittelt!<br>";
                    }
                		break;
                
                //Nickname
                case "change_nick":
                    if(mysql_num_rows(dbquery("SELECT request_id FROM ".$db_table['user_requests']." WHERE request_user_id='".$s['user']['id']."' AND request_field='user_nick';"))>0)
                    {
                        echo "Du hast schon einen Anfrage geschrieben!<br>";
                    }
                    else
                    {
                        dbquery("
                        INSERT INTO 
                        ".$db_table['user_requests']." 
                          (request_user_id,
                          request_field,
                          request_value,
                          request_comment,
                          request_timestamp) 
                        VALUES
                          ('".$s['user']['id']."',
                          'user_nick',
                          '".addslashes($_POST['value'])."',
                          '".addslashes($_POST['comment'])."',
                          ".time().");");
                        echo "Deine Anfrage wurde &uuml;bermittelt!<br>";
                    }
               			break;
                
                //Email
                case "change_email":
                    if(mysql_num_rows(dbquery("SELECT request_id FROM ".$db_table['user_requests']." WHERE request_user_id='".$s['user']['id']."' AND request_field='user_email_fix';"))>0)
                    {
                        echo "Du hast schon einen Anfrage geschrieben!<br>";
                    }
                    else
                    {
                        dbquery("
                        INSERT INTO 
                        ".$db_table['user_requests']." 
                          (request_user_id,
                          request_field,
                          request_value,
                          request_comment,
                          request_timestamp) 
                        VALUES
                          ('".$s['user']['id']."',
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
					infobox_start("&Auml;nderungsantrag: Vollst&auml;ndiger Name",1);
					$res=dbquery("SELECT user_name FROM ".$db_table['users']." WHERE user_id='".$s['user']['id']."';");
					$arr=mysql_fetch_row($res);
					$oldval=$arr[0];
					break;
				case "change_nick":
					infobox_start("&Auml;nderungsantrag: Benutzername",1);
					$res=dbquery("SELECT user_nick FROM ".$db_table['users']." WHERE user_id='".$s['user']['id']."';");
					$arr=mysql_fetch_row($res);
					$oldval=$arr[0];
					break;
				case "change_email":
					infobox_start("&Auml;nderungsantrag: Fixe E-Mail",1);
					$res=dbquery("SELECT user_email_fix FROM ".$db_table['users']." WHERE user_id='".$s['user']['id']."';");
					$arr=mysql_fetch_row($res);
					$oldval=$arr[0];
					break;
				default:
					die("Fehler! Falsche Aktion eingegeben!");
			}
			echo "<tr><th class=\"tbltitle\">Alter Wert:</th><td class=\"tbldata\">".$oldval."</td></tr>";
			echo "<tr><th class=\"tbltitle\">Neuer Wert:</th><td class=\"tbldata\"><input type=\"text\" name=\"value\" value=\"\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Bemerkungen:</th><td class=\"tbldata\"><textarea name=\"comment\" cols=\"70\" rows=\"5\"></textarea></td></tr>";
			infobox_end(1);
			echo "<input type=\"submit\" name=\"submit\" value=\"&Uuml;bermitteln\" /> &nbsp; <input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
		}
	}

	//
	// Löschbestätigung
	//

	elseif ($_GET['remove']!="")
	{
		echo "<form action=\"?page=$page\" method=\"post\">";
		echo "<p align=\"center\">Soll dein Account wirklich gel&ouml;scht werden? <br/>(R&uuml;ckg&auml;ngig machen nicht m&ouml;glich!!!)</p>";
		echo "<p align=\"center\"><b>Passwort eingeben:</b> <input type=\"password\" name=\"remove_password\" value=\"\" /><br/><br/>";
		echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /> <input type=\"submit\" name=\"remove_submit\" value=\"Account l&ouml;schen\" /></p>";
		echo "</form>";
	}



/************************/
/* Seiten Ansicht		*/
/************************/

	//
	// Allgemein
	//

	else
	{
		$res = dbquery("SELECT * FROM ".$db_table['users']." WHERE user_id='".$s['user']['id']."';");
		$arr = mysql_fetch_array($res);
		$themes = get_imagepacks();
		$designs = get_css_style();


		//change Theme skript
		echo "<script type=\"text/javascript\">";
		echo "function changeTheme()\n{";
		echo "var id=document.getElementById('user_image_select').options[document.getElementById('user_image_select').selectedIndex].value;\n";
		echo "switch (id)\n{";
		foreach ($themes as $k => $v)
		{
			echo "case '$k': document.getElementById('user_image_url').value='".$k."';\n document.getElementById('user_image_ext').value='".$v['type']."'\n;break;";
		}
		echo "default: document.getElementById('user_image_url').value='';document.getElementById('user_image_ext').value='';";
		echo "}\n";
		echo "}\n</script>";



    /****************/
    /* Userdaten    */
    /****************/

		if($_GET['mode']=='general' || $_GET['mode']=='')
		{
        //Sperrt Seite für Sitter
        if($s['user']['sitter_active']==0)
        {
          	// Datenänderung übernehmen
            if ($_POST['data_submit']!="" && checker_verify())
            {
              if (checkEmail($_POST['user_email']))
              {
                $avatar_string="";
                if ($_POST['avatar_del']==1)
                {
                  if (file_exists(BOARD_AVATAR_DIR."/".$arr['user_avatar']))
                  {
                      @unlink(BOARD_AVATAR_DIR."/".$arr['user_avatar']);
                  }
                  $avatar_string="user_avatar='',";
                }
                elseif ($_FILES['user_avatar_file']['tmp_name']!="")
                {
                    $source=$_FILES['user_avatar_file']['tmp_name'];
                    $ims = getimagesize($source);
                    
                    //überprüft Bildgrösse
                    if ($ims[0]==BOARD_AVATAR_WIDTH && $ims[1]==BOARD_AVATAR_HEIGHT)
                    {
                        $fname = "user_".$s['user']['id']."_".time().".gif";
                        if (file_exists(BOARD_AVATAR_DIR."/".$arr['user_avatar']))
                            @unlink(BOARD_AVATAR_DIR."/".$arr['user_avatar']);
                        move_uploaded_file($source,BOARD_AVATAR_DIR."/".$fname);
                        echo "Eigenen Avatar gespeichert!<br/>";
                        $avatar_string="user_avatar='".$fname."',";
                    }
                    else
                    {
                        echo "Fehler! Das Avatarbild hat die falsche Gr&ouml;sse!<br/>";
                    }
                }
                  dbquery("
                  UPDATE
                      ".$db_table['users']."
                  SET
                      user_email='".$_POST['user_email']."',
                      user_profile_text='".addslashes($_POST['user_profile_text'])."',
                      user_signature='".addslashes($_POST['user_signature'])."',
                      user_msgsignature='".addslashes($_POST['user_msgsignature'])."',
                      $avatar_string
                      user_profile_img_url='".$_POST['user_profile_img_url']."'
                  WHERE
                      user_id='".$s['user']['id']."';");
                  echo "<br>Benutzer-Daten wurden ge&auml;ndert!<br/><br/>";
                  
                  $res = dbquery("SELECT * FROM ".$db_table['users']." WHERE user_id='".$s['user']['id']."';");
                  $arr = mysql_fetch_array($res);
              }
              else
                    echo "<b>Fehler!</b> Die E-Mail-Adresse ist nicht korrekt!<br/><br/>";
            }

            echo "<form action=\"?page=$page&mode=general\" method=\"post\" enctype=\"multipart/form-data\">";
            $cstr = checker_init();
            infobox_start("Benutzeroptionen",1);
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Vollst&auml;ndiger Name:</th>
            	<td class=\"tbldata\" width=\"65%\">".$arr['user_name']." [<a href=\"?page=$page&amp;request=change_name\">&Auml;nderung beantragen</a>]</td>
            </tr>";
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Benutzername:</th>
            	<td class=\"tbldata\" width=\"65%\">".$arr['user_nick']." [<a href=\"?page=$page&amp;request=change_nick\">&Auml;nderung beantragen</a>]</td>
            </tr>";
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Fixe E-Mail:</th>
            	<td class=\"tbldata\" width=\"65%\">".$arr['user_email_fix']." [<a href=\"?page=$page&amp;request=change_email\">&Auml;nderung beantragen</a>]</td>
            </tr>";
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">E-Mail:</th>
            	<td class=\"tbldata\" width=\"65%\"><input type=\"text\" name=\"user_email\" maxlength=\"255\" size=\"30\" value=\"".$arr['user_email']."\"></td>
            </tr>";
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Beschreibung:</th>
            	<td class=\"tbldata\"><textarea name=\"user_profile_text\" cols=\"50\" rows=\"10\" width=\"65%\">".stripslashes($arr['user_profile_text'])."</textarea></td>
            </tr>";
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Allianzforum-Signatur:</th>
            	<td class=\"tbldata\"><textarea name=\"user_signature\" cols=\"50\" rows=\"2\" width=\"65%\">".stripslashes($arr['user_signature'])."</textarea></td>
            </tr>";
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Nachrichten-Signatur:</th>
            	<td class=\"tbldata\"><textarea name=\"user_msgsignature\" cols=\"50\" rows=\"2\" width=\"65%\">".stripslashes($arr['user_msgsignature'])."</textarea></td>
            </tr>";
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Avatar:</th>
            	<td class=\"tbldata\">";
	            if ($arr['user_avatar']!=BOARD_DEFAULT_IMAGE && $arr['user_avatar']!="")
	            {
	              show_avatar($arr['user_avatar']);
	              echo "<input type=\"checkbox\" value=\"1\" name=\"avatar_del\"> Avatar l&ouml;schen<br/>";
	            }
            	echo "Eigener Avatar heraufladen/&auml;ndern (".BOARD_AVATAR_WIDTH."*".BOARD_AVATAR_HEIGHT." Pixel, GIF): <input type=\"file\" name=\"user_avatar_file\" /></td>
            </tr>";
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">User-Bild-URL:</th>
            	<td class=\"tbldata\" width=\"65%\"><input type=\"text\" name=\"user_profile_img_url\" maxlength=\"255\" size=\"30\" value=\"".$arr['user_profile_img_url']."\"></td>
            </tr>";

            infobox_end(1);

            echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
            echo "</form><br/><br/>";
        }
        else
        {
          echo "Im Sittermodus ist dieser Bereich gesperrt!";
        }

		}



    /****************/
    /* Design     	*/
    /****************/

		elseif($_GET['mode']=='design')
		{
				//
        // Daten werden gespeichert
        //
        
        if ($_POST['data_submit_design']!="")
        {
          //Prüft eingaben auf unerlaube Zeichen
          $check_image = check_illegal_signs($_POST['user_image_url']);
          $check_ext = check_illegal_signs($_POST['user_image_ext']);
          if ($check_image=="" && $check_ext=="")
          {
              if($_POST['user_image_ext']!="" && $_POST['user_image_url']!="")
              {
                  //Wandelt alle \ (backslash) in / um (Da windows den pfad mit \ angibt!)
                  $grafikpack = str_replace("\\", "/", $_POST['user_image_url']);
                  echo "<h1>Design wird ge&auml;ndert...</h1>";
                  if (dbquery("
                  UPDATE
                      ".$db_table['users']."
                  SET
                      user_css_style='".$_POST['user_css_style']."',
                      user_game_width='".$_POST['user_game_width']."',
                      user_planet_circle_width='".$_POST['user_planet_circle_width']."',
                      user_image_url='".$grafikpack."',
                      user_image_ext='".$_POST['user_image_ext']."',
                      user_item_show='".$_POST['user_item_show']."',
                      user_image_filter='".$_POST['user_image_filter']."',
                      user_msg_preview='".$_POST['user_msg_preview']."',
                      user_msgcreation_preview='".$_POST['user_msgcreation_preview']."',
                      user_helpbox='".$_POST['user_helpbox']."'                          
                  WHERE
                      user_id='".$s['user']['id']."';"))
                  {
                      $s['user']['css_style']=$_POST['user_css_style'];
                      $s['user']['game_width']=$_POST['user_game_width'];
                      $s['user']['planet_circle_width']=$_POST['user_planet_circle_width'];
                      $s['user']['image_url']=$_POST['user_image_url'];
                      $s['user']['image_ext']=$_POST['user_image_ext'];
                      $s['user']['item_show']=$_POST['user_item_show'];
                      $s['user']['image_filter']=$_POST['user_image_filter'];
                      
                      echo "Design-Daten wurden ge&auml;ndert!";
                  }
                  
                  echo "<script type=\"text/javascript\">
                  location='?page=$page&mode=design';
                  </script>";
              }
              else
              {
                echo "Du hast keinen Bildpfad oder keine Dateiendung angeben!<br>";
              }
          }
          else
          {
            if($check_ext!="")
              $signs=$check_ext;

            if($check_image!="")
              $signs=$check_image;

            echo "Du hast ein unerlaubtes Zeichen ( ".$signs." ) im Grafikpfad oder in der Dateiendung!";
          }
        }



				//
				//Formular
				//
				
        echo "<form action=\"?page=$page&mode=design\" method=\"post\">";
        echo $cstr;
        infobox_start("Designoptionen",1);
        
        //Design wählen
        echo "<tr>
            <th class=\"tbldata\" width=\"36%\">Design w&auml;hlen:</th>
            <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                    <select name=\"user_css_style\">";
                    foreach ($designs as $k => $v)
                    {
                        echo "<option value=\"$k\"";
                        if ($arr['user_css_style']==$k) echo " selected=\"selected\"";
                        echo ">".$v['name']."</option>";
                    }
                    echo "</select>";
        echo "</tr>";

        //Theme (Bildpacket) wählen
        echo "<tr>
                <th class=\"tbldata\" width=\"36%\">Bildpaket w&auml;hlen:</th>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                    <select id=\"user_image_select\" onchange=\"changeTheme()\">";
                    echo "<option value=\"\">(Selbstdefiniert)</option>";
                    foreach ($themes as $k => $v)
                    {
                        echo "<option value=\"$k\"";
                        if ($arr['user_image_url']==$k) echo " selected=\"selected\"";
                        echo ">".$v['name']."</option>";
                    }
                    echo "</select> 
                    <input type=\"text\" name=\"user_image_url\" id=\"user_image_url\" maxlength=\"255\" size=\"45\" value=\"".$arr['user_image_url']."\">
                </td>";
        echo "</tr>";

				//Dateiendung
        echo "<tr>
                <th class=\"tbldata\" width=\"36%\">Dateiendung:</th>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                    <input type=\"text\" name=\"user_image_ext\" id=\"user_image_ext\" maxlength=\"20\" size=\"3\" value=\"".$arr['user_image_ext']."\">
              </td>
             </tr>";

        //Spielgrösse
        echo "<tr>
                <th class=\"tbldata\" width=\"36%\">Spielgr&ouml;sse: (nur alte Designs)</th>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                    <select name=\"user_game_width\">";
                    for ($x=70;$x<=100;$x+=10)
                    {
                        echo "<option value=\"$x\"";
                        if ($s['user']['game_width']==$x) echo " selected=\"selected\"";
                        echo ">".$x."%</option>";
                    }
                    echo "</select> <span ".tm("Info","Das Spiel wurde optimiert f&uuml;r eine Aufl&ouml;sung von 1280*1024 Pixeln! Wenn du diese besitzt empfiehlt es sich bei den Classic Designs (Blue und Dark) eine Spielgr&ouml;sse von 80% zu w&auml;hlen. Bei einer kleineren Aufl&ouml;sung empfiehlt es sich eine Spielgr&ouml;sse von 100% einzustellen!",1)."><u>Info</u></span>
                </td>
             </tr>";

        //Planetkreisgrösse
        echo "<tr>
                <th class=\"tbldata\" width=\"36%\">Planetkreisgr&ouml;sse:</th>
                <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
                  <select name=\"user_planet_circle_width\">";
                  for ($x=450;$x<=700;$x+=50)
                  {
                      echo "<option value=\"$x\"";
                      if ($s['user']['planet_circle_width']==$x) echo " selected=\"selected\"";
                      echo ">".$x."</option>";
                  }
                echo "</select> <span ".tm("Info","Mit dieser Option l&auml;sst sich die gr&ouml;sse des Planetkreises in der &Uuml;bersicht einstellen.<br>Je nach Aufl&ouml;sung die du verwendest ist es beispielsweise nicht m&ouml;glich eine Gr&ouml;sse von 700 Pixeln zu haben. Finde selber heraus welche Gr&ouml;sse am besten Aussieht.",1)."><u>Info</u></span>
                </td>
            </tr>";
	
				//Schiff/Def Ansicht (Einfach/Voll)
        echo "<tr>
            		<th class=\"tbldata\" width=\"36%\">Schiff/Def Ansicht:</th>";
          echo "<td class=\"tbldata\" width=\"16%\">
          				<input type=\"radio\" name=\"user_item_show\" value=\"full\"";
          				if($arr['user_item_show']=='full') echo " checked=\"checked\"";
          				echo " /> Volle Ansicht 
          			</td>
          			<td class=\"tbldata\" width=\"48%\" colspan=\"3\">
           				<input type=\"radio\" name=\"user_item_show\" value=\"small\"";
          				if($arr['user_item_show']=='small') echo " checked=\"checked\"";
          				echo " /> Einfache Ansicht
           			</td>";
        echo "</tr>";


				//Bildfilter (An/Aus)
        echo "<tr>
            		<th class=\"tbldata\" width=\"36%\">Bildfilter:</th>";
          echo "<td class=\"tbldata\" width=\"16%\">
          				<input type=\"radio\" name=\"user_image_filter\" value=\"1\"";
          				if($arr['user_image_filter']==1) echo " checked=\"checked\"";
          				echo "/> An  
          			</td>
          			<td class=\"tbldata\" width=\"48%\" colspan=\"3\">
          				<input type=\"radio\" name=\"user_image_filter\" value=\"0\"";
          				if($arr['user_image_filter']==0) echo " checked=\"checked\"";
          				echo "/> Aus
          			</td>";
       	echo "</tr>";
            	
        //Nachrichtenvorschau (Neue/Archiv) (An/Aus)
    		echo "<tr>
    				 		<th class=\"tbldata\" width=\"36%\">Nachrichtenvorschau (Neue/Archiv):</th>
    						<td class=\"tbldata\" width=\"16%\">
                    <input type=\"radio\" name=\"user_msg_preview\" value=\"1\" ";
                    if ($arr['user_msg_preview']==1) echo " checked=\"checked\"";
                    echo "/> Aktiviert
                </td>
                <td class=\"tbldata\" width=\"48%\" colspan=\"3\">
                    <input type=\"radio\" name=\"user_msg_preview\" value=\"0\" ";
                    if ($arr['user_msg_preview']==0) echo " checked=\"checked\"";
                    echo "/> Deaktiviert
           			</td>
           	 </tr>";
           	     
          //Nachrichtenvorschau (Erstellen) (An/Aus)
          echo "<tr>
              		<th class=\"tbldata\" width=\"36%\">Nachrichtenvorschau (Erstellen):</th>
              		<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_msgcreation_preview\" value=\"1\" ";
                      if ($arr['user_msgcreation_preview']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                   </td>
                   <td class=\"tbldata\" width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"user_msgcreation_preview\" value=\"0\" ";
                      if ($arr['user_msgcreation_preview']==0) echo " checked=\"checked\"";
                      echo "/> Deaktiviert
                  </td>
               </tr>";
                 
					//Hilfefenster (Aktiviert/Deaktiviert)
          echo "<tr>
            			<th class=\"tbldata\" width=\"36%\">Separates Hilfefenster:</th>
            			<td class=\"tbldata\" width=\"16%\">
                      <input type=\"radio\" name=\"user_helpbox\" value=\"1\" ";
                      if ($arr['user_helpbox']==1) echo " checked=\"checked\"";
                      echo "/> Aktiviert
                  </td>
                  <td class=\"tbldata\" width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"user_helpbox\" value=\"0\" ";
                      if ($arr['user_helpbox']==0) echo " checked=\"checked\"";
            					echo "/> Deaktiviert
            		</td>
          		</tr>";            
            
            
            infobox_end(1);

            echo "<input type=\"submit\" name=\"data_submit_design\" value=\"&Uuml;bernehmen\"></form><br/><br/>";


            infobox_start("Grafikpakete",1,0);
            foreach ($themes as $k => $v)
            {
                echo "<tr><td class=\"tbldata\"><a href=\"$k.".$conf['imagepack_zip_format']['v']."\" target=\"_Blank\">".$v['name']."</a></td></tr>";

            }
            infobox_end(1);




/****************/
/* Sitting			*/
/****************/

      }
      elseif($_GET['mode']=='sitting')
      {
        //Sperrt Seite für Sitter
        if($s['user']['sitter_active']==0)
        {
              //
              // Neuer user anlegen, der am gleichen PC sitzt (multi)
              //

              if ($_POST['new_multi']!="" && checker_verify())
              {
                  dbquery("
                  INSERT INTO
                  ".$db_table['user_multi']."
                  (user_multi_user_id)
                  VALUES
                  ('".$s['user']['id']."');");

                  echo "Neuer User angelegt<br>";
              }


              //
              // Daten Speichern (multi)
              //

              if ($_POST['data_submit_multi']!="" && checker_verify())
              {

                  $user=array_unique($_POST['user_multi_multi_nick']); //löscht alle users die mehrfach eingetragen wurden
                  foreach ($user as $id=>$data)
                  {
                      if ($user[$id]!="")
                      {
                          //Ist dieser User existent
                          if (get_user_id($user[$id])==0)
                          {
                              $msg = "<b>Fehler:</b> Dieser User exisitert nicht!<br><br>";
                          }
                          //ist der eigene nick eingetragen
                          elseif (get_user_id($user[$id])==$s['user']['id'])
                          {
                              $msg = "<b>Fehler:</b> Du kannst nicht dich selber eintragen!<br><br>";
                          }
                          else
                          {
                              // Daten Speichern
                              dbquery("
                              UPDATE
                                  ".$db_table['user_multi']."
                              SET
                                  user_multi_multi_user_id='".addslashes(get_user_id($user[$id]))."',
                                  user_multi_connection='".addslashes($_POST['user_multi_connection'][$id])."'
                              WHERE
                                  user_multi_id=$id;");

                          }

                      }

                  }

                  //Löscht alle angekreuzten user
                  if($_POST['del_multi'])
                  {
                      // User löschen
                      foreach ($_POST['del_multi'] as $id=>$data)
                      {
                          if ($_POST['del_multi'][$id]==1)
                          {
                              dbquery("DELETE FROM ".$db_table['user_multi']." WHERE user_multi_id=$id;");

                              // Speichert jeden gelöschten multi (soll vor missbrauch schützen -> mutli erstellen -> löschen -> erstellen -> löschen etc.)
                              dbquery("
                              UPDATE
                                  ".$db_table['users']."
                              SET
                                  user_multi_delets=user_multi_delets+1
                              WHERE
                                  user_id='".$s['user']['id']."';");
                          }
                      }
                  }

                  if($msg!="")
                      echo "$msg";
                  else
                      echo "Daten &uuml;bernommen<br>";
              }



              //
              // Neues Sitting Datum erzeugen
              //

              if ($_POST['new_sitting_date']!="" && checker_verify())
              {
                  dbquery("
                  INSERT INTO
                  ".$db_table['user_sitting_date']."
                  (user_sitting_date_user_id)
                  VALUES
                  ('".$s['user']['id']."');");

                  echo "Neues Datumfeld erzeugt!<br>";
              }


              //
              // Daten Speichern (sitting)
              //

              if ($_POST['data_submit_sitting']!="" && checker_verify())
              {
                  //überprüft ob der angegebene user wirklich vorhanden ist
                  if (get_user_id($_POST['user_sitting_sitter_nick'])!=0 || $_POST['user_sitting_sitter_nick']=="")
                  {
                      if(get_user_id($_POST['user_sitting_sitter_nick'])!=$s['user']['id'])
                      {
                          $pw1 = md5($_POST['user_sitting_sitter_password1']);
                          $pw2 = md5($_POST['user_sitting_sitter_password2']);

                          if($pw1==$pw2)
                          {
                              if (strlen($_POST['user_sitting_sitter_password1'])>=PASSWORD_MINLENGHT || $_POST['user_sitting_sitter_password1']=="")
                              {
                                  //überprüft, ob das eingegebene sitterpasswort nicht gleich dem normalen passwort ist
                                  if(mysql_num_rows(dbquery("SELECT user_password FROM ".$db_table['users']." WHERE user_id='".$s['user']['id']."' AND user_password='$pw1';"))>0)
                                  {
                                      echo "Du kannst nicht das gleiche Passwort nehmen wie beim Account!<br>";
                                  }
                                  else
                                  {

                                      if($_POST['user_sitting_sitter_password1']=="")
                                          $password = 0;
                                      else
                                          $password = $pw1;


                                      $check_res = dbquery("
                                      SELECT
                                          user_sitting_id
                                      FROM
                                          ".$db_table['user_sitting']."
                                      WHERE
                                          user_sitting_user_id='".$s['user']['id']."';");
                                      if (mysql_num_rows($check_res)>0)
                                      {
                                          // Daten Speichern
                                          dbquery("
                                          UPDATE
                                              ".$db_table['user_sitting']."
                                          SET
                                              user_sitting_sitter_user_id='".get_user_id($_POST['user_sitting_sitter_nick'])."',
                                              user_sitting_sitter_password='".$password."'
                                          WHERE
                                              user_sitting_user_id='".$s['user']['id']."';");

                                      }
                                      else
                                      {
                                          dbquery("
                                          INSERT INTO
                                          ".$db_table['user_sitting']."
                                          (user_sitting_user_id,
                                          user_sitting_sitter_user_id,
                                          user_sitting_sitter_password)
                                          VALUES
                                          ('".$s['user']['id']."',
                                          '".get_user_id($_POST['user_sitting_sitter_nick'])."',
                                          '".$password."');");
                                      }
                                  }

                              }
                              else
                              {
                                  echo "Das Passwort muss mindestens ".PASSWORD_MINLENGHT." Zeichen lang sein!<br>";
                              }
                          }
                          else
                          {
                              echo "Die beiden Passw&ouml;rter m&uuml;ssen identisch sein!<br>";
                          }
                      }
                      else
                      {
                          echo "Du kannst nicht dich selbst zum Sitter ernennen!<br>";
                      }

                  }
                  else
                  {
                      echo "Ein Spieler mit dem Nick \"".$_POST['user_sitting_sitter_nick']."\" wurde nicht gefunden!<br>";
                  }


									//Sitting Daten löschen
                  if($_POST['del_sitting_date'])
                  {
                      // Sittingdaten löschen
                      foreach ($_POST['del_sitting_date'] as $id=>$data)
                      {
                          if ($_POST['del_sitting_date'][$id]==1)
                              dbquery("DELETE FROM ".$db_table['user_sitting_date']." WHERE user_sitting_date_id=".$id.";");
                      }
                  }


                  if($_POST['user_sitting_time']==0 && $_POST['user_sitting_time']!=NULL)
                  {
                      echo "Du must eine Zeitdauer beim Sitterdatum angeben!<br>";
                  }
                  else
                  {
                      $sitting_from = mktime($_POST['user_sitting_from_h'],$_POST['user_sitting_from_i'],0,$_POST['user_sitting_from_m'],$_POST['user_sitting_from_d'],$_POST['user_sitting_from_y']);
                      $sitting_to = $sitting_from + ($_POST['user_sitting_time']*3600*24);

                      //überprüft, ob das eingegebene datum nach dem letzten ist
                      $date_check_res = dbquery("
                      SELECT
                          user_sitting_date_to
                      FROM
                          ".$db_table['user_sitting_date']."
                      WHERE
                          user_sitting_date_user_id='".$s['user']['id']."'
                      ORDER BY
                          user_sitting_date_to DESC;");
                      $date_check_row = mysql_fetch_object($date_check_res);
                      $last_date = $date_check_row->user_sitting_date_to;

											//überprüft, ob das neue Datum nicht ein älteres schneidet
                      if($last_date<$sitting_from)
                      {
                          // Sittingzeit Speichern
                          dbquery("
                          UPDATE
                              ".$db_table['user_sitting_date']."
                          SET
                              user_sitting_date_from='".$sitting_from."',
                              user_sitting_date_to='".$sitting_to."'
                          WHERE
                              user_sitting_date_user_id='".$s['user']['id']."'
                              AND user_sitting_date_from='0'
                              AND user_sitting_date_to='0';");
                      }
                      else
                      {
                          if($_POST['user_sitting_time']!=NULL)
                          {
                              echo "Das Startdatum muss sp&auml;ter als das Enddatum vom vorherigen Datum sein!<br>";
                          }
                      }
                  }

              }

              //Sittermodus aktivieren

              if ($_POST['sitting_activade']!="" && checker_verify())
              {
                  //Errechnet Anzahl der Sittertage
                  $date_res = dbquery("
                  SELECT
                      user_sitting_date_from,
                      user_sitting_date_to
                  FROM
                      ".$db_table['user_sitting_date']."
                  WHERE
                      user_sitting_date_user_id='".$s['user']['id']."'
                      AND user_sitting_date_from!=0
                      AND user_sitting_date_to!=0
                  ORDER BY
                      user_sitting_date_from;");

                  $sitting_from=0;
                  $sitting_to=0;
                  while ($date_arr=mysql_fetch_array($date_res))
                  {
                      $sitting_from+=$date_arr['user_sitting_date_from'];
                      $sitting_to+=$date_arr['user_sitting_date_to'];
                  }
                  $sitting_days=($sitting_to-$sitting_from)/3600/24;

                  //Speichert alle nötigen Daten
                  dbquery("
                  UPDATE
                      ".$db_table['user_sitting']."
                  SET
                      user_sitting_active='1',
                      user_sitting_date='".time()."'
                  WHERE
                      user_sitting_user_id='".$s['user']['id']."';");

                  dbquery("
                  UPDATE
                      ".$db_table['users']."
                  SET
                      user_sitting_days=user_sitting_days-".$sitting_days."
                  WHERE
                      user_id='".$s['user']['id']."';");

                  //löscht daten beidenen keine zeit festgelegt ist
                  dbquery("DELETE FROM ".$db_table['user_sitting_date']." WHERE user_sitting_date_from=0 AND user_sitting_date_to=0;");

                  echo "Der Sittermodus wurde aktiviert!<br>";
              }


              //
              // Multierkennung
              //

              echo "<form action=\"?page=$page&mode=sitting\" method=\"post\">";
              $cstr = checker_init();

              $res = dbquery("
              SELECT
                  *
              FROM
                  ".$db_table['user_multi']."
              WHERE
                  user_multi_user_id='".$s['user']['id']."'
              ORDER BY
                  user_multi_id;");

              $user_res = dbquery("
              SELECT
                  user_sitting_days
              FROM
                  ".$db_table['users']."
              WHERE
                  user_id='".$s['user']['id']."';");
              $user_arr = mysql_fetch_array($user_res);

              infobox_start("Multierkennung [<a href=\"?page=help&site=multi_sitting\">Info</a>]",1);

              echo "<tr>
                      <th class=\"tbltitle\" width=\"35%\">User</th>
                      <th class=\"tbltitle\" width=\"55%\">Beziehung</th>
                      <th class=\"tbltitle\" width=\"10%\">L&ouml;schen</th>
                      ";

                      $unused_multi=0;
                      while ($arr = mysql_fetch_array($res))
                      {


                          echo "<tr><td class=\"tbldata\">";

                          if($arr['user_multi_multi_user_id']!=0)
                          {
                              echo "<input type=\"text\" name=\"user_multi_multi_nick[".$arr['user_multi_id']."]\" maxlength=\"20\" size=\"20\" value=\"".stripslashes(get_user_nick($arr['user_multi_multi_user_id']))."\" readonly=\"readonly\">";
                          }
                          else
                          {
                              echo "<input type=\"text\" name=\"user_multi_multi_nick[".$arr['user_multi_id']."]\" id=\"user_nick\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"Usernick\" onkeyup=\"xajax_searchUser(this.value);\"><br/>
              								<div id=\"citybox\">&nbsp;</div>";
                              $unused_multi++;
                          }

                          echo "</td>";
                          echo "<td class=\"tbldata\">";

                          if($arr['user_multi_connection']!='0')
                          {
                              echo "<input type=\"text\" name=\"user_multi_connection[".$arr['user_multi_id']."]\" maxlength=\"50\" size=\"50\" value=\"".stripslashes($arr['user_multi_connection'])."\" readonly=\"readonly\">";
                          }
                          else
                          {
                              echo "<input type=\"text\" name=\"user_multi_connection[".$arr['user_multi_id']."]\" maxlength=\"50\" size=\"50\" value=\"\">";
                          }

                          echo "</td>";
                          echo "<td class=\"tbldata\" style=\"text-align:center;\">";
                          echo "<input type=\"checkbox\" name=\"del_multi[".$arr['user_multi_id']."]\" value=\"1\" />";
                          echo "</td></tr>";

                      }
                      if($unused_multi<1 && $s['user']['sitter_active']==0)
                      {
                          echo "<tr><td class=\"tbldata\" style=\"text-align:center;\" colspan=\"3\"><input type=\"submit\" name=\"new_multi\" value=\"User hinzuf&uuml;gen\"/></td></tr>";
                      }

              infobox_end(1);

              echo "<input type=\"submit\" name=\"data_submit_multi\" value=\"&Uuml;bernehmen\"/></form><br/><br/><br>";



              //
              // Sitter einstellungen
              //
              //überprüft ob der user noch Sittertage zur verfügung hat
              if(intval($user_arr['user_sitting_days'])>0)
              {

                  $res = dbquery("
                  SELECT
                      *
                  FROM
                      ".$db_table['user_sitting']."
                  WHERE
                      user_sitting_user_id='".$s['user']['id']."'
                  ORDER BY
                      user_sitting_id;");
                  $arr = mysql_fetch_array($res);


                  //Sperrt Formular wenn der Modus aktiv ist
                  if($arr['user_sitting_active']==0)
                  {
                      infobox_start("Sitter Einstellungen [<a href=\"?page=help&site=multi_sitting\">Info</a>]",1);
                      echo "<form action=\"?page=$page&mode=sitting\" method=\"post\">";
                      echo $cstr;

                      $nick_class="tbldata2";
                      $nick_check=0;
                      $pw_class="tbldata2";
                      $pw_check=0;
                      $date_check=0;

          						//Prüft, ob bereits ein User angegen wurde
                      if($arr['user_sitting_sitter_user_id']!='0' && $arr['user_sitting_sitter_user_id']!=NULL)
                      {
                          $nick_class="tbldata";
                          $nick_check=1;
                      }

          						//Prüft, ob bereits ein passwort angegen wurde
                      if($arr['user_sitting_sitter_password']!='0' && $arr['user_sitting_sitter_password']!=NULL)
                      {
                          $pw_class="tbldata";
                          $pw_check=1;
                      }

                      //Listet alle eingetragenen daten aus, und ev. noch ein ausfüllfeld dazu
                      $date_res = dbquery("
                      SELECT
                          *
                      FROM
                          ".$db_table['user_sitting_date']."
                      WHERE
                          user_sitting_date_user_id='".$s['user']['id']."'
                          AND user_sitting_date_from!=0
                          AND user_sitting_date_to!=0
                      ORDER BY
                          user_sitting_date_from;");

                      $date_cnt = mysql_num_rows($date_res);
                      $rowspan=0;
                      $activade=0;
                      $sitting_from=0;
                      $sitting_to=0;

                      //Es ist noch kein Datum festgelegt worden
                      if($date_cnt==0)
                      {
                        //Zeichnet "Sitting Daten" farbig und dahinter eine leere Zeile
                          echo "<tr>
                              <td class=\"tbldata2\" width=\"35%\">Sitting Daten</td>
                              <td class=\"tbldata\" width=\"65%\" colspan=\"2\"></td>
                               </tr>";
                      }
                      //Min. 1 Datum ist festgelegt -> Anzeigen der übrigen Formularfelder
                      else
                      {
                          //Sitter Nick
                          echo "<tr>
                                  <td class=\"$nick_class\" width=\"35%\">Sitter Nick</td>
                                  <td class=\"tbldata\" width=\"65%\" colspan=\"2\">";

                                  if($arr['user_sitting_sitter_user_id']!=0)
                                  {
                                      echo "<input type=\"text\" name=\"user_sitting_sitter_nick\" maxlength=\"20\" size=\"20\" value=\"".stripslashes(get_user_nick($arr['user_sitting_sitter_user_id']))."\" id=\"user_nick\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value);\"><br/>
                                      <div id=\"citybox\">&nbsp;</div>";
                                  }
                                  else
                                  {
                                      echo "<input type=\"text\" name=\"user_sitting_sitter_nick\" maxlength=\"20\" size=\"20\" value=\"\" id=\"user_nick\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value);\"><br/>
                                      <div id=\"citybox\">&nbsp;</div>";
                                  }
                                  echo "</td>";
                          echo "</tr>";

                          //Sitter Passwort
                          echo "<tr>
                                  <td class=\"$pw_class\" width=\"35%\">Sitter Passwort</td>
                                  <td class=\"tbldata\" width=\"65%\" colspan=\"2\">
                                      <input type=\"password\" name=\"user_sitting_sitter_password1\" maxlength=\"20\" size=\"20\" value=\"\">
                                  </td>
                               </tr>";

                          //Sitter Passwort (wiederholen)
                          echo "<tr>
                                  <td class=\"$pw_class\" width=\"35%\">Sitter Passwort (wiederholen)</td>
                                  <td class=\"tbldata\" width=\"65%\" colspan=\"2\">
                                      <input type=\"password\" name=\"user_sitting_sitter_password2\" maxlength=\"20\" size=\"20\" value=\"\">
                                  </td>
                               </tr>";


                          echo "<td class=\"tbldata\" width=\"35%\" rowspan=\"$date_cnt\">Sitting Daten</td>";

                          //Listet alle festgelegten daten auf
                          while ($date_arr=mysql_fetch_array($date_res))
                          {

                              if(mysql_num_rows($date_res)!=$date_cnt)
                              {
                                  echo "<tr>";
                                  echo "<td class=\"tbldata\" width=\"35%\" rowspan=\"$date_cnt\">&nbsp;</td>";
                              }

                              echo "<td class=\"tbldata\" width=\"60%\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</td>";
                              echo "<td class=\"tbldata\" width=\"5%\" style=\"text-align:center;\"><input type=\"checkbox\" name=\"del_sitting_date[".$date_arr['user_sitting_date_id']."]\" value=\"1\" title=\"Datum l&ouml;schen\"/></td></tr>";

                              $sitting_from+=$date_arr['user_sitting_date_from'];
                              $sitting_to+=$date_arr['user_sitting_date_to'];
                          }
                          $date_check=1;
                          $date_cnt--;

                 			}

                    	echo "<tr><td class=\"tbldata\" colspan=\"3\"><div align=\"center\">";

                      $date_field_res = dbquery("
                      SELECT
                          user_sitting_date_id
                      FROM
                          ".$db_table['user_sitting_date']."
                      WHERE
                          user_sitting_date_user_id='".$s['user']['id']."'
                          AND user_sitting_date_to=0;");

                      $rest_days = $user_arr['user_sitting_days'];
                      $prof_rest_days = $rest_days - (($sitting_to-$sitting_from)/3600/24);
                      if(mysql_num_rows($date_field_res)==1)
                      {
                          echo "Von ";
                          show_timebox("user_sitting_from",time());
                          echo " Dauer (Tage) ";

                          // erstellt ein Optionsfeld mit den anzahl sitting tagen die der user noch zur verfügung hat
                          echo "<select name=\"user_sitting_time\">";
                          for ($x=0;$prof_rest_days>=$x;$x++)
                          {
                              if($prof_rest_days==0)
                                  echo "<option value=\"0\">-</option>";
                              else
                                  echo "<option value=\"$x\">$x</option>";
                          }
                          echo "</select>";
                          echo "<br>";
                      }

                      if(mysql_num_rows($date_field_res)==0)
                      {
                          if($user_arr['user_sitting_days']!=0 && $prof_rest_days>0)
                          {
                              echo "<br><input type=\"submit\" name=\"new_sitting_date\" value=\"Datum hinzuf&uuml;gen\" title=\"F&uuml;ge eine neue Sitterzugriffszeit ein\"/>";
                          }
                          else
                          {
                              echo "<br>Dir stehen keine weiteren Sittertage zur verf&uuml;gung!";
                          }
                      }
                      //wenn user,passwort und datum korrekt eingegeben sind, zeige button zum aktivieren
                      if($nick_check==1 && $pw_check==1 && $date_check==1)
                      {
                          echo "<br><br><input type=\"submit\" name=\"sitting_activade\" style=\"color:#0f0\" value=\"Sittingmodus aktivieren\" title=\"Aktiviert den Sittmodus mit den momentanen Daten\" onclick=\"return confirm('Wenn du diese Info best&auml;tigst wird der Modus mit sofortiger Wirkung aktiviert und kann nicht mehr ge&auml;ndert werden, du solltest dir also sicher sein, dass die Daten richtig eingegeben sind. Es werden die gespeicherten Daten &uuml;bernommen, was bedeutet, dass du zuerst alle Daten eingeben musst und diese mit einem Klick auf &Uuml;bernehmen best&auml;tigen solltest!');\" />";
                      }

                      echo "</div></td></tr>";
                      infobox_end(1);

                      echo "<input type=\"submit\" name=\"data_submit_sitting\" value=\"&Uuml;bernehmen\"/>";
                      echo "</form><br/><br/><br>";
                  }
                  else
                  {
                      infobox_start("Sitter Einstellungen",1);
                      echo "<tr><td class=\"tbldata\"><div align=\"center\"><b>Modus aktiv!</b></div></td></tr>";

                      $date_res = dbquery("
                      SELECT
                          *
                      FROM
                          ".$db_table['user_sitting_date']."
                      WHERE
                          user_sitting_date_user_id='".$s['user']['id']."'
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
                      infobox_end(1);
                  }
              }
              //Dem User stehen keine Sittertage mehr zur Verfügung
              else
              {
                infobox_start("Sitter Einstellungen",1);
                echo "<tr><td class=\"tbldata2\"><div align=\"center\">Dir stehen keine weiteren Sittertage zur verf&uuml;gung!</div></td></tr>";
                infobox_end(1);
              }
          }
          else
          {
            echo "Im Sittermodus ist dieser Bereich gesperrt!";
          }
      }

/****************/
/* Passwort			*/
/****************/

		elseif($_GET['mode']=='password')
    	{
    		//Sperrt Seite für Sitter
            if($s['user']['sitter_active']==0)
            {
                // Änderungen speichern
                if ($_POST['password_submit']!="" && checker_verify())
                {
                    if (mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_password='".md5($_POST['user_password'])."' AND user_id=".$s['user']['id'].";"))>0)
                    {
                        if (mysql_num_rows(dbquery("SELECT user_sitting_sitter_password FROM ".$db_table['user_sitting']." WHERE user_sitting_sitter_password='".md5($_POST['user_password1'])."' AND user_sitting_user_id=".$s['user']['id'].";"))==0)
                        {
                            if ($_POST['user_password1']==$_POST['user_password2'])
                            {
                                if (strlen($_POST['user_password1'])>=PASSWORD_MINLENGHT)
                                {
                                    if (dbquery("UPDATE ".$db_table['users']." SET user_password='".md5($_POST['user_password1'])."' WHERE user_id='".$s['user']['id']."';"))
                                    {
                                    	echo "Das Passwort wurde ge&auml;ndert!<br/><br/>";
                                    	add_log(3,"Der Spieler [b]".$s['user']['nick']."[/b] &auml;ndert sein Passwort!",time());
                                    }
                                }
                                else
                                	echo "Das Passwort muss mindestens ".PASSWORD_MINLENGHT." Zeichen lang sein!<br/><br/>";
                            }
                            else
                            	echo "Die Eingaben m&uuml;ssen identisch sein!<br/><br/>";
                        }
                        else
                        	echo "Das Passwort darf nicht identisch mit dem Sitterpasswort sein!<br/><br/>";
                    }
                    else
                    	echo "Dein altes Passwort stimmt nicht mit dem gespeicherten Passwort &uuml;berein!<br/><br/>";
                }

            	// Formular anzeigen
            	$cstr = checker_init();
              echo "<form action=\"?page=$page&mode=password\" method=\"post\">";
              echo $cstr;
              infobox_start("Passwort &auml;ndern",1);
              echo "<tr><th class=\"tbldata\" width=\"35%\">Altes Passwort:</th><td class=\"tbldata\" width=\"65%\"><input type=\"password\" name=\"user_password\" maxlength=\"255\" size=\"20\"></td></tr>";
              echo "<tr><th class=\"tbldata\" width=\"35%\">Neues Passwort (mind. ".PASSWORD_MINLENGHT." Zeichen):</th><td class=\"tbldata\" width=\"65%\"><input type=\"password\" name=\"user_password1\" maxlength=\"255\" size=\"20\"></td></tr>";
              echo "<tr><th class=\"tbldata\" width=\"35%\">Neues Passwort wiederholen:</th><td class=\"tbldata\" width=\"65%\"><input type=\"password\" name=\"user_password2\" maxlength=\"255\" size=\"20\"></td></tr>";
              infobox_end(1);
              echo "Beachte dass Passw&ouml;rter eine L&auml;nge von mindestens ".PASSWORD_MINLENGHT." Zeichen haben m&uuml;ssen!<br/><br/>";

              echo "<input type=\"submit\" name=\"password_submit\" value=\"Passwort &auml;ndern\"></form><br/><br/>";
        	}
          else
          {
          	echo "Im Sittermodus ist dieser Bereich gesperrt!";
          }
        }

/****************/
/* Sonstiges		*/
/****************/

		elseif($_GET['mode']=='misc')
		{
    	infobox_start("Sonstige Accountoptionen",1);
    	// Urlaubsmodus
    	echo "<tr><th class=\"tbltitle\">Urlaubsmodus</th><td class=\"tbldata\">";
    	if ($arr['user_hmode_from']>0 && $arr['user_hmode_from']<time() && $arr['user_hmode_to']<time())
    		echo "<input type=\"button\" value=\"Urlaubsmodus deaktivieren\" onclick=\"document.location='?page=$page&mode=misc&hmod=2'\">";
    	elseif ($arr['user_hmode_from']>0 && $arr['user_hmode_from']<time() && $arr['user_hmode_to']>time())
    	  echo "Urlaubsmodus ist aktiv bis mindestens ".date("d.m.Y H:i",$arr['user_hmode_to'])."!";
    	else
    	  echo "<input type=\"button\" value=\"Urlaubsmodus aktivieren (Dauer: mind. ".MIN_UMOD_TIME." Tage)\" onclick=\"if (confirm('Soll der Urlaubsmodus wirklich aktiviert werden?')) document.location='?page=$page&hmod=1'\">";
    	echo "</td></tr>";

			// Account löschen
    	echo "<tr><th class=\"tbltitle\">Account l&ouml;schen</th><td class=\"tbldata\">";
    	echo "<input type=\"submit\" value=\"Account l&ouml;schen\" onclick=\"document.location='?page=$page&mode=misc&remove=1'\">";
    	echo "</td></tr>";
    	infobox_end(1);
		}
		
/****************/
/* Logins		*/
/****************/
		
		elseif ($_GET['mode']=="logins")
		{
			echo "Hier findest du eine Liste der letzten 10 Logins in deinen Account, ebenfalls kannst du weiter unten
			sehen wann dass fehlerhafte Loginversuche stattgefunden haben. Solltest du feststellen, dass jemand unbefugten 
			Zugriff auf deinen Account hatte, solltest du umgehend dein Passwort &auml;ndern und einen Game-Admin informieren.<br/><br/>";
    	infobox_start("Letzte 10 Logins",1);
			$res=dbquery("
			SELECT 
				log_logintime,
				log_ip,
				log_hostname 
			FROM 
				".$db_table['user_log']." 
			WHERE
				log_user_id=".$s['user']['id']."
			ORDER BY 
				log_logintime DESC
			LIMIT 
				10;");
			echo "<tr><th class=\"tbltitle\">Zeit</th>
			<th class=\"tbltitle\">IP-Adresse</th>
			<th class=\"tbltitle\">Hostname</th></tr>";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><td class=\"tbldata\">".df($arr['log_logintime'])."</td>";
				echo "<td class=\"tbldata\">".$arr['log_ip']."</td>";
				echo "<td class=\"tbldata\">".$arr['log_hostname']."</td></tr>";
			}
    	infobox_end(1);
    	infobox_start("Letzte 10 fehlgeschlagene Logins",1);
			$res=dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['login_failures']." 
			WHERE
				failure_user_id=".$s['user']['id']."
			ORDER BY 
				failure_time DESC
			LIMIT 
				10;");
			if (mysql_num_rows($res)>0)
			{
				echo "<tr><th class=\"tbltitle\">Zeit</th>
				<th class=\"tbltitle\">IP-Adresse</th>
				<th class=\"tbltitle\">Hostname</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\">".df($arr['failure_time'])."</td>";
					echo "<td class=\"tbldata\">".$arr['failure_ip']."</td>";
					echo "<td class=\"tbldata\">".$arr['failure_host']."</td></tr>";
				}
			}
			else
			{
				echo "<tr><td class=\"tbldata\">Keine fehlgeschlagenen Logins</td></tr>";
			}
    	infobox_end(1);
    	
		}

		
	}

?>