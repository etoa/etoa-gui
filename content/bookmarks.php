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
	// 	File: bookmarks.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Target-Bookmarks-Manager
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	$mode = (isset($_GET['mode']) && $_GET['mode']!="") ? $_GET['mode'] : 'target';
	
	echo "<h1>Favoriten</h1>";
 	show_tab_menu("mode",array("target"=>"Zielfavoriten",
 														"fleet"=>"Flottenfavoriten"));
 	echo '<br/>';

	if ($mode=="fleet")
	{
		$_SESSION['currentEntity']=serialize($cp);
		
		$res = dbquery("SELECT ship_id,ship_name FROM ships WHERE ship_show=1 ORDER BY ship_type_id,ship_order;");
		while ($arr = mysql_fetch_row($res))
		{
			$ships[$arr[0]] = $arr[1];
		}
	
			// Favorit speichern ok
			if (isset($_POST['submit_new']) || isset($_POST['submit_edit']))
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
					$addships = "";
					foreach ($_POST['sid'] as $k => $sid)
					{
						if ($addships=="")
							$addships.= $sid.":".nf_back($_POST['scount'][$k]);
						else
							$addships.= ",".$sid.":".nf_back($_POST['scount'][$k]);
					}
					$freight = max(0,intval(nf_back($_POST['res1']))).",".
					max(0,intval(nf_back($_POST['res2']))).",".
					max(0,intval(nf_back($_POST['res3']))).",".
					max(0,intval(nf_back($_POST['res4']))).",".
					max(0,intval(nf_back($_POST['res5']))).",".
					max(0,intval(nf_back($_POST['resp'])))."";
					
					$fetch = max(0,intval(nf_back($_POST['fetch1']))).",".
					max(0,intval(nf_back($_POST['fetch2']))).",".
					max(0,intval(nf_back($_POST['fetch3']))).",".
					max(0,intval(nf_back($_POST['fetch4']))).",".
					max(0,intval(nf_back($_POST['fetch5']))).",".
					max(0,intval(nf_back($_POST['fetchp'])))."";
					
					if (isset($_POST['submit_new']))
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
							action
						) 
						VALUES 
						(
							'".$cu->id."',
							'".addslashes($_POST['name'])."',
							'".$arr[0]."',
							'".$addships."',
							'".$freight."',
							'".$fetch."',
							'".$_POST['action']."'
						);");
								
						ok_msg("Der Favorit wurde hinzugef&uuml;gt!");
					}
					elseif (isset($_POST['submit_edit']))
					{
						dbquery("
							UPDATE
								fleet_bookmarks
							SET
								name='".addslashes($_POST['name'])."',
								target_id='".$arr[0]."',
								ships='".$addships."',
								res='".$freight."',
								resfetch='".$fetch."',
								action='".$_POST['action']."'
							WHERE
								user_id='".$cu->id."'
								AND id='".$_POST['id']."'
							LIMIT 1;");
						
						ok_msg("Der Favorit wurde gespeichert!");
					}
				}
				else
				{
					err_msg("Es existiert kein Objekt an den angegebenen Koordinaten!");
				}
			}

			// Favorit löschen ok
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
			
			if (isset($_GET['edit']) && $_GET['edit']>0)
			{
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
									ON entities.cell_id=cells.id
										AND entities.id='".$barr['target_id']."'
									LIMIT 1");
					if (mysql_num_rows($eres))
					{
						$earr=mysql_fetch_assoc($eres);
						
						echo "<form action=\"?page=$page&amp;mode=$mode\" method=\"post\">";
						iBoxStart("Favorit bearbeiten");
						checker_init();
						echo "<div id=\"checker\" style=\"display:none\"><input type=\"text\" name=\"id\" value=\"".$barr['id']."\" size=\"4\" /></div>";
						echo "Name: <input type=\"text\" name=\"name\" size=\"20\" maxlen=\"200\" value=\"".$barr['name']."\" onfocus=\"if (this.value=='Name') this.value=''\" /> &nbsp; ";
						echo "Ziel: <select name=\"sx\">";
						for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
						{
							echo "<option ";
							if ($x==$earr['sx']) echo "selected ";
							echo "value=\"$x\">$x</option>";
						}
						echo "</select> / <select name=\"sy\">";
						for ($y=1;$y<=$conf['num_of_sectors']['p2'];$y++)
						{
							echo "<option ";
							if ($y==$earr['sy']) echo "selected ";
							echo "value=\"$y\">$y</option>";
						}
						echo "</select> : <select name=\"cx\">";
						for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
						{
							echo "<option ";
							if ($x==$earr['cx']) echo "selected ";
							echo "value=\"$x\">$x</option>";
						}
						echo "</select> / <select name=\"cy\">";
						for ($y=1;$y<=$conf['num_of_cells']['p2'];$y++)
						{
							echo "<option ";
							if ($y==$earr['cy']) echo "selected ";
							echo "value=\"$y\">$y</option>";
						}
						echo "</select> : <select name=\"pos\">";
						for ($y=0;$y<=$conf['num_planets']['p2'];$y++)
						{
							echo "<option ";
							if ($y==$earr['pos']) echo "selected ";
							echo "value=\"$y\">$y</option>";
						}
						echo "</select> &nbsp; ";
						echo " <select name=\"action\">";
						foreach (FleetAction::getAll() as $ai)
						{
							echo "<option ";
							if ($ai->code()==$barr['action']) echo "selected ";
							echo "value=\"".$ai->code()."\" style=\"color:".$ai->color()."\">".$ai->name()."</option>";
						}
						echo "</select><br/><br/>";
						
						$cnt=0;
						echo "<div id=\"shipboxadd\" style=\"float:left;\">";
						$sidarr = explode(",",$barr['ships']);
						foreach ($sidarr as $sd)
						{
							$sdi = explode(":",$sd);
							echo "Schiffe hinzufügen: <input type=\"text\" name=\"scount[]\" id=\"ship_".$cnt."\" value=\"".$sdi[1]."\" size=\"6\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/>&nbsp;
								<select name=\"sid[]\">";
							foreach ($ships as $k => $v)
							{
								echo "<option ";
								if ($k==$sdi[0]) echo "selected ";
								echo "value=\"".$k."\">".$v."</option>";
							}
							echo "</select>&nbsp;<a onclick=\"xajax_addBookmarkShip(xajax.getFormValues('shipboxadd'),$cnt);\"><img src=\"images/icons/delete.png\" alt=\"Löschen\" style=\"width:16px;height:15px;border:none;\" title=\"Löschen\" /></a><br />";
							$cnt++;
						}
						echo "</div><input type=\"button\" value=\"Mehr Schiffe hinzufügen\" onclick=\"xajax_addBookmarkShip(xajax.getFormValues('shipboxadd'));\" />
							<br style=\"clear:both;\" />";
						
						$res = explode(",",$barr['res']);
						echo "<table style=\"margin:10px;width:275px;float:left;\" class=\"tb\">
						<tr><th colspan=\"2\">Fracht</th></tr>
						<tr><th>".RES_ICON_METAL."".RES_METAL."</th>
						<td><input type=\"text\" name=\"res1\" id=\"res1\" value=\"".$res[0]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
						<td><input type=\"text\" name=\"res2\" id=\"res2\" value=\"".$res[1]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
						<td><input type=\"text\" name=\"res3\" id=\"res3\" value=\"".$res[2]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_FUEL."".RES_FUEL."</th>
						<td><input type=\"text\" name=\"res4\" id=\"res4\" value=\"".$res[3]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_FOOD."".RES_FOOD."</th>
						<td><input type=\"text\" name=\"res5\" id=\"res5\" value=\"".$res[4]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_PEOPLE."Passagiere</th>
						<td><input type=\"text\" name=\"resp\" id=\"resp\" value=\"".$res[5]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>					
						</table>";
						
						$resfetch = explode(",",$barr['resfetch']);
						echo "<table style=\"margin:10px;width:275px;float:left;\" class=\"tb\">
						<tr><th colspan=\"2\">Abholauftrag</th></tr>
						<tr><th>".RES_ICON_METAL."".RES_METAL."</th>
						<td><input type=\"text\" name=\"fetch1\" id=\"fres1\" value=\"".$resfetch[0]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
						<td><input type=\"text\" name=\"fetch2\" id=\"fres2\" value=\"".$resfetch[1]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
						<td><input type=\"text\" name=\"fetch3\" id=\"fres3\" value=\"".$resfetch[2]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_FUEL."".RES_FUEL."</th>
						<td><input type=\"text\" name=\"fetch4\" id=\"fres4\" value=\"".$resfetch[3]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_FOOD."".RES_FOOD."</th>
						<td><input type=\"text\" name=\"fetch5\" id=\"fres5\" value=\"".$resfetch[4]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
						<tr><th>".RES_ICON_PEOPLE."Passagiere</th>
						<td><input type=\"text\" name=\"fetchp\" id=\"fresp\" value=\"".$resfetch[5]."\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>					
						</table>
						<br style=\"clear:both\" />";                                                                                   
						echo "<br/>Wichtig: Die Flotte wird nur starten, falls die Schiffe und das Ziel die gewählte Aktion unterstützen. Es muss pro Schiffstyp mindestens ein Schiff vorhanden sein, damit die Flotte startet. Bei den Rohstoffen wird Rohstoff für Rohstoff jeweils das Maximum eingeladen.
						<br/><br/><input type=\"submit\" value=\"Speichern\" name=\"submit_edit\" />";
						iBoxEnd();
						echo "</form>";
					}
				}
			}
			else
			{
				echo "<form action=\"?page=$page&amp;mode=$mode\" method=\"post\">";
				iBoxStart("Favorit hinzuf&uuml;gen");
				checker_init();
				echo "Name: <input type=\"text\" name=\"name\" size=\"20\" maxlen=\"200\" value=\"Name\" onfocus=\"if (this.value=='Name') this.value=''\" /> &nbsp; ";
				echo "Ziel: <select name=\"sx\">";
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
				echo " <select name=\"action\">";
				foreach (FleetAction::getAll() as $ai)
				{
					echo "<option value=\"".$ai->code()."\" style=\"color:".$ai->color()."\">".$ai->name()."</option>";
				}
				echo "</select><br/><br/>";
				echo "<div id=\"shipboxadd\" style=\"float:left;\">
				Schiffe hinzufügen: <input type=\"text\" name=\"scount[]\" id=\"ship0\" value=\"1\" size=\"6\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/>&nbsp;
				<select name=\"sid[]\">";
				foreach ($ships as $k => $v)
				{
					echo "<option value=\"".$k."\">".$v."</option>";
				}
				echo "</select>&nbsp;<a onclick=\"xajax_addBookmarkShip(xajax.getFormValues('shipboxadd'),0);\"><img src=\"images/icons/delete.png\" alt=\"Löschen\" style=\"width:16px;height:15px;border:none;\" title=\"Löschen\" /></a></div>
				<input type=\"button\" value=\"Mehr Schiffe hinzufügen\" onclick=\"xajax_addBookmarkShip(xajax.getFormValues('shipboxadd'));\" />
				<br style=\"clear:both;\" />";
				
				echo "<table style=\"margin:10px;width:275px;float:left;\" class=\"tb\">
				<tr><th colspan=\"2\">Fracht</th></tr>
				<tr><th>".RES_ICON_METAL."".RES_METAL."</th>
				<td><input type=\"text\" name=\"res1\" id=\"res1\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
				<td><input type=\"text\" name=\"res2\" id=\"res2\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
				<td><input type=\"text\" name=\"res3\" id=\"res3\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_FUEL."".RES_FUEL."</th>
				<td><input type=\"text\" name=\"res4\" id=\"res4\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_FOOD."".RES_FOOD."</th>
				<td><input type=\"text\" name=\"res5\" id=\"res5\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_PEOPLE."Passagiere</th>
				<td><input type=\"text\" name=\"resp\" id=\"resp\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>					
				</table>
	
				<table style=\"margin:10px;width:275px;float:left;\" class=\"tb\">
				<tr><th colspan=\"2\">Abholauftrag</th></tr>
				<tr><th>".RES_ICON_METAL."".RES_METAL."</th>
				<td><input type=\"text\" name=\"fetch1\" id=\"fres1\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
				<td><input type=\"text\" name=\"fetch2\" id=\"fres2\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
				<td><input type=\"text\" name=\"fetch3\" id=\"fres3\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_FUEL."".RES_FUEL."</th>
				<td><input type=\"text\" name=\"fetch4\" id=\"fres4\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_FOOD."".RES_FOOD."</th>
				<td><input type=\"text\" name=\"fetch5\" id=\"fres5\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>
				<tr><th>".RES_ICON_PEOPLE."Passagiere</th>
				<td><input type=\"text\" name=\"fetchp\" id=\"fresp\" value=\"0\" size=\"9\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/></td></tr>					
				</table>
				<br style=\"clear:both\" />";                                                                                   
				echo "<br/>Wichtig: Die Flotte wird nur starten, falls die Schiffe und das Ziel die gewählte Aktion unterstützen. Es muss pro Schiffstyp mindestens ein Schiff vorhanden sein, damit die Flotte startet. Bei den Rohstoffen wird Rohstoff für Rohstoff jeweils das Maximum eingeladen.
				<br/><br/><input type=\"submit\" value=\"Speichern\" name=\"submit_new\" />";
				iBoxEnd();
				echo "</form>";
			}

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
				tableStart("Gespeicherte Favoriten");
				echo "<tr>
								<th class=\"tbltitle\">Name</th>
								<th class=\"tbltitle\" colspan=\"2\">Ziel</th>
								<th class=\"tbltitle\">Aktion</th>
								<th class=\"tbltitle\">Schiffe</th>
								<th class=\"tbltitle\">Aktionen</th>
							</tr>";
				while ($arr=mysql_fetch_assoc($res))
				{
					$ent = Entity::createFactoryById($arr['target_id']);
					$ac = FleetAction::createFactory($arr['action']);
				
					$sidarr = explode(",",$arr['ships']);
					
					echo "<tr>
						<td class=\"tbldata\">".text2html($arr['name'])."</td>
						<td class=\"tbldata\" style=\"width:40px;background:#000\"><img src=\"".$ent->imagePath()."\" /></td>
						<td class=\"tbldata\">".$ent."<br/>(".$ent->entityCodeString().")</td>
						<td class=\"tbldata\">".$ac."</td>
						<td class=\"tbldata\">";
						$cnt = 0;
						foreach ($sidarr as $sd)
						{
							$sdi = explode(":",$sd);
							if ($cnt) echo ", ";
							echo $sdi[1]." ".$ships[$sdi[0]];
							$cnt++;
						}
						echo "</td>
						<td class=\"tbldata\">
							<a href=\"javascript:;\" onclick=\"xajax_launchBookmarkProbe(".$arr['id'].");\"  onclick=\"\">Starten</a> 
							<a href=\"?page=$page&amp;mode=$mode&amp;edit=".$arr['id']."\">Bearbeiten</a> 
							<a href=\"?page=$page&amp;mode=$mode&amp;del=".$arr['id']."\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a>
						</td>
					</tr>";
				}
				tableEnd();
				
				echo '<div id="fleet_info_box" style="display:none;">';
				iBoxStart("Flotten");
				echo '<div id="fleet_info"></div>';
				iBoxEnd();
				echo '</div>';
			}
			else
			{
				echo "<i>Noch keine Favoriten vorhanden!</i>";
			}			
		
		
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
								<th class=\"tbltitle\">Koordinaten</th>
								<td class=\"tbldata\">".$ent->entityCodeString()." - ".$ent."</td>
							</tr>
							<tr>
								<th class=\"tbltitle\">Kommentar</th>
								<td class=\"tbldata\">
									<textarea name=\"bookmark_comment\" rows=\"3\" cols=\"60\">".stripslashes($arr['comment'])."</textarea>
								</td>
							</tr>";
				tableEnd();
				
				echo "<input type=\"hidden\" name=\"bookmark_id\" value=\"".$_GET['edit']."\" />";
				echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_edit_target\" /> &nbsp; ";
			}
			else
			{
				echo "<b>Fehler:</b> Datensatz nicht gefunden!<br/><br/>";
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
						error_msg("Es existiert kein Objekt an den angegebenen Koordinaten!!");
					}
				}
				else
				{
					error_msg("Das Gebiet ist noch nicht erkundet!!");
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
						<td class=\"tbldata\" colspan=\"6\" style=\"text-align:center;\">
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
								<th class=\"tbltitle\" colspan=\"2\">Typ</th>
								<th class=\"tbltitle\">Koordinaten</th>
								<th class=\"tbltitle\">Besitzer</th>
								<th class=\"tbltitle\">Kommentar</th>
								<th class=\"tbltitle\">Aktionen</th>
							</tr>";
				while ($arr=mysql_fetch_assoc($res))
				{
					$ent = Entity::createFactory($arr['code'],$arr['entity_id']);
				
					echo "<tr>
										<td class=\"tbldata\" style=\"width:40px;background:#000\"><img src=\"".$ent->imagePath()."\" /></td>
										<td class=\"tbldata\">".$ent->entityCodeString()."</td>
										<td class=\"tbldata\">".$ent."</td>
										<td class=\"tbldata\">".$ent->owner()."</td>
										<td class=\"tbldata\">".text2html($arr['comment'])."</td>
										<td class=\"tbldata\">
											<a href=\"?page=haven&amp;target=".$ent->id()."\">Flotte</a> 
											<a href=\"?page=entity&amp;id=".$ent->id()."&amp;hl=".$ent->id()."\">Infos</a> 
											<a href=\"?page=cell&amp;id=".$ent->cellId()."&amp;hl=".$ent->id()."\">System</a> 
											<a href=\"?page=$page&amp;edit=".$arr['id']."\">Bearbeiten</a> 
											<a href=\"?page=$page&amp;del=".$arr['id']."\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a>
									</td>
							</tr>";
				}
				tableEnd();
			}
			else
			{
				echo "<i>Noch keine Bookmarks vorhanden!</i>";
			}
		}
	}
?>
