<?PHP
              // Änderungen speichern
              if ($_POST['password_submit']!="" && checker_verify())
              {

                  if (mysql_num_rows(dbquery("SELECT user_id FROM ".$db_table['users']." WHERE user_password='".pw_salt($_POST['user_password'],$arr['user_registered'])."' AND user_id=".$s['user']['id'].";"))>0)
                  {
                      if (mysql_num_rows(dbquery("SELECT user_sitting_sitter_password FROM ".$db_table['user_sitting']." WHERE user_sitting_sitter_password='".md5($_POST['user_password1'])."' AND user_sitting_user_id=".$s['user']['id'].";"))==0)
                      {
                          if ($_POST['user_password1']==$_POST['user_password2'])
                          {
                              if (strlen($_POST['user_password1'])>=PASSWORD_MINLENGHT)
                              {
                                  if (dbquery("
                                  	UPDATE 
                                  		".$db_table['users']." 
                                  	SET 
                                  		user_password='".pw_salt($_POST['user_password1'],$arr['user_registered'])."' 
                                  	WHERE 
                                  		user_id='".$s['user']['id']."'
                                  	;"))
                                  {
                                  	success_msg("Das Passwort wurde ge&auml;ndert!");
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

						if (isset($_POST['irc_submit']) && checker_verify())
						{
              $crypt = new crypt;                                                               
              $name = $crypt->encrypt(md5(PASSWORD_SALT.$arr['user_registered']), $_POST['irc_name']);								            	
							$pw = $crypt->encrypt(md5(PASSWORD_SALT.$arr['user_registered']), $_POST['irc_pw']);							
							dbquery("
							UPDATE 
								users 
							SET
								user_irc_name='".$name."',
								user_irc_pw='".$pw."'
							WHERE 
								user_id=".$s['user']['id']."
							;");											         
							$arr['user_irc_name']=$name;
							$arr['user_irc_pw']=$pw;
							success_msg("IRC-Daten gespeichert!");
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
            echo "<form action=\"?page=$page&mode=password\" method=\"post\">";
            echo $cstr;
            infobox_start("Gamesurge IRC Daten für den Chat",1);
            if ($arr['user_irc_name']!="")
            {            	
	            $crypt = new crypt;  
							$name = $crypt->decrypt(md5(PASSWORD_SALT.$arr['user_registered']), $arr['user_irc_name']);								            	            	
	            if ($arr['user_irc_name']!="")
	            {            	
								$pw = $crypt->decrypt(md5(PASSWORD_SALT.$arr['user_registered']), $arr['user_irc_pw']);								            	            	
	            }     							
	          }            
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Account:</th>
            	<td class=\"tbldata\" width=\"65%\">
            		<input type=\"text\" name=\"irc_name\" maxlength=\"255\" size=\"20\" value=\"".$name."\" />
            	</td>
            </tr>";
            echo "<tr>
            	<th class=\"tbldata\" width=\"35%\">Passwort:</th>
            	<td class=\"tbldata\" width=\"65%\">
            		<input type=\"password\" name=\"irc_pw\" maxlength=\"255\" size=\"20\" value=\"".$pw."\" />
            	</td>
            </tr>";
            infobox_end(1);
            echo "<input type=\"submit\" name=\"irc_submit\" value=\"Passwort &auml;ndern\"></form><br/><br/>";




?>