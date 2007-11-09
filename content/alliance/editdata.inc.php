<?PHP
	echo "<h2>Allianzdaten &auml;ndern</h2>";
	
	echo "<form action=\"?page=$page\" method=\"post\" enctype=\"multipart/form-data\">";
	checker_init();
	
	// Stellt fest, welche Option eingestellt ist
	if ($arr['alliance_accept_applications']==1)
	{
		$accept_applications_yes = "checked=\"checked\"";
		$accept_applications_no = "";
	}
	else
	{
		$accept_applications_yes = "";
		$accept_applications_no = "checked=\"checked\"";
	}
	
	if ($arr['alliance_accept_bnd']==1)
	{
		$accept_bnd_yes = "checked=\"checked\"";
		$accept_bnd_no = "";
	}
	else
	{
		$accept_bnd_yes = "";
		$accept_bnd_no = "checked=\"checked\"";
	}
	
	infobox_start("Daten der Info-Seite",1);
	echo "<tr>
					<td class=\"tbltitle\">Allianz-Tag:</td>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"alliance_tag\" size=\"6\" maxlength=\"6\" value=\"".stripslashes($arr['alliance_tag'])."\" />
					</td>
				</tr>
				<tr>
					<td class=\"tbltitle\">Allianz-Name:</td>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"alliance_name\" size=\"25\" maxlength=\"25\" value=\"".stripslashes($arr['alliance_name'])."\" />
					</td>
				</tr>
				<tr>
					<td class=\"tbltitle\">Beschreibung:</td>
					<td class=\"tbldata\">
						<textarea rows=\"20\" cols=\"70\" name=\"alliance_text\">".stripslashes($arr['alliance_text'])."</textarea>
					</td>
				</tr>
				<tr>
					<td class=\"tbltitle\">Website/Forum:</td>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"alliance_url\" size=\"40\" maxlength=\"255\" value=\"".stripslashes($arr['alliance_url'])."\" /> Inkl. http://www....
					</td>
				</tr>
				<tr>
  				<th class=\"tbltitle\">Allianz-Bild:</th>
  				<td class=\"tbldata\">";
			    if ($arr['alliance_img']!="")
			    {
			      echo '<img src="'.ALLIANCE_IMG_DIR.'/'.$arr['alliance_img'].'" alt="Profil" /><br/>';
			      echo "<input type=\"checkbox\" value=\"1\" name=\"alliance_img_del\"> Bild l&ouml;schen<br/>";
			    }
  				echo "Allianzbild heraufladen/&auml;ndern: <input type=\"file\" name=\"alliance_img_file\" /><br/><b>Regeln:</b> Max ".ALLIANCE_IMG_MAX_WIDTH."*".ALLIANCE_IMG_MAX_HEIGHT." Pixel, Bilder grösser als ".ALLIANCE_IMG_WIDTH."*".ALLIANCE_IMG_HEIGHT." werden automatisch verkleinert.<br/>Format: GIF, JPG oder PNG. Grösse: Max ".nf(ALLIANCE_IMG_MAX_SIZE)." Byte</td>
  			</tr>
  			<tr>
					<td class=\"tbltitle\">Bewerbungen zulassen:</td>
					<td class=\"tbldata\">
						<input type=\"radio\" name=\"alliance_accept_applications\" value=\"1\" ".$accept_applications_yes."/> <span ".tm("Bewerbungen zulassen","Jeder User kann sich bei dieser Allianz bewerben.").">Ja</span> <input type=\"radio\" name=\"alliance_accept_applications\" value=\"0\" ".$accept_applications_no."/> <span ".tm("Bewerbungen zulassen","Es können keine Bewerbungen an diese Allianz geschrieben werden.").">Nein</span>
					</td>
				</tr>
				<tr>
					<td class=\"tbltitle\">Bündnisanfragen zulassen:</td>
					<td class=\"tbldata\">
						<input type=\"radio\" name=\"alliance_accept_bnd\" value=\"1\" ".$accept_bnd_yes."/> <span ".tm("Bündnisanfragen zulassen","Bündnisanfragen von jeder Allianz sind zugelassen.").">Ja</span> <input type=\"radio\" name=\"alliance_accept_bnd\" value=\"0\" ".$accept_bnd_no."/> <span ".tm("Bewerbungen zulassen","Es werden keine Bündnisanfragen angenommen.").">Nein</span>
					</td>
				</tr>"; 
	infobox_end(1);
	
	echo "<input type=\"submit\" name=\"editsubmit\" value=\"Speichern\" /> &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";
?>