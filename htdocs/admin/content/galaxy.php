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
	// 	Dateiname: galaxy.php
	// 	Topic: Verwaltung der Galaxie
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	//
	// Beistzerprüfung
	//
	if ($sub=="map")
	{
		echo "<h1>Galaxiekarte</h1>";
		echo "Anzeigen: <select onchange=\"document.getElementById('img').src='../misc/map.image.php'+this.options[this.selectedIndex].value;\">
		<option value=\"?req_admin&amp;t=".time()."\">Normale Galaxieansicht</option>
		<option value=\"?req_admin&amp;type=populated&t=".time()."\">Bev&ouml;lkerte Systeme</option>
		
		</select><br/><br/>";
		echo "<img src=\"../misc/map.image.php?req_admin\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/>";		
		
	}

	//
	// Universe Maintenance
	//
	elseif ($sub=="uni")
	{
    require("galaxy/universe.php");
	}
  
	//
	// Integrity check
	//
	elseif ($sub=="galaxycheck")
	{	
    require("galaxy/galaxycheck.php");
	}
	
	elseif ($sub=="planet_types")
	{
		advanced_form("planet_types");
	}
	elseif ($sub=="sol_types")
	{
		advanced_form("sol_types");
	}
	
	/*
	elseif ($sub=="cells")
	{
		echo "<h1>Zellen</h1>";
		if ($_GET['editcell']>0)
		{

			//
			// Änderungen vornehmen
			//
			if ($_POST['cell_submit']!="")
			{

				// Sonnensystem Änderung
				if ($_POST['cell_solsys_solsys_sol_type']!="")
				{
					dbquery("UPDATE space_cells SET cell_solsys_solsys_sol_type=".$_POST['cell_solsys_solsys_sol_type'].",cell_solsys_name='".$_POST['cell_solsys_name']."',cell_solsys_num_planets=".$_POST['cell_solsys_num_planets']." WHERE cell_id='".$_GET['editcell']."';");
				}
				// Nebelfeld Änderung
				if ($_POST['cell_nebula_ress']!="" && $_POST['cell_nebula']>0)
				{
					dbquery("UPDATE space_cells SET cell_nebula_ress=".$_POST['cell_nebula_ress'].",cell_nebula=1 WHERE cell_id='".$_GET['editcell']."';");
				}
				// Asteroidenfeld Änderung
				if ($_POST['cell_asteroid_ress']!="" && $_POST['cell_asteroid']>0)
				{
					dbquery("UPDATE space_cells SET cell_asteroid_ress=".$_POST['cell_asteroid_ress'].",cell_asteroid=1 WHERE cell_id='".$_GET['editcell']."';");
				}
				// Wurmloch Neu
				if ($_POST['new_cell_wormhole_id']>0)
				{
					dbquery("UPDATE space_cells SET cell_wormhole_id=".$_POST['new_cell_wormhole_id']." WHERE cell_id='".$_GET['editcell']."';");
					dbquery("UPDATE space_cells SET cell_wormhole_id=".$_GET['editcell']." WHERE cell_id='".$_POST['new_cell_wormhole_id']."';");
				}
				// Wurmloch Änderung
				if ($_POST['cell_wormhole_id']>0)
				{

					change_wormhole($_GET['editcell'],$_POST['cell_wormhole_id']);

				}
				echo "&Auml;nderungen gespeichert!<br/>";
			}

			//
			// Änderungen vornehmen
			//
			elseif ($_POST['remove_content']!="")
			{
				$res=dbquery("SELECT cell_wormhole_id FROM space_cells WHERE cell_id='".$_GET['editcell']."';");
				$arr=mysql_fetch_array($res);
				if ($arr['cell_wormhole_id']>0)
				{
					dbquery("UPDATE space_cells SET cell_wormhole_id=0,cell_wormhole_changed=0	WHERE cell_id='".$arr['cell_wormhole_id']."';");
				}

				dbquery("UPDATE space_cells SET
				cell_nebula_ress=0,
				cell_nebula=0,
				cell_asteroid_ress=0,
				cell_asteroid=0,
				cell_wormhole_id=0,
				cell_solsys_solsys_sol_type=0,
				cell_solsys_name='',
				cell_solsys_num_planets=0
				WHERE cell_id='".$_GET['editcell']."';");
				echo "&Auml;nderungen gespeichert!<br/>";
			}


			$res=dbquery("SELECT * FROM space_cells WHERE cell_id='".$_GET['editcell']."';");
			if (mysql_num_rows($res)>0)
			{
 				$tres = dbquery("SELECT type_id,type_name FROM sol_types;");
 				$solsys_types=array();
				if (mysql_num_rows($tres))
				{
					while ($tarr=mysql_fetch_array($tres))
					{
						$solsys_types[$tarr['type_id']]=$tarr['type_name'];
					}
				}

 				$tres = dbquery("SELECT cell_id,cell_sx,cell_sy,cell_cx,cell_cy FROM space_cells WHERE cell_wormhole_id>0;");
 				$wormholes=array();
				if (mysql_num_rows($tres))
				{
					while ($tarr=mysql_fetch_array($tres))
					{
						$wormholes[$tarr['cell_id']]=$tarr['cell_sx']."/".$tarr['cell_sy']." : ".$tarr['cell_cx']."/".$tarr['cell_cy'];
					}
				}
 				$tres = dbquery("SELECT cell_id,cell_sx,cell_sy,cell_cx,cell_cy FROM space_cells WHERE cell_wormhole_id=0 AND cell_nebula=0 AND cell_asteroid=0 AND cell_solsys_num_planets=0 AND cell_solsys_solsys_sol_type=0;");
 				$empty=array();
				if (mysql_num_rows($tres))
				{
					while ($tarr=mysql_fetch_array($tres))
					{
						$empty[$tarr['cell_id']]=$tarr['cell_sx']."/".$tarr['cell_sy']." : ".$tarr['cell_cx']."/".$tarr['cell_cy'];
					}
				}

				$arr=mysql_fetch_array($res);
				echo "<h2>Zelle ".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." bearbeiten</h2>";
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;editcell=".$_GET['editcell']."\" method=\"post\">";

				//
				// Asteroidenfeld bearbeiten
				//
				if ($arr['cell_asteroid']>0)
				{
					echo "<input type=\"hidden\" name=\"cell_asteroid\" value=\"1\" />";
					echo "<table class=\"tb\">";
					echo "<tr><th class=\"tbltitle\">Zellentyp</th><td>Asteroidenfeld</td></tr>";
					echo "<tr><th class=\"tbltitle\">Ressourcen</th><td><input type=\"text\" name=\"cell_asteroid_ress\" value=\"".$arr['cell_asteroid_ress']."\" /></td></tr>";
					echo "</table><br/><input type=\"submit\" name=\"remove_content\" value=\"Zelle in leeren Raum umwandeln\" /><br/>";
				}

				//
				// Nebelfeld bearbeiten
				//
				elseif ($arr['cell_nebula']>0)
				{
					echo "<input type=\"hidden\" name=\"cell_nebula\" value=\"1\" />";
					echo "<table class=\"tb\">";
					echo "<tr><th class=\"tbltitle\">Zellentyp</th><td>Nebelfeld</td></tr>";
					echo "<tr><th class=\"tbltitle\">Ressourcen</th><td><input type=\"text\" name=\"cell_nebula_ress\" value=\"".$arr['cell_nebula_ress']."\" /></td></tr>";
					echo "</table><br/><input type=\"submit\" name=\"remove_content\" value=\"Zelle in leeren Raum umwandeln\" /><br/>";
				}

				//
				// Wurmloch bearbeiten
				//
				elseif ($arr['cell_wormhole_id']>0)
				{
					echo "<table class=\"tb\">";
					echo "<tr><th class=\"tbltitle\">Wurmloch nach</th><td><select name=\"cell_wormhole_id\">";
					foreach ($wormholes as $id=>$val)
					{
						if ($id!=$arr['cell_id'])
						{
							echo "<option value=\"$id\"";
							if ($id==$arr['cell_wormhole_id']) echo " selected=\"selected\"";
							echo ">$val</option>";
						}
					}
					echo "</select> Bei einer &Auml;nderung wird das alte freiwerdende Wurmloch mit <br/>dem nun freigewordenen Partner des neuen Wurmlochs verkn&uuml;pft!</td></tr>";
					echo "<tr><th class=\"tbltitle\">Letzte &Auml;nderung</th><td>";
					if ($arr['cell_wormhole_changed'])
						echo date("d.m.Y H:i",$arr['cell_wormhole_changed']);
					else
						echo "Nie";
					echo "</td></tr>";
					echo "</table><br/><input type=\"submit\" name=\"remove_content\" value=\"Zelle in leeren Raum umwandeln\" /><br/>";
				}

				//
				// Sonnensystem bearbeiten
				//
				elseif ($arr['cell_solsys_solsys_sol_type']>0)
				{
					// Fehlende Planeten erstellen
					if ($_GET['action']=="createmissingplanets")
					{
						$pres=dbquery("SELECT id,planet_solsys_pos FROM planets WHERE planet_solsys_id=".$arr['cell_id']." ORDER BY planet_solsys_pos;");
						$createCnt = $arr['cell_solsys_num_planets']-mysql_num_rows($pres);
						$positions=array();
						if (mysql_num_rows($pres)>0)
						{
							while ($parr=mysql_fetch_array($pres))
							{
								array_push($positions,$parr['planet_solsys_pos']);
							}
						}
						$pos=1;
						while ($createCnt>0)
						{
							if (!in_array($pos,$positions))
							{
								$num_planet_types=mysql_num_rows(dbquery("SELECT type_id FROM planet_types;"));
								$num_planet_images = $conf['num_planet_images']['v'];
								$planet_fields_min=$conf['planet_fields']['p1'];
								$planet_fields_max=$conf['planet_fields']['p2'];
								$planet_temp_min=$conf['planet_temp']['p1'];
								$planet_temp_max=$conf['planet_temp']['p2'];
								$planet_temp_diff=$conf['planet_temp']['v'];
								$planet_temp_totaldiff=abs($planet_temp_min)+abs($planet_temp_max);
								$num_planets_min=$conf['num_planets']['p1'];
								$num_planets_max=$conf['num_planets']['p2'];
								$np = $arr['cell_solsys_num_planets'];
								$pt = mt_rand(1,$num_planet_types);
								$img_nr = $pt."_".mt_rand(1,$num_planet_images);
								$fields = mt_rand($planet_fields_min,$planet_fields_max);
								$tblock =  round($planet_temp_totaldiff / $np);
								$temp = mt_rand($planet_temp_max-($tblock*$cnp),($planet_temp_max-($tblock*$pos)+$tblock));
								$tmin = $temp - $planet_temp_diff;
								$tmax = $temp + $planet_temp_diff;
								$sql = "INSERT INTO planets (
									planet_solsys_id,
									planet_solsys_pos,
									planet_type_id,
									planet_fields,
									planet_image,
									planet_temp_from,
									planet_temp_to
								) VALUES(
								'".$arr['cell_id']."',
								'$pos',
								'$pt',
								'$fields',
								'$img_nr',
								'$tmin',
								'$tmax'
								)";
								dbquery($sql);
								$createCnt--;
								echo "Planet mit ID <b>".mysql_insert_id()."</b> auf Position <b>$pos</b> erstellt.<br/>";
							}
							$pos++;
						}
					}

					echo "<table class=\"tb\">";
					echo "<tr><th class=\"tbltitle\">Zellentyp</th><td>Sonnensystem</td></tr>";
					echo "<tr><th class=\"tbltitle\">Stern-Typ</th><td><select name=\"cell_solsys_solsys_sol_type\">";
					foreach ($solsys_types as $id=>$val)
					{
						echo "<option value=\"$id\"";
						if ($id==$arr['cell_solsys_solsys_sol_type']) echo " selected=\"selected\"";
						echo ">$val</option>";
					}
					echo "</select></td></tr>";
					echo "<tr><th class=\"tbltitle\">Name des Sterns</th><td><input type=\"text\" name=\"cell_solsys_name\" value=\"".$arr['cell_solsys_name']."\" /></td></tr>";
					echo "<tr><th>Anzahl Planeten</th><td><select name=\"cell_solsys_num_planets\">";
					for ($x=$conf['num_planets']['p1'];$x<=$conf['num_planets']['p2'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($x==$arr['cell_solsys_num_planets']) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select></td></tr>";
					echo "<tr><th class=\"tbltitle\">Planeten</th><td>";
					$pres=dbquery("SELECT planet_name,id,planet_solsys_pos,type_name FROM planets,planet_types WHERE planet_type_id=type_id AND planet_solsys_id=".$arr['cell_id']." ORDER BY planet_solsys_pos;");
					if (mysql_num_rows($res)>0)
					{
						echo "<table class=\"tb\">";
						while ($parr=mysql_fetch_array($pres))
						{
							if ($parr['planet_name']=="") $parr['planet_name']="<i>Unbenannt</i>";
							echo "<tr><td>".$parr['planet_solsys_pos']."</td>";
							echo "<td>".$parr['planet_name']."</td>";
							echo "<td>".$parr['type_name']."</td>";
							echo "<td>".edit_button("?page=galaxy&amp;sub=edit&amp;planet_id=".$parr['id']);
							//if ($parr['planet_user_id']==0)
							//	echo " ".del_button("?page=$page&amp;sub=$sub&amp;action=deleteplanet&amp;planet_id=".$parr['id']);
							echo "</td></tr>";
						}
						echo "</table>";
					}
					if (mysql_num_rows($pres)<$arr['cell_solsys_num_planets'])
					{
						echo "Es fehlen noch <b>".($arr['cell_solsys_num_planets']-mysql_num_rows($pres))."</b> Planeten!<br/><br/>";
						echo "<input type=\"button\" value=\"Fehlende Planeten erstellen\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;editcell=".$arr['cell_id']."&amp;action=createmissingplanets'\" />";
					}
					echo "</td></tr>";
					echo "</table>";
				}

				//
				// Feld aus leerem Feld erstellen
				//
				elseif ($_POST['create_content']!="")
				{
					switch ($_POST['cell_type'])
					{
						case "solsys":
							echo "<h3>Zelle in Sonnensystem umwandeln</h3>";
							echo "<table class=\"tb\">";
							echo "<tr><th>Stern-Typ</th><td><select name=\"cell_solsys_solsys_sol_type\">";
							foreach ($solsys_types as $id=>$val)
							{
								echo "<option value=\"$id\"";
								echo ">$val</option>";
							}
							echo "</select></td></tr>";
							echo "<tr><th>Name des Sterns</th><td><input type=\"text\" name=\"cell_solsys_name\" value=\"".$arr['cell_solsys_name']."\" /></td></tr>";
							echo "<tr><th>Anzahl Planeten</th><td><select name=\"cell_solsys_num_planets\">";
							for ($x=$conf['num_planets']['p1'];$x<=$conf['num_planets']['p2'];$x++)
							{
								echo "<option value=\"$x\">$x</option>";
							}
							echo "</select></td></tr>";
							echo "</table>";
							break;
						case "nebula":
							echo "<h3>Zelle in Nebelfeld umwandeln</h3>";
							echo "<input type=\"hidden\" name=\"cell_nebula\" value=\"1\" />";
							echo "<table class=\"tb\">";
							echo "<tr><th class=\"tbltitle\">Ressourcen</th><td><input type=\"text\" name=\"cell_nebula_ress\" value=\"".$arr['cell_nebula_ress']."\" /></td></tr>";
							echo "</table>";
							break;
						case "asteroid":
							echo "<h3>Zelle in Asteroidenfeld umwandeln</h3>";
							echo "<input type=\"hidden\" name=\"cell_asteroid\" value=\"1\" />";
							echo "<table class=\"tb\">";
							echo "<tr><th class=\"tbltitle\">Ressourcen</th><td class=\"tbldata\"><input type=\"text\" name=\"cell_asteroid_ress\" value=\"".$arr['cell_asteroid_ress']."\" /></td></tr>";
							echo "</table>";
							break;
						case "wormhole":
							echo "<h3>Zelle in Wurmloch umwandeln</h3>";
							echo "<table class=\"tb\">";
							echo "<tr><th>Ziel-Feld</th><td><select name=\"new_cell_wormhole_id\">";
							foreach ($empty as $id=>$val)
							{
								if ($id!=$arr['cell_id'])
								echo "<option value=\"$id\">$val</option>";
							}
							echo "</select></td></tr>";
							echo "</table>";
							break;
						default:
					}
				}

				//
				// Show empty cell
				//
				else
				{
					echo "<table class=\"tb\">";
					echo "<tr><th class=\"tbltitle\">Zellentyp</th><td class=\"tbldata\">Leerer Raum</td></tr>";
					echo "</table><br/>";
					echo "<b>Zelle umwandeln:</b> <select name=\"cell_type\">";
					echo "<option value=\"solsys\">Sonnensystem</option>";
					echo "<option value=\"nebula\">Nebel</option>";
					echo "<option value=\"asteroid\">Asteroidenfeld</option>";
					echo "<option value=\"wormhole\">Wurmloch</option>";
					echo "</select> <input type=\"submit\" name=\"create_content\" value=\"erstellen\" /><br/>";
				}
				echo "<br/><input type=\"submit\" name=\"cell_submit\" value=\"&Uuml;bernehmen\"/> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" value=\"Zur&uuml;ck zu den Suchergebnissen\"/></form>";
			}
			else
				cms_err_msg("Datensatz wurde nicht gefunden!");
		}
		//
		// Zellen Suchresultate
		//
		elseif ($_POST['cell_search']!="" || $_GET['action']=="searchresults")
		{
			if ($_SESSION['cells']['query']=="")
			{
				if ($_POST['cell_cx']!="")
				{
					$sql.= " AND cell_cx='".$_POST['cell_cx']."'";
				}
				if ($_POST['cell_cy']!="")
				{
					$sql.= " AND cell_cy='".$_POST['cell_cy']."'";
				}
				if ($_POST['cell_sx']!="")
				{
					$sql.= " AND cell_sx='".$_POST['cell_sx']."'";
				}
				if ($_POST['cell_sy']!="")
				{
					$sql.= " AND cell_sy='".$_POST['cell_sy']."'";
				}
				if ($_POST['type']!="")
				{
					if ($_POST['type']=="solsys")
						$str="cell_solsys_num_planets>0";
					elseif ($_POST['type']=="asteroid")
						$str="cell_asteroid>0";
					elseif ($_POST['type']=="nebula")
						$str="cell_nebula>0";
					elseif ($_POST['type']=="wormhole")
						$str="cell_wormhole_id>0";
					$sql.= " AND $str";
				}
				if ($_POST['solsys_name']!="")
				{
					if (stristr($_POST['qmode']['solsys_name'],"%")) $addchars = "%";else $addchars = "";
					$sql.= " AND cell_solsys_name ".stripslashes($_POST['qmode']['solsys_name']).$_POST['solsys_name']."$addchars'";
				}

				$sqlstart = "SELECT * FROM space_cells WHERE 1 ";
				$sqlend = " ORDER BY cell_sx,cell_sy,cell_cx,cell_cy;";
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['cells']['query']=$sql;
			}
			else
				$sql = $_SESSION['cells']['query'];

			$res = dbquery($sql);
			if (mysql_num_rows($res)>0)
			{
				echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
				if (mysql_num_rows($res)>20)
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub'\" value=\"Neue Suche\" /><br/><br/>";

 				$tres = dbquery("SELECT type_id,type_name FROM sol_types;");
 				$solsys_types=array();
				if (mysql_num_rows($tres))
				{
					while ($tarr=mysql_fetch_array($tres))
					{
						$solsys_types[$tarr['type_id']]=$tarr['type_name'];
					}
				}

 				$tres = dbquery("SELECT cell_id,cell_sx,cell_sy,cell_cx,cell_cy FROM space_cells WHERE cell_wormhole_id>0;");
 				$wormholes=array();
				if (mysql_num_rows($tres))
				{
					while ($tarr=mysql_fetch_array($tres))
					{
						$wormholes[$tarr['cell_id']]=$tarr['cell_sx']."/".$tarr['cell_sy']." : ".$tarr['cell_cx']."/".$tarr['cell_cy'];
					}
				}

				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" valign=\"top\">ID</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Koordinaten</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Typ</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Aktion</td>";
				echo "<td>&nbsp;</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					echo "<tr>";
					echo "<td class=\"tbldata\">".$arr['cell_id']."</td>";
					echo "<td class=\"tbldata\">".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']."</td>";
					if ($arr['cell_asteroid']>0) echo "<td class=\"tbldata\">Asteroidenfeld (".nf($arr['cell_asteroid_ress']).")";
					elseif ($arr['cell_nebula']>0) echo "<td class=\"tbldata\">Nebel (".nf($arr['cell_nebula_ress']).")";
					elseif ($arr['cell_wormhole_id']>0) echo "<td class=\"tbldata\">Wurmloch nach ".$wormholes[$arr['cell_wormhole_id']]."";
					elseif ($arr['cell_solsys_solsys_sol_type']>0)
						echo "<td class=\"tbldata\">System <b>".$arr['cell_solsys_name']."</b> (<a href=\"?page=galaxy&sub=sol_types&tmp=1&action=edit&id=".$arr['cell_solsys_solsys_sol_type']."\" title=\"Typ bearbeiten\">".$solsys_types[$arr['cell_solsys_solsys_sol_type']]."</a>), ".$arr['cell_solsys_num_planets']." Planeten";
					else
						echo "<td class=\"tbldata\"><i>Leerer Raum</i>";
					echo "</td>";
					echo "<td class=\"tbldata\">".edit_button("?page=$page&amp;sub=$sub&amp;editcell=".$arr['cell_id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub'\" value=\"Neue Suche\" />";
			}
			else
			{
				$_SESSION['planets']['query']=Null;
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub'\" value=\"Zur&uuml;ck\" />";
			}
		}
		else
		{
			$_SESSION['cells']['query']=Null;
			echo "Suchmaske:<br/><br/>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">Koordinaten</td><td class=\"tbldata\"><select name=\"cell_sx\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_sy\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> : <select name=\"cell_cx\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_cy\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Typ</td><td class=\"tbldata\"><select name=\"type\">";
			echo "<option value=\"\">(egal)</option>";
			echo "<option value=\"solsys\">Sonnensystem</option>";
			echo "<option value=\"asteroid\">Asteroidenfeld</option>";
			echo "<option value=\"nebula\">Nebel</option>";
			echo "<option value=\"wormhole\">Wurmloch</option>";
			echo "</select></td></tr>";
			echo "<tr><th class=\"tbltitle\">Sytemname</th><td class=\"tbldata\"><input type=\"text\" name=\"solsys_name\" value=\"\" size=\"20\" maxlength=\"250\" />&nbsp;";
			fieldqueryselbox('solsys_name');
			echo "</td>";;
			echo "</table>";
			echo "<br/><input type=\"submit\" class=\"button\" name=\"cell_search\" value=\"Suche starten\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM space_cells;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/><br/>Die Galaxie besteht aus ".$conf['num_of_sectors']['p1']."x".$conf['num_of_sectors']['p2']." Sektoren und jeder Sektor aus ".$conf['num_of_cells']['p1']."x".$conf['num_of_cells']['p2']." Zellen.";
		}
	}
	*/
	
	else
	{
		$order_array=array();		
		$order_array['id']="Objekt-ID";
		$order_array['planet_name']="Objekt-Name";
		$order_array['type_name']="Objekt-Subtyp";
		$order_array['user_nick']="Besitzer-Name";

		echo "<h1>Raumobjekte (Entitäten)</h1>";
		
		$sa = array();
		$so = array();
		
		//
		// Details bearbeiten
		//
		if ($sub=="edit")
		{
			require("galaxy/edit.php");
		}
		
		//
		// Search query and result
		//
		elseif (searchQueryArray($sa,$so))
		{
			$table = "entities e";
			$joins = " INNER JOIN cells c ON c.id=e.cell_id ";
			$selects = "";			
			$sql = "";

			if (isset($sa['id']))
			{
				$sql.= " AND e.id ".searchFieldSql($sa['id']);
			}
			if (isset($sa['code']))
			{
				$sql.= " AND (";
				$i=0;
				foreach ($sa['code'][1] as $code)
				{
					if ($i>0)			
						$sql.=" OR";
					$sql .= " 
					e.code='".$code."'";
					$i++;
				}
				$sql.= ") ";
			}
			if (isset($sa['cell_cx']))
			{
				$sql.= " AND c.cx ".searchFieldSql($sa['cell_cx']);
			}
			if (isset($sa['cell_cy']))
			{
				$sql.= " AND c.cy ".searchFieldSql($sa['cell_cy']);
			}
			if (isset($sa['cell_c']))
			{
				$val = explode("_",$sa['cell_c'][1]);
				$sql.= " AND c.cx=".$val[0];
				$sql.= " AND c.cy=".$val[1];
			}			
			if (isset($sa['cell_sx']))
			{
				$sql.= " AND c.sx ".searchFieldSql($sa['cell_sx']);
			}
			if (isset($sa['cell_sy']))
			{
				$sql.= " AND c.sy ".searchFieldSql($sa['cell_sy']);
			}
			if (isset($sa['cell_s']))
			{
				$val = explode("_",$sa['cell_s'][1]);
				$sql.= " AND c.sx=".$val[0];
				$sql.= " AND c.sy=".$val[1];
			}			
			if (isset($sa['cell_pos']))
			{
				$sql.= " AND c.pos ".searchFieldSql($sa['cell_pos']);
			}

			if (isset($sa['name']))
			{
				$joins.= " INNER JOIN planets p ON p.id=e.id ";
				$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				
				$sql.= " AND p.planet_name ".searchFieldSql($sa['name']);
			}
			if (isset($sa['user_id']))
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";	
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}
				$sql.= " AND p.planet_user_id ".searchFieldSql($sa['user_id']);
			}				
			if (isset($sa['user_main']) && $sa['user_main'][1]<2 )
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";	
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}
				$sql.= " AND p.planet_user_main='".intval($sa['user_main'][1])."'";
			}			
			if (isset($sa['debris']) && $sa['debris'][1]<2)
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";	
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}
				if ($sa['debris'][1]==1)
					$sql.= " AND (p.planet_wf_metal>0 OR p.planet_wf_crystal>0 OR p.planet_wf_plastic>0)";
				else
					$sql.= " AND (p.planet_wf_metal=0 AND p.planet_wf_crystal=0 AND p.planet_wf_plastic=0)";
			}
			if (isset($sa['user_nick']))
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";	
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}

				$sql.= " AND users.user_nick ".searchFieldSql($sa['user_nick']);
				$joins.= " INNER JOIN users ON p.planet_user_id=user_id ";
			}	
			if (isset($sa['desc']) && $sa['desc'][1]<2)
			{
				if (!stristr($joins,"planets p"))
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";	
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
				}

				if ($sa['desc'][1]==1)
				{
					$sql.= " AND p.planet_desc!='' ";
				}
				else
				{
					$sql.= " AND p.planet_desc='' ";
				}
			}								
						
			// Build ordering
			if (count($so)>1)
			{
				$sql.=" ORDER BY ";
				foreach ($so as $k=> $v)
				{
					if ($k!="limit")
					{
						$sql.=" ".$k." ".($v == "d" ? "DESC" : "ASC")." ";
					}
				}
			}

			// Build limit
			$sql.=" LIMIT ".$so['limit'];

			// Build query
			$sql = "SELECT   
				SQL_CALC_FOUND_ROWS
				e.id,
				e.code, 
				e.pos,
				c.sx,c.sy,c.cx,c.cy ".
				$selects
				."
			FROM ".$table." 
			".$joins." 
			WHERE 1 ".$sql;
			
			// Execute query
			$res = dbquery($sql);
			$nr = mysql_num_rows($res);

			// Save query
			searchQuerySave($sa,$so);

			// Select total found rows
			$ares = dbquery("SELECT FOUND_ROWS()");			
			$aarr = mysql_fetch_row($ares);
			$enr =$aarr[0];
			
			echo "<h2>Suchresultate</h2>";
			echo "<form acton=\"?page=".$page."\" method=\"post\">";

			echo "<b>Abfrage:</b> ";
			$cnt=0;
			$n = count($sa);
			foreach ($sa as $k => $v)
			{
				echo "<i>$k</i> ".searchFieldOptionsName($v[0])." ";
				if (is_array($v[1]))
				{
					$scnt=0;
					$sn = count($v[1]);
					foreach ($v[1] as $sv)
					{
						echo "'$sv'";
						$scnt++;
						if ($scnt< $sn)
							echo " oder ";
					}
				}
				else
					echo "'".$v[1]."'";
				$cnt++;
				if ($cnt<$n)
					echo ", ";
			}
			echo "<br/><b>Ergebnis:</b> ".$nr." Datens&auml;tze (".$enr." total)<br/>";
			//, Sortierung: ".$order_array[$_POST['order']]."<br/>";
			echo "<b>Anzeigen:</b> <select name=\"search_limit\">";
			for ($x=100;$x<=2000;$x+=100)
			{
				echo "<option value=\"$x\"";
				if ($so['limit']==$x)
					echo " selected=\"selected\"";
				echo ">$x</option>";
			}
			echo "</select> Datensätze sortiert nach <select name=\"search_order\">";
			foreach ($order_array as $k=>$v)
			{
				echo "<option value=\"".$k."\"";
				if (isset($so[$k]))
					echo " selected=\"selected\"";
				echo ">".$v."</option>";
			}
			echo "</select> <input type=\"submit\" value=\"Anzeigen\" name=\"search_resubmit\" /></form><br/>";
			
			if ($nr > 0)
			{  
				if ($nr > 20)
				{
					echo button("Neue Suche","?page=$page&amp;newsearch")."<br/><br/>";
				}
				
				echo "<table class=\"tb\">";
				echo "<tr>";
				echo "<th style=\"width:40px;\">ID</th>";
				echo "<th style=\"width:90px;\">Koordinaten</th>";
				echo "<th>Entitätstyp</th>";
				echo "<th>Subtyp</th>";
				echo "<th>Name</th>";
				echo "<th>Besitzer</th>";
				echo "<th style=\"width:20px;\">&nbsp;</th>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					$ent = Entity::createFactory($arr['code'],$arr['id']);
					
					echo "<tr>";
					echo "<td>
						<a href=\"?page=$page&sub=edit&id=".$arr['id']."\">
						".$arr['id']."
						</a></td>";
					echo "<td>
						<a href=\"?page=$page&sub=edit&id=".$arr['id']."\">
						".$arr['sx']."/".$arr['sy']." : ".$arr['cx']."/".$arr['cy']." : ".$arr['pos']."
						</a>  </td>";
					echo "<td style=\"color:".Entity::$entityColors[$arr['code']]."\">";
					echo $ent->entityCodeString();
					echo " ".($ent->ownerMain() ? "(Hauptplanet)": '')."";
					echo "</td>";
					echo "<td>".$ent->type()."</td>";
					echo "<td>".$ent->name()."</td>";
					echo "<td>";
					if ($ent->ownerId()>0)
					{
						echo "<a href=\"?page=user&amp;sub=edit&amp;user_id=".$ent->ownerId()."\" title=\"Spieler bearbeiten\">
							".$ent->owner()."</a>";
					}					
					echo "
					</td>";
  				echo "<td>".edit_button("?page=$page&sub=edit&id=".$arr['id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/>".button("Neue Suche","?page=$page&amp;newsearch");
			}
			else
			{
				searchQueryReset();
				echo "Die Suche lieferte keine Resultate!<br/><br/>
				".button("Neue Suche","?page=$page&amp;newsearch");
			}
		}
	
		//
		// Suchmaske
		//

		else
		{
			echo "<h2>Suchmaske</h2>";
			echo "<form action=\"?page=$page\" method=\"post\" name=\"dbsearch\" autocomplete=\"off\">";
			echo "<table class=\"tb\" style=\"width:auto;margin:0px\">";
			echo "<tr>
				<th>ID:</th>
				<td><input type=\"text\" name=\"search_id\" value=\"\" size=\"5\" maxlength=\"10\" /></td></tr>";
			echo "<tr>
				<th style=\"width:160px\">Name:</th>
				<td>".searchFieldTextOptions('name')." <input type=\"text\" name=\"search_name\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr>
				<th>Koordinaten:</th>
				<td><select name=\"search_cell_s\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
			{
				for ($y=1;$y<=$conf['num_of_sectors']['p2'];$y++)
				{
					echo "<option value=\"".$x."_".$y."\">$x / $y</option>";
				}
			}				
			echo "</select> : <select name=\"search_cell_c\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
			{
				for ($y=1;$y<=$conf['num_of_cells']['p2'];$y++)
				{
					echo "<option value=\"".$x."_".$y."\">$x / $y</option>";
				}
			}
			echo "</select> : <select name=\"search_cell_pos\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=0;$x<=$conf['num_planets']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";
			echo "<tr>
				<th style=\"width:160px\">Entitätstyp:<br/><br/>
				<a href=\"javascript:;\" onclick=\"if (this.innerHTML=='Alles auswählen') { this.innerHTML='Auswahl aufheben';for(i=0;i<=7;i++) {document.getElementById('code_'+i).checked=true} } else {for(i=0;i<=7;i++) {document.getElementById('code_'+i).checked=false};this.innerHTML='Alles auswählen';}\">Alles auswählen</a>
				</tH>
				<td>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_0\" value=\"p\" /> Planet<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_1\" value=\"s\" /> Stern<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_2\" value=\"n\" /> Nebel<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_3\" value=\"a\" /> Asteroidenfeld<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_4\" value=\"w\" /> Wurmloch<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_5\" value=\"m\" /> Marktplanet<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_6\" value=\"x\" /> Allianzplanet<br/>
					<input type=\"checkbox\" name=\"search_code[]\" id=\"code_7\" value=\"e\" /> Leerer Raum
				</td></tr>";
			echo "<tr>
				<th>Besitzer-ID:</th>
				<td><input type=\"text\" name=\"search_user_id\" value=\"\" size=\"5\" maxlength=\"10\" /></td>";
			echo "<tr>
				<th>Besitzer:</th>
				<td>".searchFieldTextOptions('user_nick')." <input type=\"text\" name=\"search_user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\"  />&nbsp;";
				echo "</td></tr>";
			echo "<tr>
				<td style=\"height:2px\" colspan=\"2\"></td></tr>";
			echo "<tr>
				<th>Hauptplanet:</th>
				<td><input type=\"radio\" name=\"search_user_main\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
				<input type=\"radio\" name=\"search_user_main\" value=\"0\" /> Nein &nbsp;
				<input type=\"radio\" name=\"search_user_main\" value=\"1\" /> Ja</td>";
			echo "<tr>
				<th>Tr&uuml;mmerfeld:</th>
				<td><input type=\"radio\" name=\"search_debris\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
				<input type=\"radio\" name=\"search_debris\" value=\"0\" /> Nein &nbsp;
				<input type=\"radio\" name=\"search_debris\" value=\"1\"  /> Ja </td>";
			echo "<tr><th>Bemerkungen:</th>
				<td><input type=\"radio\" name=\"search_desc\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
				<input type=\"radio\" name=\"search_desc\" value=\"0\" /> Keine &nbsp;
				<input type=\"radio\" name=\"search_desc\" value=\"1\"  /> Vorhanden</td></tr>";
			echo "</table>";
			echo "<br/>
			<select name=\"search_limit\">";
			for ($x=100;$x<=2000;$x+=100)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> Datensätze sortiert nach <select name=\"search_order\">";
				foreach ($order_array as $k=>$v)
				{
					echo "<option value=\"".$k."\">".$v."</option>";
				}
				echo "
			</select> <input type=\"submit\" name=\"search_submit\" value=\"Suchen\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(id) FROM planets;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";
			
			echo "<script type=\"text/javascript\">document.forms['dbsearch'].elements[2].focus();</script>";
		}
	}
?>

