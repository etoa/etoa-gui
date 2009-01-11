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
		<option value=\"?t=".time()."\">Normale Galaxieansicht</option>
		<option value=\"?type=populated&t=".time()."\">Bev&ouml;lkerte Systeme</option>
		
		</select><br/><br/>";
		echo "<img src=\"../misc/map.image.php\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/>";		
		
	}

	//
	// Beistzerprüfung
	//
	elseif ($sub=="galaxycheck")
	{	
		echo "<h1>Entitäten pr&uuml;fen</h1>";

		$res=dbquery("SELECT id,code FROM entities;");
		if (mysql_num_rows($res)>0)
		{
			$errcnt = 0;	
			echo "Entitäten werden auf Integrität geprüft...<br/>";
			while ($arr=mysql_fetch_assoc($res))
			{
				switch ($arr['code'])
				{
					case 's':
						$eres = dbquery("
						SELECT
							id
						FROM
							stars
						WHERE id=".$arr['id'].";");						
						if (mysql_num_rows($eres)==0)
						{
							echo "Fehlender Detaildatensatz bei Entität ".$arr['id']." (Stern)<br/>";							
							$errcnt++;
						}
						break;
					case 'p':
						$eres = dbquery("
						SELECT
							id
						FROM
							planets
						WHERE id=".$arr['id'].";");						
						if (mysql_num_rows($eres)==0)
						{
							echo "Fehlender Detaildatensatz bei Entität ".$arr['id']." (Planet)<br/>";							
							$errcnt++;
						}
						break;
					case 'a':
						$eres = dbquery("
						SELECT
							id
						FROM
							asteroids
						WHERE id=".$arr['id'].";");						
						if (mysql_num_rows($eres)==0)
						{
							echo "Fehlender Detaildatensatz bei Entität ".$arr['id']." (Asteroidenfeld)<br/>";							
							$errcnt++;
						}
						break;
					case 'n':
						$eres = dbquery("
						SELECT
							id
						FROM
							nebulas
						WHERE id=".$arr['id'].";");						
						if (mysql_num_rows($eres)==0)
						{
							echo "Fehlender Detaildatensatz bei Entität ".$arr['id']." (Nebel)<br/>";							
							$errcnt++;
						}
						break;
					case 'w':
						$eres = dbquery("
						SELECT
							id
						FROM
							wormholes
						WHERE id=".$arr['id'].";");						
						if (mysql_num_rows($eres)==0)
						{
							echo "Fehlender Detaildatensatz bei Entität ".$arr['id']." (Wurmloch)<br/>";							
							$errcnt++;
						}
						break;
					case 'e':
						$eres = dbquery("
						SELECT
							id
						FROM
							space
						WHERE id=".$arr['id'].";");						
						if (mysql_num_rows($eres)==0)
						{
							echo "Fehlender Detaildatensatz bei Entität ".$arr['id']." (Leerer Raum)<br/>";							
							$errcnt++;
						}
						break;
					default:
						echo "Achtung! Entität ".$arr['id']." hat einen unbekannten Code (".$arr['code'].")<br/>";							
						$errcnt++;					
				}
			}
			if ($errcnt>0)
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!<br/>";
			}
			else
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Keine Fehler gefunden!<br/>";
			}
		}
		else
		{
			echo "Keine Entitäten vorhanden!<br/>";
		}
		
		
		$res=dbquery("
		SELECT 
			id
		FROM
			stars;");
		if (mysql_num_rows($res)>0)
		{
			$errcnt = 0;	
			echo "Sterne werden auf Integrität geprüft...<br/>";
			while ($arr=mysql_fetch_assoc($res))
			{
				$eres=dbquery("
				SELECT 
					code 
				FROM 
					entities
				WHERE
					id=".$arr['id'].";");
				if (mysql_num_rows($eres)==0)
				{
					echo "Fehlender Entitätsdatemsatz bei Stern ".$arr['id']."<br/>";							
					$errcnt++;
				}
				else
				{
					$earr = mysql_fetch_array($eres);
					if($earr['code']!='s')
					{
						echo "Falscher Code (".$earr['code'].") bei Stern ".$arr['id']."<br/>";							
						$errcnt++;
					}					
				}
			}
			if ($errcnt>0)
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!<br/>";
			}
			else
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Keine Fehler gefunden!<br/>";
			}
		}
		else
		{
			echo "Keine Sterne vorhanden!<br/>";
		}		

		$res=dbquery("
		SELECT 
			id
		FROM
			wormholes;");
		if (mysql_num_rows($res)>0)
		{
			$errcnt = 0;	
			echo "Wurmlöcher werden auf Integrität geprüft...<br/>";
			while ($arr=mysql_fetch_assoc($res))
			{
				$eres=dbquery("
				SELECT 
					code 
				FROM 
					entities
				WHERE
					id=".$arr['id'].";");
				if (mysql_num_rows($eres)==0)
				{
					echo "Fehlender Entitätsdatemsatz bei Wurmloch ".$arr['id']."<br/>";							
					$errcnt++;
				}
				else
				{
					$earr = mysql_fetch_array($eres);
					if($earr['code']!='w')
					{
						echo "Falscher Code (".$earr['code'].") bei Wurmloch ".$arr['id']."<br/>";							
						$errcnt++;
					}					
				}
			}
			if ($errcnt>0)
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!<br/>";
			}
			else
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Keine Fehler gefunden!<br/>";
			}
		}
		else
		{
			echo "Keine Wurmlöcher vorhanden!<br/>";
		}	

		$res=dbquery("
		SELECT 
			id
		FROM
			space;");
		if (mysql_num_rows($res)>0)
		{
			$errcnt = 0;	
			echo "Leere Räume werden auf Integrität geprüft...<br/>";
			while ($arr=mysql_fetch_assoc($res))
			{
				$eres=dbquery("
				SELECT 
					code 
				FROM 
					entities
				WHERE
					id=".$arr['id'].";");
				if (mysql_num_rows($eres)==0)
				{
					echo "Fehlender Entitätsdatemsatz bei leerem Raum ".$arr['id']."<br/>";							
					$errcnt++;
				}
				else
				{
					$earr = mysql_fetch_array($eres);
					if($earr['code']!='e')
					{
						echo "Falscher Code (".$earr['code'].") bei leerem Raum ".$arr['id'].".<br/>";				
						$errcnt++;
					}					
				}
			}
			if ($errcnt>0)
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!<br/>";
			}
			else
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Keine Fehler gefunden!<br/>";
			}
		}
		else
		{
			echo "Keine leeren Räume vorhanden!<br/>";
		}
		

		$res=dbquery("SELECT id FROM cells;");
		if (mysql_num_rows($res)>0)
		{
			$errcnt = 0;	
			echo "<br/>Zellen werden auf Integrität geprüft...<br/>";
			while ($arr=mysql_fetch_assoc($res))
			{
				$eres = dbquery("
					SELECT
						id
					FROM
						entities
					WHERE cell_id=".$arr['id'].";");						
				if (mysql_num_rows($eres)==0)
				{
					$earr = mysql_fetch_assoc($eres);
					echo "Fehlende Entität ".$earr['id']." bei Zelle ".$arr['id']."<br/>";							
					$errcnt++;
				}
			}
			if ($errcnt>0)
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Es wurden <b>$errcnt</b> Fehler gefunden!<br/>";
			}
			else
			{
				echo mysql_num_rows($res)." Datensätze geprüft. Keine Fehler gefunden!<br/>";
			}
		}
		

	}


	
	//
	// Beistzerprüfung
	//
	elseif ($sub=="planetcheck")
	{
		echo "<h1>Planeten pr&uuml;fen</h1>";

		echo "Prüfen ob zu allen Planeten mit einer User-Id auch ein User existiert...<br/>";
		$user=array();
		$res=dbquery("SELECT user_id,user_nick FROM users;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				$user[$arr['user_id']]=$arr['user_nick'];
			}
		}
		$res=dbquery("
		SELECT 
			id,
			planet_user_id,
			planet_user_main 
		FROM 
			planets 
		WHERE 
			planet_user_id>0
		;");
		$cnt=0;
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\"><tr><th>Name</th><th>Id</th><th>User-Id</th><th>Id</th><th>Aktionen</th></tr>";
			while ($arr=mysql_fetch_array($res))
			{
				if (count($user[$arr['planet_user_id']])==0)
				{
					$cnt++;
					echo "<tr><td>".$arr['planet_name']."</td><td>".$arr['id']."</td><td>".$arr['planet_user_id']."</td>
					<td><a href=\"?page=$page&sub=edit&amp;id=".$arr['id']."\">Bearbeiten</a></td></tr>";
				}
			}
			if ($cnt==0)
			{
				echo "<tr><td colspan=\"5\">Keine Fehler gefunden!</td></th>";
			}			
			echo "</table>";
		}
		else
		{
			echo "<i>Keine bewohnten Planeten gefunden!</i>";
		}

		
		echo "<br/><br/>Prüfe auf Hauptplaneten ohne User...<br/>";
		$res=dbquery("
		SELECT
			planet_name,
			id
		FROM
			planets
		WHERE
			planet_user_main=1
			AND planet_user_id=0
		");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\"><tr><th>Name</th><th>Id</th><th>Aktionen</th></tr>";
			while ($arr=mysql_fetch_array($res))
			{
				if (count($user[$arr['planet_user_id']])==0)
				{
					echo "<tr><td>".$arr['planet_name']."</td><td>".$arr['id']."</td><td><a href=\"?page=$page&sub=edit&amp;id=".$arr['id']."\">Bearbeiten</a></td></tr>";
				}
			}
			echo "</table>";			
		}
		else
		{
			echo "<i>Keine Fehler gefunden!</i>";
		}
		
	}

	elseif ($sub=="planet_types")
	{
		advanced_form("planet_types");
	}
	elseif ($sub=="sol_types")
	{
		advanced_form("sol_types");
	}
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
					echo "<tr><th class=\"tbltitle\">Zellentyp</th><td class=\"tbldata\">Asteroidenfeld</td></tr>";
					echo "<tr><th class=\"tbltitle\">Ressourcen</th><td class=\"tbldata\"><input type=\"text\" name=\"cell_asteroid_ress\" value=\"".$arr['cell_asteroid_ress']."\" /></td></tr>";
					echo "</table><br/><input type=\"submit\" name=\"remove_content\" value=\"Zelle in leeren Raum umwandeln\" /><br/>";
				}

				//
				// Nebelfeld bearbeiten
				//
				elseif ($arr['cell_nebula']>0)
				{
					echo "<input type=\"hidden\" name=\"cell_nebula\" value=\"1\" />";
					echo "<table class=\"tb\">";
					echo "<tr><th class=\"tbltitle\">Zellentyp</th><td class=\"tbldata\">Nebelfeld</td></tr>";
					echo "<tr><th class=\"tbltitle\">Ressourcen</th><td class=\"tbldata\"><input type=\"text\" name=\"cell_nebula_ress\" value=\"".$arr['cell_nebula_ress']."\" /></td></tr>";
					echo "</table><br/><input type=\"submit\" name=\"remove_content\" value=\"Zelle in leeren Raum umwandeln\" /><br/>";
				}

				//
				// Wurmloch bearbeiten
				//
				elseif ($arr['cell_wormhole_id']>0)
				{
					echo "<table class=\"tb\">";
					echo "<tr><th class=\"tbltitle\">Wurmloch nach</th><td class=\"tbldata\"><select name=\"cell_wormhole_id\">";
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
					echo "<tr><th class=\"tbltitle\">Letzte &Auml;nderung</th><td class=\"tbldata\">";
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
					echo "<tr><th class=\"tbltitle\">Zellentyp</th><td class=\"tbldata\">Sonnensystem</td></tr>";
					echo "<tr><th class=\"tbltitle\">Stern-Typ</th><td class=\"tbldata\"><select name=\"cell_solsys_solsys_sol_type\">";
					foreach ($solsys_types as $id=>$val)
					{
						echo "<option value=\"$id\"";
						if ($id==$arr['cell_solsys_solsys_sol_type']) echo " selected=\"selected\"";
						echo ">$val</option>";
					}
					echo "</select></td></tr>";
					echo "<tr><th class=\"tbltitle\">Name des Sterns</th><td class=\"tbldata\"><input type=\"text\" name=\"cell_solsys_name\" value=\"".$arr['cell_solsys_name']."\" /></td></tr>";
					echo "<tr><th>Anzahl Planeten</th><td><select name=\"cell_solsys_num_planets\">";
					for ($x=$conf['num_planets']['p1'];$x<=$conf['num_planets']['p2'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($x==$arr['cell_solsys_num_planets']) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select></td></tr>";
					echo "<tr><th class=\"tbltitle\">Planeten</th><td class=\"tbldata\">";
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
							echo "<tr><th class=\"tbltitle\">Ressourcen</th><td class=\"tbldata\"><input type=\"text\" name=\"cell_nebula_ress\" value=\"".$arr['cell_nebula_ress']."\" /></td></tr>";
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
	else
	{
		echo "<h1>Entitäten</h1>";
		
		$order_array=array();		
		$order_array['id']="Planeten-ID";
		$order_array['planet_name']="Planetenname";
		$order_array['type_name']="Planetentyp";
		$order_array['user_nick']="Besitzer-Name";
		
		if ($_POST['search_submit']!="" || $_POST['search_resubmit']!="" || $_GET['action']=="searchresults" || $_GET['query']!="")
		{
			$search_text = array();
			$query_save = array();
			
			if ($_GET['query']!="")
			{
				$qs = searchQueryDecode($_GET['query']);
				foreach($qs as $k=>$v)
				{
					$_POST[$k]=$v;
				}
				$_SESSION['search']['planets']['query']=null;
			}
			
			if (isset($_POST['search_submit']))
			{
				$_SESSION['search']['planets']['query']=null;
			}
				
			if (isset($_SESSION['search']['planets']['query']) && count($_SESSION['search']['planets']['query'])>0)		
			{
				foreach ($_SESSION['search']['planets']['query'] as $qs)
				{
					$t = explode(":",$qs);
					if (!isset($_POST[$t[0]]))
					{
						$_POST[$t[0]]=$t[2];
						$_POST['qmode'][$t[0]]=$t[1];
					}
				}	
			}
			
				// Define tables
				$tables = "entities e";
				$joins = " INNER JOIN cells c ON c.id=e.cell_id ";
				
				$selects = "";
				//INNER JOIN planet_types ON type_id = planet_type_id ";

				// Set default order and limit values
				$_POST['limit'] = ($_POST['limit']=="") ? 100 : $_POST['limit'];
				$oak = array_keys($order_array);
				$_POST['order'] = ($_POST['order']=="") ? $oak[0] : $_POST['order'];

				// Build query
				if ($_POST['id']!="")
				{
					$sql.= " AND e.id=".intval($_POST['id']);
					$search_text['Entitäts-ID'] = intval($_POST['id']);
					$query_save[] = "e.id:=:".intval($_POST['id']);
				}
				if ($_POST['code']!="")
				{
					$sql.= " AND (";
					$i=0;
					foreach ($_POST['code'] as $code)
					{
						if ($i>0)			
							$sql.=" OR";
						$sql .= " e.code='".$code."'";
						$i++;
					}
					$sql.= ") ";
				}
				if ($_POST['cell_cx']!="")
				{
					$sql.= " AND c.cx=".$_POST['cell_cx'];
					$search_text['Zelle X'] = $_POST['cell_cx'];
					$query_save[] = "c.cx:=:".$_POST['cell_cx'];
				}
				if ($_POST['cell_cy']!="")
				{
					$sql.= " AND c.cy=".$_POST['cell_cy'];
					$search_text['Zelle Y'] = $_POST['cell_cy'];
					$query_save[] = "c.cy:=:".$_POST['cell_cy'];
				}
				if ($_POST['cell_sx']!="")
				{
					$sql.= " AND c.sx=".$_POST['cell_sx'];
					$search_text['Sektor X'] = $_POST['cell_sx'];
					$query_save[] = "c.sx:=:".$_POST['cell_sx'];
				}
				if ($_POST['cell_sy']!="")
				{
					$sql.= " AND c.sy=".$_POST['cell_sy'];
					$search_text['Sektor Y'] = $_POST['cell_sy'];
					$query_save[] = "c.sy:=:".$_POST['cell_sy'];
				}
				if ($_POST['planet_solsys_pos']!="")
				{
					$sql.= " AND e.pos=".$_POST['planet_solsys_pos'];
					$search_text['Position'] = $_POST['planet_solsys_pos'];
					$query_save[] = "e.pos:=:".$_POST['planet_solsys_pos'];
				}


				if ($_POST['planet_name']!="")
				{
					$joins.= " INNER JOIN planets p ON p.id=e.id ";
					$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
					
					$sql.= " AND p.planet_name ".searchFielsOptionsSql($_POST['planet_name'],$_POST['qmode']['planet_name']);
					$search_text['Planetenname'] = searchFieldOptionsName($_POST['qmode']['planet_name'])." ".$_POST['planet_name'];
					$query_save[] = "p.planet_name:".$_POST['qmode']['planet_name'].":".$_POST['planet_name'];
				}
				if ($_POST['planet_user_id']!="")
				{
					if (!stristr($joins,"planets p"))
					{
						$joins.= " INNER JOIN planets p ON p.id=e.id ";	
						$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
					}
					$sql.= " AND p.planet_user_id=".intval($_POST['planet_user_id']);
					$search_text['Besitzer-ID'] = intval($_POST['planet_user_id']);
					$query_save[] = "p.planet_user_id:=:".intval($_POST['planet_user_id']);
				}
				if (isset($_POST['planet_user_main']) && $_POST['planet_user_main']<2 )
				{
					if (!stristr($joins,"planets p"))
					{
						$joins.= " INNER JOIN planets p ON p.id=e.id ";	
						$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
					}
					$sql.= " AND p.planet_user_main LIKE '".$_POST['planet_user_main']."'";
					$search_text['Hauptplanet'] = $_POST['planet_user_main']==1 ? "Ja" : "Nein";
					$query_save[] = "p.planet_user_main:=:".$_POST['planet_user_main'];
				}
				if (isset($_POST['planet_wf']) && $_POST['planet_wf']<2)
				{
					if (!stristr($joins,"planets p"))
					{
						$joins.= " INNER JOIN planets p ON p.id=e.id ";	
						$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
					}
					$sql.= " AND (p.planet_wf_metal>0 OR p.planet_wf_crystal>0 OR p.planet_wf_plastic>0)";
					$search_text['Trümmerfeld'] = $_POST['planet_wf']==1 ? "Ja" : "Nein";
					$query_save[] = "planet_wf:=:".$_POST['planet_wf'];
				}

				if ($_POST['user_nick']!="")
				{
					if (!stristr($joins,"planets p"))
					{
						$joins.= " INNER JOIN planets p ON p.id=e.id ";	
						$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
					}

					$sql.= " AND users.user_nick ".searchFielsOptionsSql($_POST['user_nick'],$_POST['qmode']['user_nick']);
					$joins.= " INNER JOIN users ON p.planet_user_id=user_id ";
					$search_text['Besitzer-Nick'] = searchFieldOptionsName($_POST['qmode']['user_nick'])." ".$_POST['user_nick'];
					$query_save[] = "user_nick:".$_POST['qmode']['user_nick'].":".$_POST['user_nick'];
				}
				if (isset($_POST['planet_desc']) && $_POST['planet_desc']<2)
				{
					if (!stristr($joins,"planets p"))
					{
						$joins.= " INNER JOIN planets p ON p.id=e.id ";	
						$selects = ",p.planet_user_id,p.planet_type_id,p.planet_name";
					}

					if ($_POST['planet_desc']==1)
					{
						$sql.= " AND p.planet_desc!='' ";
						$search_text['Beschreibung'] = "Vorhanden";
					}
					else
					{
						$sql.= " AND p.planet_desc='' ";
						$search_text['Beschreibung'] = "Nicht vorhanden";
					}
					$query_save[] = "planet_desc:=:".$_POST['planet_desc'];
				}

				$sqlstart = "SELECT   
					SQL_CALC_FOUND_ROWS e.id,
					e.id,
					e.code, 
					e.pos,
					c.sx,c.sy,c.cx,c.cy ".
					$selects
					."
				FROM ".$tables." ".$joins." WHERE 1 ";
				$sqlend = " ORDER BY ".$_POST['order']." LIMIT ".$_POST['limit'].";";
				$sql = $sqlstart.$sql.$sqlend;
				
				$query_save[]="limit::".$_POST['limit'];
				$query_save[]="order::".$_POST['order'];
				
				$_SESSION['search']['planets']['query'] = $query_save;
			

			$res = dbquery($sql);
			
			$ares = dbquery("SELECT FOUND_ROWS()");			
			$aarr = mysql_fetch_row($ares);
			$nr =$aarr[0];

			echo "<h2>Suchresultate</h2>";
			echo "<form acton=\"?page=".$page."\" method=\"post\">";
			$n = count($search_text);
			if ($n>0)
			{
				echo "<b>Abfrage:</b> ";
				$cnt=0;
				foreach($search_text as $stk => $stv)
				{
					echo "".$stk." ".$stv;
					if ($cnt<$n-1)
						echo ", ";
					$cnt++;
				}
				echo "<br/>";				
			}
			echo "<b>Ergebnis:</b> ".$nr." Datens&auml;tze, Limit: ".$_POST['limit'].", Sortierung: ".$order_array[$_POST['order']]."<br/>";
			echo "<b>Optionen:</b> <select name=\"limit\">";
			for ($x=100;$x<=2000;$x+=100)
			{
				echo "<option value=\"$x\"";
				if ($_POST['limit']==$x)
					echo " selected=\"selected\"";
				echo ">$x</option>";
			}
			echo "</select> Datensätze sortiert nach <select name=\"order\">";
			foreach ($order_array as $k=>$v)
			{
				echo "<option value=\"".$k."\"";
				if ($_POST['order']==$k)
					echo " selected=\"selected\"";
				
				echo ">".$v."</option>";
			}
			echo "</select> <input type=\"submit\" value=\"Anzeigen\" name=\"search_resubmit\" /></form><br/>";
			


			if ($nr > 0)
			{  
				if ($nr > 20)
				{
					echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";
				}
				
				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" valign=\"top\" style=\"width:40px;\">ID</td>";
				echo "<td class=\"tbltitle\" valign=\"top\" style=\"width:90px;\">Koordinaten</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Entitätstyp</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Name</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Besitzer</td>";
				echo "<td class=\"tbltitle\" valign=\"top\" style=\"width:150px;\">Typ</td>";
				echo "<td style=\"width:20px;\">&nbsp;</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					$ent = Entity::createFactory($arr['code'],$arr['id']);
					
					echo "<tr>";
					echo "<td class=\"tbldata\">
						".$arr['id']."</td>";
					echo "<td class=\"tbldata\">
					<a href=\"?page=$page&sub=edit&id=".$arr['id']."\">
						".$arr['sx']."/".$arr['sy']." : ".$arr['cx']."/".$arr['cy']." : ".$arr['pos']."
					</a></td>";
					echo "<td class=\"tbldata\" style=\"color:".Entity::$entityColors[$arr['code']]."\">";
					echo $ent->entityCodeString();
					echo "</td>";
					echo "<td class=\"tbldata\">".$ent->name()."</td>";
					echo "<td class=\"tbldata\">";
					if ($ent->ownerId()>0)
					{
						echo "<a href=\"?page=user&amp;sub=edit&amp;user_id=".$ent->ownerId()."\" title=\"Spieler bearbeiten\">
							".$ent->owner()."
						</a>";
					}					
					echo "
					</td>";
					echo "<td class=\"tbldata\">";
					echo $ent->type();
					echo "</td>";
  				echo "<td class=\"tbldata\">".edit_button("?page=$page&sub=edit&id=".$arr['id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
			}
			else
			{
				$_SESSION['planets']['query']=Null;
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" />";
			}
		}

		//
		// Details bearbeiten
		//

		elseif ($_GET['sub']=="edit")
		{
			$eres = dbquery("
			SELECT 
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
				AND e.id=".$_GET['id'].";");
			if (mysql_num_rows($eres)>0)
			{
				$earr = mysql_fetch_array($eres);
				
				echo "<h2 style=\"color:".Entity::$entityColors[$earr['code']]."\">Entität ".$earr['id']." (".$earr['sx']."/".$earr['sy']." : ".$earr['cx']."/".$earr['cy']." : ".$earr['pos']."";
				if ($earr['code']=='p')
				{		
					echo ", Planet) bearbeiten</h2>";
								
					if ($_POST['save']!="")
					{
						//Daten Speichern
						dbquery("
						UPDATE
							planets
						SET
              planet_name='".$_POST['planet_name']."',
              planet_user_main=".$_POST['planet_user_main'].",
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
							id='".$_GET['id']."';");
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
							$pl = new Planet($_GET['id']);
							$pl->chown($_POST['planet_user_id']);
		
							//Log Schreiben
							add_log(8,$_SESSION[SESSION_NAME]['user_nick']." wechselt den Besitzer vom Planeten: [URL=?page=galaxy&sub=edit&id=".$_GET['id']."][B]".$_GET['id']."[/B][/URL]\nAlter Besitzer: [URL=?page=user&sub=edit&user_id=".$_POST['planet_user_id_old']."][B]".$_POST['planet_user_id_old']."[/B][/URL]\nNeuer Besitzer: [URL=?page=user&sub=edit&user_id=".$_POST['planet_user_id']."][B]".$_POST['planet_user_id']."[/B][/URL]",time());
		
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
						id=".$_GET['id'].";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$_GET['id']."\" method=\"post\" id=\"editform\">";
					echo "<table class=\"tbl\">";
					
				
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Name</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"".$arr['planet_name']."\" size=\"20\" maxlength=\"250\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Typ</td>
					<td class=\"tbldata\">
					<select name=\"planet_type_id\">";
					$tres = dbquery("SELECT * FROM planet_types ORDER BY type_name;");
					while ($tarr = mysql_fetch_array($tres))
					{
						echo "<option value=\"".$tarr['type_id']."\"";
						if ($arr['planet_type_id']==$tarr['type_id']) echo " selected=\"selected\"";
						echo ">".$tarr['type_name']."</option>\n";
					}
					echo "</select></td></tr>";
					
					//Listet alle User der Spiels auf
					$users = get_user_names();
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Besitzer</td><td class=\"tbldata\"><select name=\"planet_user_id\">";
					echo "<option value=\"0\">(niemand)</option>";
					foreach ($users as $uid=>$udata)
					{
						echo "<option value=\"$uid\"";
						if ($arr['planet_user_id']==$uid) {echo " selected=\"selected\"";$planet_user_id=$uid;}
						echo ">".$udata['nick']."</option>";
					}
					echo "</select> ";
					echo "<input tabindex=\"29\" type=\"button\" name=\"change_owner\" value=\"Planet &uuml;bergeben\" class=\"button\" onclick=\"if( confirm('Dieser Planet soll einem neuen Besitzer geh&ouml;ren. Alle Schiffs- und Verteidigungsdaten vom alten Besitzer werden komplett gel&ouml;scht.')) document.getElementById('editform').submit()\"/>&nbsp;";
					
					echo "</td>";
					//übergibt den alten besitzer mit
					echo "<td class=\"tbltitle\" valign=\"top\">Allianz</td><td class=\"tbldata\">";
					echo "<input type=\"hidden\" name=\"planet_user_id_old\" value=\"".$arr['planet_user_id']."\">";
					if ($users[$planet_user_id]['alliance_id']>0)
					{
						$aarr = mysql_fetch_array(dbquery("SELECT alliance_tag FROM alliances WHERE alliance_id='".$users[$planet_user_id]['alliance_id']."';"));
						echo $aarr['alliance_tag'];
					}
					echo "</td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Felder / Extra-Felder</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_fields\" value=\"".$arr['planet_fields']."\" size=\"10\" maxlength=\"250\" />
					<input type=\"text\" name=\"planet_fields_extra\" value=\"".$arr['planet_fields_extra']."\" size=\"10\" maxlength=\"250\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Felder benutzt</td>
					<td class=\"tbldata\">".nf($arr['planet_fields_used'])."</td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Temp min</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_temp_from\" value=\"".$arr['planet_temp_from']."\" size=\"20\" maxlength=\"250\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Temp max</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_temp_to\" value=\"".$arr['planet_temp_to']."\" size=\"20\" maxlength=\"250\" /></td></tr>";
		
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Bild</td>
					<td class=\"tbldata\">
					<img src=\"".IMAGE_PATH."/planets/planet".$arr['planet_image']."_small.".IMAGE_EXT."\" style=\"float:left;\" />
					<select name=\"planet_image\">";
					$tres = dbquery("SELECT * FROM planet_types ORDER BY type_name;");
					
					while ($tarr = mysql_fetch_array($tres))
					{
						for ($x=1;$x<=$cfg->value('num_planet_images');$x++)
						{
							echo "<option value=\"".$tarr['type_id']."_".$x."\"";
							if ($arr['planet_image']==$tarr['type_id']."_".$x) 
								echo " selected=\"selected\"";
							echo ">".$tarr['type_name']." $x</option>\n";
						}
					}
					echo "</select>
					
					</td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Hauptplanet</td>
					<td class=\"tbldata\">
					<input type=\"radio\" name=\"planet_user_main\" ".($arr['planet_user_main']==1 ? " checked=\"checked\"" : "")." value=\"1\"/> Ja
					<input type=\"radio\" name=\"planet_user_main\" ".($arr['planet_user_main']==0 ? " checked=\"checked\"" : "")." value=\"0\"/> Nein
					 </td></tr>";
					
					echo "<td class=\"tbldata\" style=\"height:2px;\" colspan=\"4\"></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Produktion ".RES_METAL."</td>
					<td class=\"tbldata\">".nf($arr['planet_prod_metal'])."</td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Speicher ".RES_METAL.":</td>
					<td class=\"tbldata\">".nf($arr['planet_store_metal'])."</td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Produktion ".RES_CRYSTAL."</td>
					<td class=\"tbldata\">".nf($arr['planet_prod_crystal'])."</td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Speicher ".RES_CRYSTAL.":</td>
					<td class=\"tbldata\">".nf($arr['planet_store_crystal'])."</td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Produktion ".RES_PLASTIC."</td>
					<td class=\"tbldata\">".nf($arr['planet_prod_plastic'])."</td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Speicher ".RES_PLASTIC.":</td>
					<td class=\"tbldata\">".nf($arr['planet_store_plastic'])."</td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Produktion ".RES_FUEL."</td>
					<td class=\"tbldata\">".nf($arr['planet_prod_fuel'])."</td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Speicher ".RES_FUEL.":</td>
					<td class=\"tbldata\">".nf($arr['planet_store_fuel'])."</td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Produktion ".RES_FOOD."</td>
					<td class=\"tbldata\">".nf($arr['planet_prod_food'])."</td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Speicher ".RES_FOOD.":</td>
					<td class=\"tbldata\">".nf($arr['planet_store_food'])."</td></tr>";
		
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Verbrauch Energie:</td>
					<td class=\"tbldata\">".nf($arr['planet_use_power'])."</td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Produktion Energie:</td>
					<td class=\"tbldata\">".nf($arr['planet_prod_power'])."</td></tr>";
		
					echo "<td class=\"tbldata\" style=\"height:2px;\" colspan=\"4\"></td></tr>";
		
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Titan</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_res_metal\" value=\"".intval($arr['planet_res_metal'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Silizium</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_res_crystal\" value=\"".intval($arr['planet_res_crystal'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">PVC</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_res_plastic\" value=\"".intval($arr['planet_res_plastic'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Tritium</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_res_fuel\" value=\"".intval($arr['planet_res_fuel'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Nahrung</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_res_food\" value=\"".intval($arr['planet_res_food'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Bevölkerung</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_people\" value=\"".intval($arr['planet_people'])."\" size=\"12\" maxlength=\"20\" /><br/>
					+/-: <input type=\"text\" name=\"planet_people_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Wohnraum</td>
					<td class=\"tbldata\">".nf($arr['planet_people_place'])."</td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Bevölkerungswachstum</td>
					<td class=\"tbldata\">".nf($arr['planet_prod_people'])."</td></tr>";
		
					echo "<td class=\"tbldata\" style=\"height:2px;\" colspan=\"4\"></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Tr&uuml;mmerfeld Titan</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_wf_metal\" value=\"".$arr['planet_wf_metal']."\" size=\"20\" maxlength=\"250\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Tr&uuml;mmerfeld Silizium</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_wf_crystal\" value=\"".$arr['planet_wf_crystal']."\" size=\"20\" maxlength=\"250\" /></td></tr>";
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Tr&uuml;mmerfeld PVC</td>
					<td class=\"tbldata\"><input type=\"text\" name=\"planet_wf_plastic\" value=\"".$arr['planet_wf_plastic']."\" size=\"20\" maxlength=\"250\" /></td>";
					echo "<td class=\"tbltitle\" valign=\"top\">Updated</td>
					<td class=\"tbldata\">".date("d.m.Y H:i",$arr['planet_last_updated'])."</td></tr>";
		
					
					echo "<tr><td class=\"tbltitle\" valign=\"top\">Beschreibung</td>
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
							id='".$_GET['id']."';");
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
						id=".$_GET['id'].";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$_GET['id']."\" method=\"post\" id=\"editform\">";
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
							id='".$_GET['id']."';");
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
						id=".$_GET['id'].";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$_GET['id']."\" method=\"post\" id=\"editform\">";
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
							id='".$_GET['id']."';");
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
						id=".$_GET['id'].";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$_GET['id']."\" method=\"post\" id=\"editform\">";
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
						id=".$_GET['id'].";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$_GET['id']."\" method=\"post\" id=\"editform\">";
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
						id=".$_GET['id'].";");
					$arr = mysql_fetch_array($res);
					
					echo "<form action=\"?page=$page&sub=edit&id=".$_GET['id']."\" method=\"post\" id=\"editform\">";
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
		}

		//
		// Suchmaske
		//

		else
		{
			echo "<h2>Suchmaske</h2>";
			echo "<form action=\"?page=$page\" method=\"post\" name=\"advancedsearch\"  autocomplete=\"off\">";
			echo "<table class=\"tbl\" style=\"width:550px;margin:0px\">";
			echo "<tr>
				<td class=\"tbltitle\">ID:</td>
				<td class=\"tbldata\"><input type=\"text\" name=\"id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr>
				<td class=\"tbltitle\">Koordinaten:</td>
				<td class=\"tbldata\"><select name=\"cell_sx\">";
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
			echo "</select> : <select name=\"planet_solsys_pos\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_planets']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";
			echo "<tr>
				<td class=\"tbltitle\" style=\"width:160px\">Entitätstyp:</td>
				<td class=\"tbldata\">
					<input type=\"checkbox\" name=\"code[]\" value=\"s\" checked=\"checked\" /> Stern
					<br/><input type=\"checkbox\" name=\"code[]\" value=\"p\" checked=\"checked\" /> Planet
					<br/><input type=\"checkbox\" name=\"code[]\" value=\"n\" checked=\"checked\" /> Nebel
					<br/><input type=\"checkbox\" name=\"code[]\" value=\"a\" checked=\"checked\" /> Asteroidenfeld
					<br/><input type=\"checkbox\" name=\"code[]\" value=\"w\" checked=\"checked\" /> Wurmloch
					<br/><input type=\"checkbox\" name=\"code[]\" value=\"e\" checked=\"checked\" /> Leerer Raum
				</td></tr>";

			echo "<tr>
				<td class=\"tbltitle\" style=\"height:2px\" colspan=\"2\"></td></tr>";

			echo "<tr>
				<td class=\"tbltitle\" style=\"width:160px\">Name:</td>
				<td class=\"tbldata\">".searchFieldTextOptions('planet_name')." <input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr>
				<td class=\"tbltitle\">Besitzer-ID:</td>
				<td class=\"tbldata\"><input type=\"text\" name=\"planet_user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
			echo "<tr>
				<td class=\"tbltitle\">Besitzer:</td>
				<td class=\"tbldata\">".searchFieldTextOptions('user_nick')." <input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\"  />&nbsp;";
				echo "</td></tr>";

			echo "<tr>
				<td class=\"tbltitle\">Hauptplanet:</td>
				<td class=\"tbldata\"><input type=\"radio\" name=\"planet_user_main\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
				<input type=\"radio\" name=\"planet_user_main\" value=\"0\" /> Nein &nbsp;
				<input type=\"radio\" name=\"planet_user_main\" value=\"1\" /> Ja</td>";
			echo "<tr>
				<td class=\"tbltitle\">Tr&uuml;mmerfeld:</td>
				<td class=\"tbldata\"><input type=\"radio\" name=\"planet_wf\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
				<input type=\"radio\" name=\"planet_wf\" value=\"0\" /> Nein &nbsp;
				<input type=\"radio\" name=\"planet_wf\" value=\"1\"  /> Ja </td>";
			echo "<tr><td class=\"tbltitle\">Bemerkungen:</td>
				<td class=\"tbldata\"><input type=\"radio\" name=\"planet_desc\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
				<input type=\"radio\" name=\"planet_desc\" value=\"0\" /> Keine &nbsp;
				<input type=\"radio\" name=\"planet_desc\" value=\"1\"  /> Vorhanden</td></tr>";
			echo "</table>";
			echo "<br/>
			<select name=\"limit\">";
			for ($x=100;$x<=2000;$x+=100)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> Datensätze sortiert nach <select name=\"order\">";
				foreach ($order_array as $k=>$v)
				{
					echo "<option value=\"".$k."\">".$v."</option>";
				}
				echo "
			</select> <input type=\"submit\" class=\"button\" name=\"search_submit\" value=\"Suchen\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(id) FROM planets;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";
			
			echo "<script type=\"text/javascript\">document.forms['advancedsearch'].elements[1].focus();</script>";
		}
	}
?>

