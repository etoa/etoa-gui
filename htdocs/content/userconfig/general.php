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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//

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
          	$imup = new ImageUpload('user_avatar_file',BOARD_AVATAR_DIR,"user_".$cu->id."_".time());
          	$imup->setMaxSize(BOARD_AVATAR_MAX_SIZE);
          	$imup->setMaxDim(BOARD_AVATAR_MAX_WIDTH,BOARD_AVATAR_MAX_HEIGHT);
          	$imup->enableResizing(BOARD_AVATAR_WIDTH,BOARD_AVATAR_HEIGHT);
          	
						if ($imup->process())
						{
							$cu->avatar = $imup->getResultName();
							success_msg("Avatar hochgeladen!");
						}           	
					}
          
          // Profil-Bild
          if (isset($_POST['profile_img_del']) && $_POST['profile_img_del']==1)
          {
          	$cu->profileImage = "";
          }
          elseif ($_FILES['user_profile_img_file']['tmp_name']!="")
          {
          	$imup = new ImageUpload('user_profile_img_file',PROFILE_IMG_DIR,"user_".$cu->id."_".time());
          	$imup->setMaxSize(PROFILE_IMG_MAX_SIZE);
          	$imup->setMaxDim(PROFILE_MAX_IMG_WIDTH,PROFILE_MAX_IMG_HEIGHT);
          	$imup->enableResizing(PROFILE_IMG_WIDTH,PROFILE_IMG_HEIGHT);
          	
						if ($imup->process())
						{
							$cu->profileImage = $imup->getResultName();
							success_msg("Profilbild hochgeladen!");
						}          	
          }

          $cu->email = $_POST['user_email'];
          $cu->profileText = addslashes($_POST['user_profile_text']);
          $cu->signature = addslashes($_POST['user_signature']);
          $cu->profileBoardUrl = $_POST['user_profile_board_url'];
          
          success_msg("Benutzer-Daten wurden ge&auml;ndert!");
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
        	$im = PROFILE_IMG_DIR.'/'.$cu->profileImage;
        	if (is_file($im))
        	{
	          echo '<img src="'.$im.'" alt="Profil" /><br/>';
	          echo "<input type=\"checkbox\" value=\"1\" name=\"profile_img_del\"> Bild l&ouml;schen<br/>";
        	}
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
        	if (is_file(BOARD_AVATAR_DIR."/".$cu->avatar))
        	{
	          show_avatar($cu->avatar);
  	        echo "<input type=\"checkbox\" value=\"1\" name=\"avatar_del\"> Avatar l&ouml;schen<br/>";
  	      }
        }
      	echo "Avatar heraufladen/&auml;ndern: <input type=\"file\" name=\"user_avatar_file\" /><br/>
      	<b>Regeln:</b> Max ".BOARD_AVATAR_MAX_WIDTH."*".BOARD_AVATAR_MAX_HEIGHT." Pixel, Bilder grösser als 
      	".BOARD_AVATAR_WIDTH."*".BOARD_AVATAR_HEIGHT." werden automatisch verkleinert.<br/>
      	Format: GIF, JPG oder PNG. Grösse: Max ".byte_format(BOARD_AVATAR_MAX_SIZE)." </td>
      </tr>";
      echo "<tr>
		<th width=\"35%\">Link zum öffentliches Foren-Profil:</th>
		<td width=\"65%\"><input type=\"text\" name=\"user_profile_board_url\" maxlength=\"200\" size=\"50\" value=\"".$cu->profileBoardUrl."\"></td>
      </tr>";

      tableEnd();

      echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
      echo "</form><br/><br/>";
?>