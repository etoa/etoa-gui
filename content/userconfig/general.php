<?PHP
    	// Datenänderung übernehmen
      if (isset($_POST['data_submit']) && $_POST['data_submit']!="" && checker_verify())
      {
        if (checkEmail($_POST['user_email']))
        {
              
          // Avatar
          if (isset($_POST['avatar_del']) && $_POST['avatar_del']==1)
          {
						$cu->avatar = "";
          }
          elseif ($_FILES['user_avatar_file']['tmp_name']!="")
          {
            $source=$_FILES['user_avatar_file']['tmp_name'];
            $ims = getimagesize($source);
            
            //überprüft Bildgrösse
            if ($ims[0]==BOARD_AVATAR_WIDTH && $ims[1]==BOARD_AVATAR_HEIGHT)
            {
                $fname = "user_".$cu->id."_".time().".gif";
                if (file_exists(BOARD_AVATAR_DIR."/".$arr['user_avatar']))
                    @unlink(BOARD_AVATAR_DIR."/".$arr['user_avatar']);
                move_uploaded_file($source,BOARD_AVATAR_DIR."/".$fname);
                if (UNIX)
                	chmod(BOARD_AVATAR_DIR."/".$fname,FILE_UPLOAD_PERMS);
                
								$cu->avatar = $fname;
                echo "Eigenen Avatar gespeichert!<br/>";
            }
            else
            {
                echo "Fehler! Das Avatarbild hat die falsche Gr&ouml;sse!<br/>";
            }
          }
          
          
          // Profil-Bild
          if (isset($_POST['profile_img_del']) && $_POST['profile_img_del']==1)
          {
          	$cu->profileImage = "";
          }
          elseif ($_FILES['user_profile_img_file']['tmp_name']!="")
          {
          	if ($_FILES['user_profile_img_file']['size']<=PROFILE_IMG_MAX_SIZE)
          	{          		
              $source=$_FILES['user_profile_img_file']['tmp_name'];
              $ims = getimagesize($source);
              
             	$ext = substr($ims['mime'],strrpos($ims['mime'],"/")+1);
             	if ($ext=="jpg" || $ext=="jpeg" || $ext=="gif" || $ext=="png")
             	{                  
                //überprüft Bildgrösse
                if ($ims[0]<=PROFILE_MAX_IMG_WIDTH && $ims[1]<=PROFILE_MAX_IMG_HEIGHT)
                {
                    $fname = "user_".$cu->id."_".time().".".$ext;
                    if (file_exists(PROFILE_IMG_DIR."/".$arr['user_profile_img']))
                        @unlink(PROFILE_IMG_DIR."/".$arr['user_profile_img']);
                    move_uploaded_file($source,PROFILE_IMG_DIR."/".$fname);
		                if (UNIX)
		                	chmod(PROFILE_IMG_DIR."/".$fname,FILE_UPLOAD_PERMS);
		                	
                    if ($ims[0]>PROFILE_IMG_WIDTH || $ims[1]>PROFILE_IMG_HEIGHT)
										{
											if (resizeImage(PROFILE_IMG_DIR."/".$fname,PROFILE_IMG_DIR."/".$fname,PROFILE_IMG_WIDTH,PROFILE_IMG_HEIGHT,$ext))
											{
												echo "Bildgrösse wurde angepasst! ";
                      	echo "Profilbild gespeichert!<br/>";
	                    	$cu->profileImage = $fname;
											}
											else
											{
												Echo "Bildgrösse konnte nicht angepasst werden!";
                        @unlink(PROFILE_IMG_DIR."/".$arr['user_profile_img']);
											}
										}
										else
										{
                    	echo "Profilbild gespeichert!<br/>";
                    	$cu->profileImage = $fname;
                    }
                }
                else
                {
                    echo "Fehler! Das Profilbild hat die falsche Gr&ouml;sse (".$ims[0]."*".$ims[1].")!<br/>";
                }
             	}
             	else
             	{
                echo "Fehler! Das Profilbild muss vom Typ jpeg, png oder gif sein.!<br/>";
							}
						}	                 	
           	else
           	{
              echo "Fehler! Das Profilbild ist zu gross (Max ".nf(PROFILE_IMG_MAX_SIZE)." Byte)!<br/>";
						}
          }
          
          
          $cu->email = $_POST['user_email'];
          $cu->profileText = addslashes($_POST['user_profile_text']);
          $cu->signature = addslashes($_POST['user_signature']);
          $cu->profileBoardUrl = $_POST['user_profile_board_url'];
          
          success_msg("Benutzer-Daten wurden ge&auml;ndert!");
          //$cu->addToUserLog("settings","{nick} hat sein Profil aktualisiert.",1);
            
        }
        else
              echo "<b>Fehler!</b> Die E-Mail-Adresse ist nicht korrekt!<br/><br/>";
      }

      echo "<form action=\"?page=$page&mode=general\" method=\"post\" enctype=\"multipart/form-data\">";
      $cstr = checker_init();
      tableStart("Benutzeroptionen");
      echo "<tr>
      	<th width=\"35%\">&Ouml;ffentliches Profil:</th>
      	<td width=\"65%\" style=\"color:#0f0;\">Klicke <a href=\"?page=userinfo&amp;id=".$cu->id."\">hier</a> um dein Profil anzuzeigen.</td>
      </tr>";

      echo "<tr>
      	<th width=\"35%\">Benutzername:</th>
      	<td width=\"65%\">".$cu->nick."</td>
      </tr>";
      echo "<tr>
      	<th width=\"35%\">Vollst&auml;ndiger Name:</th>
      	<td width=\"65%\">".$cu->realName." [".ticketLink("&Auml;nderung beantragen",10)."]</td>
      </tr>";
      echo "<tr>
      	<th width=\"35%\">Fixe E-Mail:</th>
      	<td width=\"65%\">".$cu->emailFix." [".ticketLink("&Auml;nderung beantragen",9)."]</td>
      </tr>";
      echo "<tr>
      	<th width=\"35%\">E-Mail:</th>
      	<td width=\"65%\"><input type=\"text\" name=\"user_email\" maxlength=\"255\" size=\"30\" value=\"".$cu->email."\"></td>
      </tr>";
      echo "<tr>
      	<th width=\"35%\">Beschreibung:</th>
      	<td><textarea name=\"user_profile_text\" cols=\"50\" rows=\"10\" width=\"65%\">".stripslashes($cu->profileText)."</textarea></td>
      </tr>";
      echo "<tr>
      	<th width=\"35%\">User-Bild:</th>
      	<td>";
        if ($cu->profileImage!="")
        {
          echo '<img src="'.PROFILE_IMG_DIR.'/'.$cu->profileImage.'" alt="Profil" /><br/>';
          echo "<input type=\"checkbox\" value=\"1\" name=\"profile_img_del\"> Bild l&ouml;schen<br/>";
        }
      	echo "Profilbild heraufladen/&auml;ndern: <input type=\"file\" name=\"user_profile_img_file\" /><br/>
      	<b>Regeln:</b> Max ".PROFILE_MAX_IMG_WIDTH."*".PROFILE_MAX_IMG_HEIGHT." Pixel, Bilder grösser als 
      	".PROFILE_IMG_WIDTH."*".PROFILE_IMG_HEIGHT." werden automatisch verkleinert.<br/>
      	Format: GIF, JPG oder PNG. Grösse: Max ".byte_format(PROFILE_IMG_MAX_SIZE)." </td>
      </tr>";   
      echo "<tr>
      	<th width=\"35%\">Allianzforum-Signatur:</th>
      	<td><textarea name=\"user_signature\" cols=\"50\" rows=\"2\" width=\"65%\">".stripslashes($cu->signature)."</textarea></td>
      </tr>";
      echo "<tr>
      	<th width=\"35%\">Allianzforum-Avatar:</th>
      	<td>";
        if ($cu->avatar!="" && $cu->avatar!=BOARD_DEFAULT_IMAGE)
        {
          show_avatar($cu->avatar);
          echo "<input type=\"checkbox\" value=\"1\" name=\"avatar_del\"> Avatar l&ouml;schen<br/>";
        }
      	echo "Eigener Avatar heraufladen/&auml;ndern (".BOARD_AVATAR_WIDTH."*".BOARD_AVATAR_HEIGHT." Pixel, GIF): <input type=\"file\" name=\"user_avatar_file\" /></td>
      </tr>";
      echo "<tr>
      	<th width=\"35%\">Öffentliches Foren-Profil:<br/>
      	<span style=\"font-weight:500;font-size:7pt;\">(zb http://www.etoa.ch/forum/profile.php?userid=1)</span></th>
      	<td width=\"65%\"><input type=\"text\" name=\"user_profile_board_url\" maxlength=\"200\" size=\"50\" value=\"".$cu->profileBoardUrl."\"></td>
      </tr>";

      tableEnd();

      echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
      echo "</form><br/><br/>";
?>