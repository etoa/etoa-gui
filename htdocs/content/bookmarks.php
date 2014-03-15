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
	
	/**
	* Target-Bookmarks-Manager
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2010 by EtoA Gaming, www.etoa.net
	*/	

	$mode = (isset($_GET['mode']) && $_GET['mode']!="") ? $_GET['mode'] : 'target';
	
	// save current planet for use in xajax functions
	$_SESSION['currentEntity']=serialize($cp);
	
	// Header & Menu
	echo "<h1>Favoriten</h1>";
 	show_tab_menu("mode",
		array("target"=>"Zielfavoriten",
			"fleet"=>"Flottenfavoriten",
			"new"=>"Neuer Flottenfavorit"));
 	echo '<br/>';
	
	// Save edited or new fleet bookmarks
	if (isset($_POST['submitEdit']) || isset($_POST['submitNew']))
	{
		// Check entity
		$res=dbquery("
			SELECT
				entities.id
			FROM
				entities
			INNER JOIN
				cells
			ON entities.cell_id=cells.id
				AND sx='".$_POST['sx']."'
        		AND sy='".$_POST['sy']."'
        		AND cx='".$_POST['cx']."'
        		AND cy='".$_POST['cy']."'
        		AND pos='".$_POST['pos']."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_row($res);

			//Check discovered for fleet bookmarks, bugfix by river
			$absX = (($_POST['sx']-1) * CELL_NUM_X) + $_POST['cx'];
			$absY = (($_POST['sy']-1) * CELL_NUM_Y) + $_POST['cy'];
			if ($cu->discovered($absX,$absY))
			{
				// Create shipstring
				$addships = "";
				foreach ($_POST['ship_count'] as $sid => $count)
				{
					if ($addships=="")
						$addships.= $sid.":".nf_back($count);
					else
						$addships.= ",".$sid.":".nf_back($count);
				}
				
				$speed = max(1,min(100,nf_back($_POST['value'])));
				
				// Create restring
				$freight = intval(nf_back_sign($_POST['res0'])).",".
					intval(nf_back_sign($_POST['res1'])).",".
					intval(nf_back_sign($_POST['res2'])).",".
					intval(nf_back_sign($_POST['res3'])).",".
					intval(nf_back_sign($_POST['res4'])).",".
					intval(nf_back_sign($_POST['res5']))."";
					
				/*$fetch = intval(nf_back_sign($_POST['fetch0'])).",".
					intval(nf_back_sign($_POST['fetch1'])).",".
					intval(nf_back_sign($_POST['fetch2'])).",".
					intval(nf_back_sign($_POST['fetch3'])).",".
					intval(nf_back_sign($_POST['fetch4'])).",".
					intval(nf_back_sign($_POST['fetch5']))."";*/

				/*$freight = max(0,intval(nf_back($_POST['res0']))).",".
					max(0,intval(nf_back($_POST['res1']))).",".
					max(0,intval(nf_back($_POST['res2']))).",".
					max(0,intval(nf_back($_POST['res3']))).",".
					max(0,intval(nf_back($_POST['res4']))).",".
					max(0,intval(nf_back($_POST['res5']))).""; */
					
				$fetch = max(0,intval(nf_back($_POST['fetch0']))).",".
					max(0,intval(nf_back($_POST['fetch1']))).",".
					max(0,intval(nf_back($_POST['fetch2']))).",".
					max(0,intval(nf_back($_POST['fetch3']))).",".
					max(0,intval(nf_back($_POST['fetch4']))).",".
					max(0,intval(nf_back($_POST['fetch5']))).""; 
					
				// Save new bookmark
				if (isset($_POST['submitNew']))
				{
					dbquery("
						INSERT INTO 
							fleet_bookmarks
						(
							user_id,
							name,
							target_id,
							ships,
							res,
							resfetch,
							action,
							speed
						) 
						VALUES 
						(
							'".$cu->id."',
							'".addslashes($_POST['name'])."',
							'".$arr[0]."',
							'".$addships."',
							'".$freight."',
							'".$fetch."',
							'".$_POST['action']."',
							'".$speed."'
						);");
								
					ok_msg("Der Favorit wurde hinzugef&uuml;gt!");
				}
				elseif (isset($_POST['submitEdit']))
				{
					// Update edidet bookmark
					dbquery("
						UPDATE
							fleet_bookmarks
						SET
							name='".addslashes($_POST['name'])."',
							target_id='".$arr[0]."',
							ships='".$addships."',
							res='".$freight."',
							resfetch='".$fetch."',
							action='".$_POST['action']."',
							speed='".$speed."'
						WHERE
							user_id='".$cu->id."'
							AND id='".$_POST['id']."'
						LIMIT 1;");
					
					ok_msg("Der Favorit wurde gespeichert!");
				}
			}
			else
			{
				err_msg('Ziel wurde noch nicht entdeckt.');
			}
		}
		else
		{
			err_msg("Es existiert kein Objekt an den angegebenen Koordinaten!");
		}
	}
	
	// Delete fleet bookmark
	if (isset($_GET['del']) && $_GET['del']>0)
	{
		dbquery("
		DELETE FROM 
			fleet_bookmarks
		WHERE 
			id='".$_GET['del']."' 
			AND user_id='".$cu->id."';");
		if (mysql_affected_rows()>0)
			ok_msg("Gelöscht");
	}
	
	if ($mode=="fleet")
	{
		// Load fleet bookmarks
		$res = dbquery("
			SELECT
	      		*
			FROM
				fleet_bookmarks
			WHERE
				user_id='".$cu->id."'
			ORDER BY
			 name;");
		if (mysql_num_rows($res)>0)
		{
			// Load Shipdata
			$sres = dbquery("
						SELECT
							ship_id,
							ship_name
						FROM
							ships");
			while ($sarr = mysql_fetch_row($sres))
			{
				$ships[$sarr[0]] = $sarr[1];
			}
			
			tableStart("Gespeicherte Favoriten");
			echo "<tr>
						<th>Name</th>
						<th colspan=\"2\">Ziel</th>
						<th>Aktion</th>
						<th>Schiffe</th>
						<th>Aktionen</th>
				</tr>";
			while ($arr=mysql_fetch_assoc($res))
			{
				$ent = Entity::createFactoryById($arr['target_id']);
				$ac = FleetAction::createFactory($arr['action']);
				
				$sidarr = explode(",",$arr['ships']);
				
				echo "<tr>
						<td>".text2html($arr['name'])."</td>
						<td style=\"width:40px;background:#000\"><img src=\"".$ent->imagePath()."\" /></td>
						<td>".$ent."<br/>(".$ent->entityCodeString().")</td>
						<td>".$ac."</td>
						<td>";
				
				// Creating ship-print-string
				foreach ($sidarr as $sd)
				{
					$sdi = explode(":",$sd);
					echo nf($sdi[1])." ".$ships[$sdi[0]]."<br />";
				}
				echo "</td>
						<td id=\"fleet_bm_actions_" . $arr['id'] . "\" class=\"tbldata\">
							<a href=\"javascript:;\" onclick=\"$('#fleet_bm_actions_" . $arr['id'] . "').html('Flotte wird gestartet...');xajax_launchBookmarkProbe(".$arr['id'].");\">Starten</a> 
							<a href=\"?page=$page&amp;mode=new&amp;edit=".$arr['id']."\">Bearbeiten</a> 
							<a href=\"?page=$page&amp;mode=$mode&amp;del=".$arr['id']."\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a>
						</td>
					</tr>";
			}
			tableEnd();
			
			// Create box for future events
			echo '<div id="fleet_info_box" style="display:none;">';
			iBoxStart("Flotten");
			echo '<div id="fleet_info"></div>';
			iBoxEnd();
			echo '</div>';
		}
		else
		{
			info_msg("Noch keine Favoriten vorhanden!");
		}			
			
	}
	elseif ($mode=="new")
	{
		// Creat array for data
		$data = array();
		$new = false;
		
		$_SESSION['bookmarks'] = array('added');
		$_SESSION['bookmarks']['added'] = array();
		
		if (isset($_GET['edit']) && $_GET['edit']>0)
		{
			// Load bookmark data
			$bres = dbquery("
						SELECT
							*
						FROM
							fleet_bookmarks
						WHERE
							id='".$_GET['edit']."' 
							AND user_id='".$cu->id."';");
			if (mysql_num_rows($bres)>0)
			{
				$barr=mysql_fetch_assoc($bres);
				$eres = dbquery("
							SELECT
								cells.sx,
								cells.cx,
								cells.sy,
								cells.cy,
								entities.pos
							FROM
								entities
							INNER JOIN
								cells
							ON
								entities.cell_id=cells.id
								AND entities.id='".$barr['target_id']."'
							LIMIT 1");
				if (mysql_num_rows($eres))
				{
					$earr=mysql_fetch_assoc($eres);
					
					$res = explode(",",$barr['res']);
					$fetch = explode(",",$barr['resfetch']);
					$ships = array();
					$ship = explode(",",$barr['ships']);
					foreach ($ship as $shipdata)
					{
						$s = explode(":", $shipdata);
						$ships[$s[0] ] = $s[1];
					}
					
					// Fill data array
					$data = array_merge($data,$earr);
					$data['res'] = $res;
					$data['fetch'] = $fetch;
					$data['ships'] = $ships;
					$data['speed'] = $barr['speed'];
					$data['name'] = $barr['name'];
					$data['id'] = $barr['id'];
					$data['action'] = $barr['action'];
				}
				else
				{
					error_msg("Ziel wurde nicht gefunden!");
				}
			}
			else
			{
				error_msg("Flottenfavorit konnte nicht gefunden werden!");
			}
		}
		
		// If data array is without data create a new one
		if (count($data) === 0)
		{
			$new = true;
			$data['id'] = 0;
			$data['sx'] = 1;
			$data['sy'] = 1;
			$data['cx'] = 1;
			$data['cy'] = 1;
			$data['pos'] = 0;
			$data['name'] = "";
			$data['res'] = array(0,0,0,0,0,0);
			$data['fetch'] = array(0,0,0,0,0,0);
			$data['ships'] = array();
			$data['speed'] = "100";
			$data['action'] = "flight";
		}
		
		echo '<form id="bookmarkForm" action="?page='.$page.'&amp;mode=fleet" method="post">';
		checker_init();
		echo '<input type="hidden" name="id" value="'.$data['id'].'" />';
		
		tableStart('Allgemeines');
		echo '<tr>
				<th>Name</th>
				<td><input type="text" name="name" id="name" value="'.$data['name'].'" autocomplete="off" size="30" maxlength="30" /></td>
			</tr>
			<tr>
				<th>Flottenaktion</th>
				<td>
					<select name="action">';
		foreach (FleetAction::getAll(true) as $ai)
		{
			echo '<option value="'.$ai->code().'" style="color:'.$ai->color().'"';
			if ($data['action']==$ai->code())
				echo ' selected="selected" ';
			echo '>'.$ai->name().'</option>';
		}
		echo '		</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">Wichtig: Die Flotte wird nur starten, falls die Schiffe und das Ziel die gewählte Aktion unterstützen. Es muss pro Schiffstyp mindestens ein Schiff vorhanden sein, damit die Flotte startet. Bei den Rohstoffen wird Rohstoff für Rohstoff jeweils das Maximum eingeladen.</td>
			</tr>';
		tableEnd();
		
		// Ship databox
		$cnt = 0;
		tableStart('Schiffe',0,"",'bookmarkShiplistInputTable');
    echo "<tbody></tbody>";
		tableEnd();

    echo "<script>$(function(){";
		foreach($data['ships'] as $id=>$count)
		{
			?>
				fleetBookmarkAddShipToList(<?PHP echo "'".$id."', '".$count."'"; ?>);
			<?PHP
		}
    echo "});</script>";
		
		// Ship addbox
		tableStart('Schiffe hinzufügen',0,'','shipadder');
		echo '<tr>
				<th colspan="2">Schiffname:</th>
				<td>
					<input type="text" name="shipname" id="shipname" value="" autocomplete="off" size="30" maxlength="30" onkeyup="xajax_searchShipList(this.value);">
					<br>
					<div id="shiplist">&nbsp;</div>
				</td>
				<td ';
		if ($cnt==0) echo 'style="display:none;"';
		echo 'id="saveShips">
					<input type="button" value="Keine weiteren Schiffe hinzufügen" onclick="toggleBox(\'shipadder\');toggleBox(\'targetBox\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\'));" />
			</tr>';
		tableEnd();
		
		// Show target selector
		tableStart('Zielwahl',0,'nondisplay','targetBox');
		
		// Manuel selector
		echo '<tr id="manuelselect">
				<th width="25%">Manuelle Eingabe:</th>
				<td colspan="2" width="75%">
					<input type="text" 
						id="sx"
						name="sx" 
						size="1" 
						maxlength="1" 
						value="'.$data['sx'].'" 
						title="Sektor X-Koordinate" 
						autocomplete="off" 
						onfocus="this.select()" 
						onclick="this.select()" 
						onkeydown="detectChangeRegister(this,\'t1\');"
						onkeyup="if (detectChangeTest(this,\'t1\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
						onkeypress="return nurZahlen(event)"
					/>&nbsp;/&nbsp;
					<input type="text" 
						id="sy" 
						name="sy" 
						size="1" 
						maxlength="1" 
						value="'.$data['sy'].'" 
						title="Sektor Y-Koordinate" 
						autocomplete="off" 
						onfocus="this.select()" 
						onclick="this.select()" 
						onkeydown="detectChangeRegister(this,\'t2\');"
						onkeyup="if (detectChangeTest(this,\'t2\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
						onkeypress="return nurZahlen(event)"
					/>&nbsp;&nbsp;:&nbsp;&nbsp;
					<input type="text" 
						id="cx" 
						name="cx" 
						size="2" 
						maxlength="2" 
						value="'.$data['cx'].'" 
						title="Zelle X-Koordinate" 
						autocomplete="off" 
						onfocus="this.select()" 
						onclick="this.select()" 
						onkeydown="detectChangeRegister(this,\'t3\');"
						onkeyup="if (detectChangeTest(this,\'t3\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
						onkeypress="return nurZahlen(event)"
				/>&nbsp;/&nbsp;
				<input type="text" 
						id="cy" 
						name="cy" 
						size="2"
						maxlength="2" 
						value="'.$data['cy'].'" 
						title="Zelle Y-Koordinate" 
						autocomplete="off" 
						onfocus="this.select()" 
						onclick="this.select()" 
						onkeydown="detectChangeRegister(this,\'t4\');"
						onkeyup="if (detectChangeTest(this,\'t4\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
						onkeypress="return nurZahlen(event)"
				/>&nbsp;&nbsp;:&nbsp;&nbsp;
				<input type="text" 
						id="pos" 
						name="pos" 
						size="2" 
						maxlength="2" 
						value="'.$data['pos'].'" 
						title="Position des Planeten im Sonnensystem" 
						autocomplete="off" 
						onfocus="this.select()" 
						onclick="this.select()" 
						onkeydown="detectChangeRegister(this,\'t5\');"
						onkeyup="if (detectChangeTest(this,\'t5\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
						onkeypress="return nurZahlen(event)"
				/></td></tr>';
				
		// Bookmark selector
		echo '<tr id="bookmarkselect">
				<th width="25%">Zielfavoriten:</th>
				<td colspan="2" width="75%" align="left">
					<select name="bookmarks" 
							id="bookmarks" 
							onchange="xajax_bookmarkBookmark(xajax.getFormValues(\'bookmarkForm\'));"
					>\n
						<option value="0">Wählen...</option>';
		
		$pRes=dbquery("
				SELECT
					planets.id
				FROM
					planets
				WHERE
					planets.planet_user_id=".$cu->id."
				ORDER BY
					planet_user_main DESC,
					planet_name ASC;");
		
		if (mysql_num_rows($pRes)>0)
		{	
			while ($pArr=mysql_fetch_assoc($pRes))
			{
				$ent = Entity::createFactory('p',$pArr['id']);
				echo '<option value="'.$ent->id().'">Eigener Planet: '.$ent.'</option>\n';
			}
		}
		
		$bRes=dbquery("
				SELECT
					bookmarks.entity_id,
					bookmarks.comment,
					entities.code      
				FROM
					bookmarks
				INNER JOIN
					entities	
				ON
					bookmarks.entity_id=entities.id
					AND bookmarks.user_id=".$cu->id.";");
		
		if (mysql_num_rows($bRes)>0)
		{
			echo '<option value="0">-------------------------------</option>\n';
			
			while ($bArr=mysql_fetch_assoc($bRes))
			{
				$ent = Entity::createFactory($bArr['code'],$bArr['entity_id']);
				echo '<option value="'.$ent->id().'">'.$ent->entityCodeString().' - '.$ent.' ('.$bArr['comment'].')</option>\n';
			}
		}	
		echo '		</select>
				</td>
			</tr>
			<tr>
				<th width="25%"><b>Ziel-Informationen:</b></th>
				<td colspan="2" id="targetinfo" style="padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;">
					<img src="images/loading.gif" alt="Loading" /> Lade Daten...
				</td>
			</tr>
			<tr>
				<th>Speedfaktor:</th>
				<td width="75%">
					<div id="slider" style="margin:10px;"></div>
				</td>
				<td style="background:#000;vertical-align:middle;text-align:center;">
					<input type="text" id="value" name="value" value="'.$data['speed'].' %" size="4" style="border:0"/>
				</td>
			</tr>';
		tableEnd();
		
		tableStart('Ladung',0,'nondisplay','resbox');
		echo '<tr>
				<th>&nbsp;</th>
				<th>Fracht</th>
				<th>Abholauftrag</th>
			</tr>
			<tr>
				<th>'.RES_ICON_METAL.''.RES_METAL.'</th>
				<td>
					<input type="text" name="res0" id="res0" value="'.$data['res'][0].'" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
				<td>
					<input type="text" name="fetch0" id="fetch0" value="'.$data['fetch'][0].'" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
			</tr>
			<tr>
				<th>'.RES_ICON_CRYSTAL.''.RES_CRYSTAL.'</th>
				<td>
					<input type="text" name="res1" id="res1" value="'.$data['res'][1].'" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
				<td>
					<input type="text" name="fetch1" id="fetch1" value="'.$data['fetch'][1].'" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
			</tr>
			<tr>
				<th>'.RES_ICON_PLASTIC.''.RES_PLASTIC.'</th>
				<td>
					<input type="text" name="res2" id="res2" value="'.$data['res'][2].'" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
				<td>
					<input type="text" name="fetch2" id="fetch2" value="'.$data['fetch'][2].'" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
			</tr>
			<tr>
				<th>'.RES_ICON_FUEL.''.RES_FUEL.'</th>
				<td>
					<input type="text" name="res3" id="res3" value="'.$data['res'][3].'" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
				<td>
					<input type="text" name="fetch3" id="fetch3" value="'.$data['fetch'][3].'" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
			</tr>
			<tr>
				<th>'.RES_ICON_FOOD.''.RES_FOOD.'</th>
				<td>
					<input type="text" name="res4" id="res4" value="'.$data['res'][4].'" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
				<td>
					<input type="text" name="fetch4" id="fetch4" value="'.$data['fetch'][4].'" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
			</tr>
			<tr>
				<th>'.RES_ICON_PEOPLE.'Passagiere</th>
				<td>
					<input type="text" name="res5" id="res5" value="'.$data['res'][5].'" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
				<td>
					<input type="text" name="fetch5" id="fetch5" value="'.$data['fetch'][5].'" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
				</td>
			</tr>';
		tableEnd();
		
		jsSlider("slider", $data['speed']);
		
		echo '<div id="submit" style="display:none;">';
		if ($new)
			echo '<input type="submit" value="Speichern" name="submitNew" id="submitNew" />';
		else
			echo '<input type="submit" value="Speichern" name="submitEdit" id="submitEdit" />';
		echo '</div>';
		echo "</form>";
		
	}
	else
	{
		/****************************
		*  Sortiereingaben speichern *
		****************************/
		if(count($_POST)>0 && isset($_POST['sort_submit']))
		{
			$cu->properties->itemOrderBookmark = $_POST['sort_value'];
    		$cu->properties->itemOrderWay = $_POST['sort_way'];
		}
		
		// Bearbeiten
		if (isset($_GET['edit']) && $_GET['edit']>0)
		{
			echo "<form action=\"?page=$page\" method=\"post\">";
			checker_init();
			$res=dbquery("
			SELECT
	      bookmarks.comment,
	      bookmarks.entity_id,
	      entities.code      
			FROM
	      bookmarks
			INNER JOIN
				entities	
				ON bookmarks.entity_id=entities.id
				AND bookmarks.id='".$_GET['edit']."'
				AND bookmarks.user_id=".$cu->id.";");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_assoc($res);
				$ent = Entity::createFactory($arr['code'],$arr['entity_id']);
				
				tableStart("Favorit bearbeiten");
				echo "<tr>
								<th>Koordinaten</th>
								<td>".$ent->entityCodeString()." - ".$ent."</td>
							</tr>
							<tr>
								<th>Kommentar</th>
								<td>
									<textarea name=\"bookmark_comment\" rows=\"3\" cols=\"60\">".stripslashes($arr['comment'])."</textarea>
								</td>
							</tr>";
				tableEnd();
				
				echo "<input type=\"hidden\" name=\"bookmark_id\" value=\"".$_GET['edit']."\" />";
				echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_edit_target\" /> &nbsp; ";
			}
			else
			{
				error_msg("Datensatz nicht gefunden!");
			}
			echo " <input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
			echo "</form>";
		}
		else
		{
			// Bearbeiteter Favorit speichern
			if (isset($_POST['submit_edit_target']) && $_POST['submit_edit_target'] && checker_verify())
			{
				dbquery("
				UPDATE 
					bookmarks
				SET 
					comment='".addslashes($_POST['bookmark_comment'])."' 
				WHERE 
					id='".$_POST['bookmark_id']."' 
					AND user_id='".$cu->id."';");
				if (mysql_affected_rows()>0)
					ok_msg("Gespeichert");
			}
	
			// Favorit löschen
			if (isset($_GET['del']) && $_GET['del']>0)
			{
				dbquery("
				DELETE FROM 
					bookmarks
				WHERE 
					id='".$_GET['del']."' 
					AND user_id='".$cu->id."';");
				if (mysql_affected_rows()>0)
					ok_msg("Gelöscht");
			}
	
			// Neuer Favorit speichern
			if (isset($_POST['submit_target']) && $_POST['submit_target']!="" && checker_verify())
			{
				$absX = (($_POST['sx']-1) * CELL_NUM_X) + $_POST['cx'];
				$absY = (($_POST['sy']-1) * CELL_NUM_Y) + $_POST['cy'];
				if ($cu->discovered($absX,$absY))
				{
					$res=dbquery("
						SELECT
							entities.id
						FROM
							entities
						INNER JOIN
							cells
						ON entities.cell_id=cells.id
							AND sx='".$_POST['sx']."'
	    				    AND sy='".$_POST['sy']."'
	        				AND cx='".$_POST['cx']."'
	        				AND cy='".$_POST['cy']."'
	        				AND pos='".$_POST['pos']."';");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_row($res);
						$check_res = dbquery("
							SELECT 
								id 
							FROM 
								bookmarks
							WHERE 
								entity_id='".$arr[0]."' 
								AND user_id='".$cu->id."';");
						if (mysql_num_rows($check_res)==0)
						{
							dbquery("
								INSERT INTO 
									bookmarks
								(
									user_id,
									entity_id,
									comment) 
								VALUES 
									('".$cu->id."',
									'".$arr[0]."',
									'".addslashes($_POST['bookmark_comment'])."');");
								
							ok_msg("Der Favorit wurde hinzugef&uuml;gt!");
						}
						else
						{
							error_msg("Dieser Favorit existiert schon!");
						}
					}
					else
					{
						error_msg("Es existiert kein Objekt an den angegebenen Koordinaten!");
					}
				}
				else
				{
					error_msg("Das Gebiet ist noch nicht erkundet!");
				}
			}
	
			// Neuer Favorit speichern (id gegeben
			if (isset($_GET['add']) && $_GET['add']>0)
			{
				$res=dbquery("
				SELECT
					entities.id
				FROM
					entities
				WHERE
					id=".$_GET['add'].";");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_row($res);
					$check_res = dbquery("
					SELECT 
						id 
					FROM 
						bookmarks
					WHERE 
						entity_id='".$arr[0]."' 
						AND user_id='".$cu->id."';");
					if (mysql_num_rows($check_res)==0)
					{
						dbquery("
						INSERT INTO 
							bookmarks
						(
							user_id,
							entity_id,
							comment) 
						VALUES 
							('".$cu->id."',
							'".$arr[0]."',
							'-');");
								
						ok_msg("Der Favorit wurde hinzugef&uuml;gt!");
					}
					else
					{
						error_msg("Dieser Favorit existiert schon!");
					}
				}
				else
				{
					error_msg("Es existiert kein Objekt an den angegebenen Koordinaten!!");
				}
			}
			
			// Add-Bookmakr-Box
			iBoxStart("Favorit hinzuf&uuml;gen");
			echo "<form action=\"?page=$page\" method=\"post\">";
			checker_init();
			echo "<select name=\"sx\">";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
			{
				echo "<option value=\"$x\">$x</option>";
			}
			echo "</select> / <select name=\"sy\">";
			for ($y=1;$y<=$conf['num_of_sectors']['p2'];$y++)
			{
				echo "<option value=\"$y\">$y</option>";
			}
			echo "</select> : <select name=\"cx\">";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
			{
				echo "<option value=\"$x\">$x</option>";
			}
			echo "</select> / <select name=\"cy\">";
			for ($y=1;$y<=$conf['num_of_cells']['p2'];$y++)
			{
				echo "<option value=\"$y\">$y</option>";
			}
			echo "</select> : <select name=\"pos\">";
			for ($y=0;$y<=$conf['num_planets']['p2'];$y++)
			{
				echo "<option value=\"$y\">$y</option>";
			}
			echo "</select> &nbsp; ";
			echo "<input type=\"text\" name=\"bookmark_comment\" size=\"20\" maxlen=\"200\" value=\"Kommentar\" onfocus=\"if (this.value=='Kommentar') this.value=''\" /> &nbsp;";
			echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_target\" />";
			
			iBoxEnd();
			
			$order = "";
			if ($cu->properties->itemOrderBookmark=="users.user_nick")
				$order=" LEFT JOIN
							planets
						ON
							bookmarks.entity_id=planets.id
						LEFT JOIN
							users
						ON
							planets.planet_user_id=users.user_id ";
			$order.=" ORDER BY ".$cu->properties->itemOrderBookmark." ".$cu->properties->itemOrderWay."";
	
			// List bookmarks
			$res = dbquery("
			SELECT
	      bookmarks.id,
	      bookmarks.comment,
	      bookmarks.entity_id,
	      entities.code
			FROM
				bookmarks
			INNER JOIN
				entities	
				ON bookmarks.user_id=".$cu->id."
				AND bookmarks.entity_id=entities.id
			".$order.";");
			if (mysql_num_rows($res)>0)
			{
				tableStart("Gespeicherte Favoriten");
/*************
	* Sortierbox *
	*************/
				//Legt Sortierwerte in einem Array fest
				$values = array(
								"bookmarks.id"=>"Erstelldatum",
								"bookmarks.entity_id"=>"Koordianten",
								"bookmarks.comment"=>"Kommentar",
								"entities.code"=>"Typ",
								"users.user_nick"=>"Besitzer"
								);
											
				echo "<tr>
						<td colspan=\"6\" style=\"text-align:center;\">
							<select name=\"sort_value\">";
				foreach ($values as $value => $name)
				{
					echo "<option value=\"".$value."\"";
					if($cu->properties->itemOrderBookmark==$value)
					{
						echo " selected=\"selected\"";
					}
					echo ">".$name."</option>";							
				}																																																							
				echo "</select>
				
					<select name=\"sort_way\">";
					
				//Aufsteigend
				echo "<option value=\"ASC\"";
				if($cu->properties->itemOrderWay=='ASC') echo " selected=\"selected\"";
					echo ">Aufsteigend</option>";
					
				//Absteigend
				echo "<option value=\"DESC\"";
				if($cu->properties->itemOrderWay=='DESC') echo " selected=\"selected\"";
					echo ">Absteigend</option>";	
				
				echo "</select>						
				
							<input type=\"submit\" class=\"button\" name=\"sort_submit\" value=\"Sortieren\"/>
						</td>
					</tr>";
				echo "<tr>
								<th colspan=\"2\">Typ</th>
								<th>Koordinaten</th>
								<th>Besitzer</th>
								<th>Kommentar</th>
								<th>Aktionen</th>
							</tr>";
				while ($arr=mysql_fetch_assoc($res))
				{
					$ent = Entity::createFactory($arr['code'],$arr['entity_id']);
				
					echo "<tr>
										<td style=\"width:40px;background:#000\"><img src=\"".$ent->imagePath()."\" /></td>
										<td>".$ent->entityCodeString()."</td>
										<td><a href=\"?page=cell&amp;id=".$ent->cellId()."&amp;hl=".$ent->id()."\">".$ent."</a></td>
										<td>".$ent->owner()."</td>
										<td>".text2html($arr['comment'])."</td>
										<td>";

					// Action icons added by river, Info link moved to coordinates (above)

					// Flotte
					if ($ent->entityCode()=='p' || $ent->entityCode()=='a' || $ent->entityCode()=='w' || $ent->entityCode()=='n' || $ent->entityCode()=='e')
					{
						echo "<a href=\"?page=haven&amp;target=".$ent->id()."\" title=\"Flotte hinschicken\">".icon('fleet')."</a> ";
					}

					if ($ent->entityCode()=='p')					
					{						
						// Nachrichten-Link
						if ($ent->ownerId()>0 && $cu->id!=$ent->ownerId())
						{
							echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$ent->ownerId()."\" title=\"Nachricht senden\">".icon("mail")."</a> ";
						}
							
						// Ausspionieren, Raketen, Krypto
						if ($cu->id!=$ent->ownerId())
						{
							// Besiedelter Planet
							if($ent->ownerId() > 0)
							{
								echo "<a href=\"javascript:;\" onclick=\"xajax_launchSypProbe(".$ent->id().");\" title=\"Ausspionieren\">".icon("spy")."</a>";
								echo "<a href=\"?page=missiles&amp;target=".$ent->id()."\" title=\"Raketenangriff starten\">".icon("missile")."</a> ";
								echo "<a href=\"?page=crypto&amp;target=".$ent->id()."\" title=\"Flottenbewegungen analysieren\">".icon("crypto")."</a> ";					
							}
						}
					}
					
					// Analysieren, letzten Analysebericht als Popup anzeigen
					if (in_array("analyze",$ent->allowedFleetActions()))
					{
						if ($cu->properties->showCellreports)
						{
							$reports = Report::find(array("type"=>"spy","user_id"=>$cu->id, "entity1_id"=>$ent->id()),"timestamp DESC",1,0,true);
							if (count($reports)) {
								$r = array_pop($reports);
								echo "<span ".tm($r->subject,$r."<br style=\"clear:both\" />")."><a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(".$ent->id().");\" title=\"Analysieren\">".icon("spy")."</a></span>";	
							}
							else
								echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(".$ent->id().");\" title=\"Analysieren\">".icon("spy")."</a> ";
						}
						else
							echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(".$ent->id().");\" title=\"Analysieren\">".icon("spy")."</a> ";
					}
									echo "
											<a href=\"?page=entity&amp;id=".$ent->id()."&amp;hl=".$ent->id()."\">".icon('info')."</a> 
											<a href=\"?page=$page&amp;edit=".$arr['id']."\">".icon('edit')."</a> 
											<a href=\"?page=$page&amp;del=".$arr['id']."\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">".icon('delete')."</a>
									</td>
							</tr>";
				}
				
				// Feedback-box für Ausspionieren und Analysieren von river
				echo '
						<tr><td colspan="6"><div id="spy_info_box" style="display:none"><span id="spy_info"></span></div></td></tr>';
				
				tableEnd();
				
			}
			else
			{
				info_msg("Noch keine Bookmarks vorhanden!");
			}
		}
	}
?>
