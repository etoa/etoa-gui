<?PHP
	$id = $_GET['id'];
	$eres = dbquery("
			SELECT 
				e.id,
				code,
				pos,
				sx,
				sy,
				cx,
				cy 
			FROM 
				entities e
			INNER JOIN
				cells c
			ON
				e.cell_id=c.id
				AND e.id=".$id."
			LIMIT 1;;");
			if (mysql_num_rows($eres)>0)
			{
				$earr = mysql_fetch_array($eres);
				
				echo "<h2>Raumobjekt ".$earr['sx']."/".$earr['sy']." : ".$earr['cx']."/".$earr['cy']." : ".$earr['pos']." bearbeiten</h2>";
				if ($id>1)
					echo button("&lt;&lt; Vorheriges Objekt","?page=$page&amp;sub=$sub&id=".($id-1)."");
				echo " Objekt ".$earr['id']." ";
				echo button("Nächstes Objekt &gt;&gt;","?page=$page&amp;sub=$sub&id=".($id+1)."");
				echo "<br/><br/>";
				
				if ($earr['code']=='p')
				{		
					if (isset($_POST['save']))
					{
						if (isset($_POST['planet_user_main']))
						{
							$pl = new Planet($id);							
							if ($pl->setToMain())
								success_msg("Hauptplanet gesetzt!");
						}			              
						
						$addsql = "";
						if (isset($_POST['rst_user_changed']))
						{
							$addsql.= ",planet_user_changed=0";
						}
						
						//Daten Speichern
						dbquery("
						UPDATE
							planets
						SET
              planet_name='".$_POST['planet_name']."',
              planet_type_id=".$_POST['planet_type_id'].",
              planet_fields=".$_POST['planet_fields'].",
              planet_fields_extra=".$_POST['planet_fields_extra'].",
              planet_image='".$_POST['planet_image']."',
              planet_temp_from=".$_POST['planet_temp_from'].",
              planet_temp_to=".$_POST['planet_temp_to'].",
              planet_res_metal='".$_POST['planet_res_metal']."',
              planet_res_crystal='".$_POST['planet_res_crystal']."',
              planet_res_plastic='".$_POST['planet_res_plastic']."',
              planet_res_fuel='".$_POST['planet_res_fuel']."',
              planet_res_food='".$_POST['planet_res_food']."',
              planet_res_metal=planet_res_metal+'".$_POST['planet_res_metal_add']."',
              planet_res_crystal=planet_res_crystal+'".$_POST['planet_res_crystal_add']."',
              planet_res_plastic=planet_res_plastic+'".$_POST['planet_res_plastic_add']."',
              planet_res_fuel=planet_res_fuel+'".$_POST['planet_res_fuel_add']."',
              planet_res_food=planet_res_food+'".$_POST['planet_res_food_add']."',
              planet_wf_metal='".$_POST['planet_wf_metal']."',
              planet_wf_crystal='".$_POST['planet_wf_crystal']."',
              planet_wf_plastic='".$_POST['planet_wf_plastic']."',
              planet_people='".$_POST['planet_people']."',
              planet_people=planet_people+'".$_POST['planet_people_add']."',
              planet_desc='".addslashes($_POST['planet_desc'])."'
						WHERE
							id='".$id."';");
						if (mysql_affected_rows()>0)
						{
							success_msg("Änderungen übernommen");
						}
					}
					
					if(count($_POST)>0 && !isset($_POST['save']))
					{
						//Wenn der Besitzer wechseln soll
						if($_POST['planet_user_id']!=$_POST['planet_user_id_old'])
						{
							//Planet dem neuen User übergeben (Schiffe und Verteidigung werden vom Planeten gelöscht!)
							$pl = new Planet($id);
							$pl->chown($_POST['planet_user_id']);
		
							//Log Schreiben
							add_log(8,$_SESSION[SESSION_NAME]['user_nick']." wechselt den Besitzer vom Planeten: [URL=?page=galaxy&sub=edit&id=".$id."][B]".$id."[/B][/URL]\nAlter Besitzer: [URL=?page=user&sub=edit&user_id=".$_POST['planet_user_id_old']."][B]".$_POST['planet_user_id_old']."[/B][/URL]\nNeuer Besitzer: [URL=?page=user&sub=edit&user_id=".$_POST['planet_user_id']."][B]".$_POST['planet_user_id']."[/B][/URL]",time());
		
							success_msg("Der Planet wurde dem User mit der ID: [b]".$_POST['planet_user_id']."[/b] &uuml;bergeben!");
						}
						else
						{
							error_msg("Es wurde kein neuer Besitzer gew&auml;hlt!");
						}
					}
					
					$res = dbquery("
					SELECT 
						* 
					FROM 
						planets
					WHERE 
						id=".$id."
					LIMIT 1;");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
					tableStart("<span style=\"color:".Entity::$entityColors[$earr['code']]."\">Planet</span>","auto");
					
				
					echo "<tr><th>Name</t>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"".$arr['planet_name']."\" size=\"20\" maxlength=\"250\" /></td>";
					echo "<th>Typ</th>
					<td class=\"tbldata\">
					<select name=\"planet_type_id\">";
					$tres = dbquery("SELECT * FROM planet_types ORDER BY type_name;");
					while ($tarr = mysql_fetch_array($tres))
					{
						echo "<option value=\"".$tarr['type_id']."\"";
						if ($arr['planet_type_id']==$tarr['type_id']) 
						{
							echo " selected=\"selected\"";
							$planetTypeName = $tarr['type_name'];
						}
						echo ">".$tarr['type_name']."</option>\n";
					}
					echo "</select></td></tr>";

					echo "<tr><td class=\"tbldata\" style=\"height:2px;\" colspan=\"4\"></td></tr>";
					
					//Listet alle User der Spiels auf
					$users = get_user_names();
					echo "<tr><th>Besitzer</th><td class=\"tbldata\" colspan=\"3\"><select name=\"planet_user_id\">";
					echo "<option value=\"0\">(niemand)</option>";
					foreach ($users as $uid=>$udata)
					{
						echo "<option value=\"$uid\"";
						if ($arr['planet_user_id']==$uid) 
							{echo " selected=\"selected\"";$planet_user_id=$uid;}
						echo ">".$udata['nick']."</option>";
					}
					echo "</select> ";
					if ($arr['planet_user_id']>0 && $users[$planet_user_id]['alliance_id']>0)
					{
						$ally = new Alliance($users[$planet_user_id]['alliance_id']);
						echo $ally." &nbsp; ";
						unset($ally);
					}
					echo "<input type=\"hidden\" name=\"planet_user_id_old\" value=\"".$arr['planet_user_id']."\">";
					echo "<input tabindex=\"29\" type=\"button\" name=\"change_owner\" value=\"Planet &uuml;bergeben\" class=\"button\" onclick=\"if( confirm('Dieser Planet soll einem neuen Besitzer geh&ouml;ren. Alle Schiffs- und Verteidigungsdaten vom alten Besitzer werden komplett gel&ouml;scht.')) document.getElementById('editform').submit()\"/>&nbsp;";
					echo "</td></tr>";

					echo "<tr>
					<th>Hauptplanet</th>
					<td class=\"tbldata\">";
					if ($arr['planet_user_id']>0)
					{
						echo "<input type=\"checkbox\" name=\"planet_user_main\" ".($arr['planet_user_main']==1 ? " checked=\"checked\"" : "")." value=\"1\"/> Ist Hauptplanet";
					}
					else
						echo "-";
					echo "</td>
					<th>Letzer Besitzerwechsel</th>
					<td class=\"tbldata\">
					".($arr['planet_user_changed']>0 ? df($arr['planet_user_changed'])." <input type=\"checkbox\" name=\"rst_user_changed\" value=\"1\" /> Reset" : '-')."
					</td>
					</tr>";
					
					echo "<tr><td class=\"tbldata\" style=\"height:2px;\" colspan=\"4\"></td></tr>";
					
					echo "<tr><th>Felder / Extra-Felder</th>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_fields\" value=\"".$arr['planet_fields']."\" size=\"10\" maxlength=\"250\" />
					<input type=\"text\" name=\"planet_fields_extra\" value=\"".$arr['planet_fields_extra']."\" size=\"10\" maxlength=\"250\" /></td>";
					echo "<th>Felder benutzt</th>
					<td class=\"tbldata\">".nf($arr['planet_fields_used'])."</td></tr>";
					
					echo "<tr><th>Temperatur</th>
					<td class=\"tbldata\">
						<input type=\"text\" name=\"planet_temp_from\" value=\"".$arr['planet_temp_from']."\" size=\"4\" maxlength=\"5\" />
						bis <input type=\"text\" name=\"planet_temp_to\" value=\"".$arr['planet_temp_to']."\" size=\"4\" maxlength=\"5\" /> &deg;C
					</td>";
					$imPath = IMAGE_PATH."/planets/planet";
					$imPathPost = "_small.".IMAGE_EXT;
					echo "<th>Bild</th>
					<td class=\"tbldata\">
					<img id=\"pimg\" src=\"".$imPath.$arr['planet_image'].$imPathPost."\" style=\"float:left;\" />
					<select name=\"planet_image\" onchange=\"document.getElementById('pimg').src='$imPath'+this.value+'$imPathPost'\">";
					echo "<option value=\"\">Undefiniert</options";

					for ($x=1;$x<=$cfg->value('num_planet_images');$x++)
					{
						echo "<option value=\"".$arr['planet_type_id']."_".$x."\"";
						if ($arr['planet_image']==$arr['planet_type_id']."_".$x) 
							echo " selected=\"selected\"";
						echo ">".$planetTypeName." $x</option>\n";
					}
					echo "</select>
					
					</td>";
					
					echo "</tr>";
					
					echo "<td class=\"tbldata\" style=\"height:2px;\" colspan=\"4\"></td></tr>";
					
					echo "<tr><th class=\"resmetalcolor\">Titan</th>
					<td><input type=\"text\" name=\"planet_res_metal\" value=\"".intval($arr['planet_res_metal'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<th class=\"rescrystalcolor\">Silizium</th>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_res_crystal\" value=\"".intval($arr['planet_res_crystal'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "<tr><th class=\"resplasticcolor\">PVC</th>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_res_plastic\" value=\"".intval($arr['planet_res_plastic'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<th class=\"resfuelcolor\">Tritium</th>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_res_fuel\" value=\"".intval($arr['planet_res_fuel'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "<tr><th class=\"resfoodcolor\">Nahrung</th>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_res_food\" value=\"".intval($arr['planet_res_food'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<th class=\"respeoplecolor\">Bevölkerung</th>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_people\" value=\"".intval($arr['planet_people'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_people_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

					echo "<td class=\"tbldata\" style=\"height:2px;\" colspan=\"4\"></td></tr>";

					echo "<tr><th>Produktion ".RES_METAL."</th>
					<td class=\"tbldata\">".nf($arr['planet_prod_metal'])."</td>";
					echo "<th>Speicher ".RES_METAL.":</th>
					<td class=\"tbldata\">".nf($arr['planet_store_metal'])."</td></tr>";
					
					echo "<tr><th>Produktion ".RES_CRYSTAL."</th>
					<td class=\"tbldata\">".nf($arr['planet_prod_crystal'])."</td>";
					echo "<th>Speicher ".RES_CRYSTAL.":</th>
					<td class=\"tbldata\">".nf($arr['planet_store_crystal'])."</td></tr>";
					
					echo "<tr><th>Produktion ".RES_PLASTIC."</th>
					<td class=\"tbldata\">".nf($arr['planet_prod_plastic'])."</td>";
					echo "<th>Speicher ".RES_PLASTIC.":</th>
					<td class=\"tbldata\">".nf($arr['planet_store_plastic'])."</td></tr>";
					
					echo "<tr><th>Produktion ".RES_FUEL."</th>
					<td class=\"tbldata\">".nf($arr['planet_prod_fuel'])."</td>";
					echo "<th>Speicher ".RES_FUEL.":</th>
					<td class=\"tbldata\">".nf($arr['planet_store_fuel'])."</td></tr>";
					
					echo "<tr><th>Produktion ".RES_FOOD."</th>
					<td class=\"tbldata\">".nf($arr['planet_prod_food'])."</td>";
					echo "<th>Speicher ".RES_FOOD.":</th>
					<td class=\"tbldata\">".nf($arr['planet_store_food'])."</td></tr>";
		
					echo "<tr><th>Verbrauch Energie:</th>
					<td class=\"tbldata\">".nf($arr['planet_use_power'])."</td>";
					echo "<th>Produktion Energie:</th>
					<td class=\"tbldata\">".nf($arr['planet_prod_power'])."</td></tr>";
					
					echo "<tr><th>Wohnraum</th>
					<td class=\"tbldata\">".nf($arr['planet_people_place'])."</td>";
					echo "<th>Bevölkerungswachstum</th>
					<td class=\"tbldata\">".nf($arr['planet_prod_people'])."</td></tr>";
		
					echo "<td class=\"tbldata\" style=\"height:2px;\" colspan=\"4\"></td></tr>";
					
					echo "<tr><th>Tr&uuml;mmerfeld Titan</th>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_wf_metal\" value=\"".$arr['planet_wf_metal']."\" size=\"20\" maxlength=\"250\" /></td>";
					echo "<th>Tr&uuml;mmerfeld Silizium</th>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_wf_crystal\" value=\"".$arr['planet_wf_crystal']."\" size=\"20\" maxlength=\"250\" /></td></tr>";
					
					echo "<tr><th>Tr&uuml;mmerfeld PVC</th>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_wf_plastic\" value=\"".$arr['planet_wf_plastic']."\" size=\"20\" maxlength=\"250\" /></td>";
					echo "<th>Updated</th>
					<td class=\"tbldata\">".date("d.m.Y H:i",$arr['planet_last_updated'])."</th></tr>";
		
					
					echo "<tr><th>Beschreibung</td>
					<td class=\"tbldata\" colspan=\"3\"><textarea name=\"planet_desc\" rows=\"2\" cols=\"50\" >".stripslashes($arr['planet_desc'])."</textarea></td></tr>";
					echo "</table>";
					echo "<br/>
								<input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
					echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
					echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
					echo "</form>";
					echo "<hr/>";
					echo "<input type=\"button\" value=\"Gebäude\" onclick=\"document.location='?page=buildings&action=search&query=".searchQuery(array("entity_id"=>$arr['id']))."'\" /> &nbsp;";
				}
				elseif ($earr['code']=='s')
				{		
					echo ", Stern) bearbeiten</h2>";
								
					if ($_POST['save']!="")
					{
						//Daten Speichern
						dbquery("
						UPDATE
							stars
						SET
              name='".$_POST['name']."',
              type_id=".$_POST['type_id']."
						WHERE
							id='".$id."';");
						if (mysql_affected_rows()>0)
						{
							success_msg("Änderungen übernommen");
						}
					}
					
					$res = dbquery("
					SELECT 
						* 
					FROM 
						stars
					WHERE 
						id=".$id.";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
					echo "<table class=\"tbl\">";
					
				
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Name</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"name\" value=\"".$arr['name']."\" size=\"20\" maxlength=\"250\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Typ</td>
					<td class=\"tbldata\">
					<img src=\"".IMAGE_PATH."/stars/star".$arr['type_id']."_small.".IMAGE_EXT."\" style=\"float:left;\" />
					<select name=\"type_id\">";
					$tres = dbquery("SELECT * FROM sol_types ORDER BY type_name;");
					while ($tarr = mysql_fetch_array($tres))
					{
						echo "<option value=\"".$tarr['type_id']."\"";
						if ($arr['type_id']==$tarr['type_id']) echo " selected=\"selected\"";
						echo ">".$tarr['type_name']."</option>\n";
					}
					echo "</select></td></tr>";
					echo "</table>";
					echo "<br/>
								<input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
					echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
					echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
					echo "</form>";			
				}
				elseif ($earr['code']=='a')
				{		
					echo ", Asteroidenfeld) bearbeiten</h2>";
								
					if ($_POST['save']!="")
					{
						//Daten Speichern
						dbquery("
						UPDATE
							asteroids
						SET
              res_metal='".$_POST['res_metal']."',
              res_crystal='".$_POST['res_crystal']."',
              res_plastic='".$_POST['res_plastic']."',
              res_fuel='".$_POST['res_fuel']."',
              res_food='".$_POST['res_food']."',
              res_power='".$_POST['res_power']."',
              res_metal=res_metal+'".$_POST['res_metal_add']."',
              res_crystal=res_crystal+'".$_POST['res_crystal_add']."',
              res_plastic=res_plastic+'".$_POST['res_plastic_add']."',
              res_fuel=res_fuel+'".$_POST['res_fuel_add']."',
              res_food=res_food+'".$_POST['res_food_add']."',
              res_power=res_power+'".$_POST['res_power_add']."'
						WHERE
							id='".$id."';");
						if (mysql_affected_rows()>0)
						{
							success_msg("Änderungen übernommen");
						}
					}
							
					$res = dbquery("
					SELECT 
						* 
					FROM 
						asteroids
					WHERE 
						id=".$id.";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
					echo "<table class=\"tbl\">";
					
		
					echo "<tr><td class=\"tbltitle\" valign=\"top\">".RES_METAL."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_metal\" value=\"".intval($arr['res_metal'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">".RES_CRYSTAL."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_crystal\" value=\"".intval($arr['res_crystal'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">".RES_PLASTIC."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_plastic\" value=\"".intval($arr['res_plastic'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">".RES_FUEL."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_fuel\" value=\"".intval($arr['res_fuel'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">".RES_FOOD."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_food\" value=\"".intval($arr['res_food'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">".RES_POWER."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_power\" value=\"".intval($arr['res_power'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_power_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "</table>";
					echo "<br/>
								<input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
					echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
					echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
					echo "</form>";			
				}				
				elseif ($earr['code']=='n')
				{		
					echo ", Nebel) bearbeiten</h2>";
								
					if ($_POST['save']!="")
					{
						//Daten Speichern
						dbquery("
						UPDATE
							nebulas
						SET
              res_metal='".$_POST['res_metal']."',
              res_crystal='".$_POST['res_crystal']."',
              res_plastic='".$_POST['res_plastic']."',
              res_fuel='".$_POST['res_fuel']."',
              res_food='".$_POST['res_food']."',
              res_power='".$_POST['res_power']."',
              res_metal=res_metal+'".$_POST['res_metal_add']."',
              res_crystal=res_crystal+'".$_POST['res_crystal_add']."',
              res_plastic=res_plastic+'".$_POST['res_plastic_add']."',
              res_fuel=res_fuel+'".$_POST['res_fuel_add']."',
              res_food=res_food+'".$_POST['res_food_add']."',
              res_power=res_power+'".$_POST['res_power_add']."'
						WHERE
							id='".$id."';");
						if (mysql_affected_rows()>0)
						{
							success_msg("Änderungen übernommen");
						}
					}
							
					$res = dbquery("
					SELECT 
						* 
					FROM 
						nebulas
					WHERE 
						id=".$id.";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
					echo "<table class=\"tbl\">";
					
		
					echo "<tr><td class=\"tbltitle\" valign=\"top\">".RES_METAL."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_metal\" value=\"".intval($arr['res_metal'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">".RES_CRYSTAL."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_crystal\" value=\"".intval($arr['res_crystal'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">".RES_PLASTIC."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_plastic\" value=\"".intval($arr['res_plastic'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">".RES_FUEL."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_fuel\" value=\"".intval($arr['res_fuel'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">".RES_FOOD."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_food\" value=\"".intval($arr['res_food'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">".RES_POWER."</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"res_power\" value=\"".intval($arr['res_power'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"res_power_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "</table>";
					echo "<br/>
								<input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
					echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
					echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
					echo "</form>";
				}	
				elseif ($earr['code']=='w')
				{		
					echo ", Wurmloch) bearbeiten</h2>";
								
					$res = dbquery("
					SELECT 
						* 
					FROM 
						wormholes
					WHERE 
						id=".$id.";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
					echo "<table class=\"tbl\">";
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Entstanden</td>
					<td class=\"tbldata\">
						".df($arr['changed'])."
					</td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Ziel</td>
					<td class=\"tbldata\">";
					$ent = Entity::createFactoryById($arr['target_id']);
					echo $ent;
					echo "</td></tr>";
					echo "</table>";
					echo "<br/>
								<input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
					echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
					echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
					echo "</form>";	
				}
				elseif ($earr['code']=='e')
				{		
					echo ", Raum) bearbeiten</h2>";
								
					$res = dbquery("
					SELECT 
						* 
					FROM 
						space
					WHERE 
						id=".$id.";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$id."\" method=\"post\" id=\"editform\">";
					echo "<table class=\"tbl\">";
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Zuletzt besucht</td>
					<td class=\"tbldata\">";
					if ($arr['lastvisited']>0)
						df($arr['lastvisited']);
					else
						echo "Nie";
					echo "</td></tr>";
					echo "</table>";
					echo "<br/>
								<input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
					echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
					echo "<input tabindex=\"28\" type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
					echo "</form>";
				}									
				else
				{
					echo ", unbekannt) bearbeiten</h2>";
					echo "Für diesen Entitätstyp (".$earr['code'].") existiert noch kein Bearbeitungsformular!";
					echo "<br/><br/><input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
				}
				
			}
			else
			{
				echo "Entität nicht vorhanden!";
			}
?>