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
	* Sends ships on their flights
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// BEGIN SKRIPT //

	if ($cp)
	{
		echo '<h1>Raumschiffhafen des Planeten '.$cp->name.'</h1>';
		echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);

		if (!$cu->isVerified)
		{
			iBoxStart("Funktion gesperrt");
			echo "Solange deine E-Mail Adresse nicht bestätigt ist, kannst du keine Flotten versenden!";
			iBoxEnd();
		}
		else
		{
			// Count number of mobile defense structures
			$ndarr = mysql_fetch_row(dbquery("
			SELECT
				COUNT(d.def_id)
			FROM
				defense d
			INNER JOIN
				obj_transforms t
				ON t.def_id=d.def_id
			INNER JOIN
				deflist l
				ON l.deflist_def_id=d.def_id
				AND l.deflist_user_id=".$cu->id."
				AND l.deflist_entity_id=".$cp->id."
				AND l.deflist_count > 0"));		
			$nsarr = mysql_fetch_row(dbquery("
			SELECT
				COUNT(d.ship_id)
			FROM
				ships d
			INNER JOIN
				obj_transforms t
				ON t.ship_id=d.ship_id
			INNER JOIN
				shiplist l
				ON l.shiplist_ship_id=d.ship_id
				AND l.shiplist_user_id=".$cu->id."
				AND l.shiplist_entity_id=".$cp->id."
				AND l.shiplist_count > 0"));
				
			$numMobile = $ndarr[0] + $nsarr[0];
		
			$mode = isset($_GET['mode']) && ($_GET['mode']!="") && ctype_alpha($_GET['mode']) ? $_GET['mode'] : 'launch';
			if ($numMobile > 0)
			{
				show_tab_menu("mode",array(
					"launch"=>"Flotten versenden",
					"transship"=>"Mobile Anlagen umladen",
				));
				echo "<br/>";
			}

			//
			// Launch fleet
			//
			if ($mode == "launch")
			{
		
				//
				// Kampfsperre prüfen
				//
				if ($cfg->get("battleban")!=0 && $cfg->param1("battleban_time")<=time() && $cfg->param2("battleban_time")>time())
				{
					iBoxStart("Kampfsperre");
					echo 'Es ist momentan nicht m&ouml;glich andere Spieler anzugreifen. Grund: '.text2html($cfg->param1("battleban")).'<br />Die Sperre dauert vom '.date("d.m.Y",$cfg->param1("battleban_time")).' um '.date("H:i",$cfg->param1("battleban_time")).' Uhr bis am '.date("d.m.Y",$cfg->param2("battleban_time"))." um ".date("H:i",$cfg->param2("battleban_time")).' Uhr!';
					iBoxEnd();
				}
			
				if (isset($_GET['target']) && intval($_GET['target'])>0)
				{
					$_SESSION['haven']['targetId']=intval($_GET['target']);
				}
				elseif (isset($_GET['cellTarget']) && intval($_GET['cellTarget'])>0)
				{
					$_SESSION['haven']['cellTargetId']=intval($_GET['cellTarget']);
				}

				// Fleet object
				$fleet = new FleetLaunch($cp,$cu);

				$fleet->checkHaven();

				// Set vars for xajax
				$_SESSION['haven'] = Null;
				$_SESSION['haven']['fleetObj']=serialize($fleet);
				
				echo '<div id="havenContent">
				<div id="havenContentShips" style="">
				<div style="padding:20px"><img src="images/loading.gif" alt="Loading" /> Lade Daten...</div>
				</div>
				<div id="havenContentTarget" style="display:none;"></div>
				<div id="havenContentWormhole" style="display:none;"></div>
				<div id="havenContentAction" style="display:none;"></div>
				</div>';
				echo '<script type="text/javascript">xajax_havenShowShips();</script>';
			}

			//
			// Mobile defenses
			// 
			else if ($mode == "transship")
			{
				if ($numMobile > 0)
				{		
					if (isset($_POST['dtransform_submit'])) {
						$sl = new ShipList($cp->id,$cu->id);
						$dl = new DefList($cp->id,$cu->id);

						$transformed_counter = 0;
						if (isset($_POST['dtransform']) && count($_POST['dtransform']) > 0) {
							foreach ($_POST['dtransform'] as $def_id => $v) {
								$res = dbquery("
									SELECT
										l.deflist_count as cnt,
										t.ship_id as id,
										t.num_def
									FROM
										deflist l
									INNER JOIN
										obj_transforms t
										ON t.def_id=l.deflist_def_id
										AND l.deflist_user_id=".$cu->id."
										AND l.deflist_entity_id=".$cp->id."
										AND l.deflist_count > 0
										AND l.deflist_def_id=".intval($def_id)."");
								if (mysql_num_rows($res)) {
									$arr = mysql_fetch_assoc($res);
									$packcount = intval(min(max(0, $v), $arr['cnt']));

									if ($packcount > 0) {
										$sl->add($arr['id'],$dl->remove($def_id, $packcount));
										$transformed_counter += $packcount;
									}
								}
							}
						}

						if ($transformed_counter > 0) {
							success_msg("$transformed_counter Verteidigungsanlagen wurden verladen!");
						}			
					}

					if (isset($_POST['stransform_submit'])) {
						$sl = new ShipList($cp->id,$cu->id);
						$dl = new DefList($cp->id,$cu->id);

						$transformed_counter = 0;
						if (isset($_POST['stransform']) && count($_POST['stransform']) > 0) {
							foreach ($_POST['stransform'] as $ship_id => $v) {
								$ship_id = intval($ship_id);
								$res = dbquery("
									SELECT
										l.shiplist_count as cnt,
										t.def_id as id,
										t.num_def
									FROM
										shiplist l
									INNER JOIN
										obj_transforms t
										ON t.ship_id=l.shiplist_ship_id
										AND l.shiplist_user_id=".$cu->id."
										AND l.shiplist_entity_id=".$cp->id."
										AND l.shiplist_count > 0
										AND l.shiplist_ship_id=".$ship_id."");
								if (mysql_num_rows($res)) {
									$arr = mysql_fetch_assoc($res);
									$packcount = intval(min(max(0, $v),$arr['cnt']));
									if ($packcount>0) {
										$dl->add($arr['id'],$sl->remove($ship_id, $packcount));
										$transformed_counter += $packcount;
									}
								}
							}
						}

						if ($transformed_counter > 0) {
							success_msg("$transformed_counter Verteidigungsanlagen wurden installiert!");
						}			
					}

					$has_mobile_objects = false;
					$otres = dbquery("
						SELECT
							d.def_id as id,
							d.def_name as name,
							l.deflist_count as cnt
						FROM
							defense d
						INNER JOIN
							obj_transforms t
							ON t.def_id=d.def_id
						INNER JOIN
							deflist l
							ON l.deflist_def_id=d.def_id
							AND l.deflist_user_id=".$cu->id."
							AND l.deflist_entity_id=".$cp->id."
							AND l.deflist_count > 0");
					if (mysql_num_rows($otres) > 0) {
						echo "<form action=\"?page=$page&mode=$mode\" method=\"post\">";
						tableStart("Verteidigungsanlagen auf Träger verladen");
						echo "<tr><th>Typ</th><th>Anzahl</th></tr>";
						while ($otarr = mysql_fetch_assoc($otres)) {
							echo "<tr><td>".$otarr['name']."</td>
								<td><input type=\"text\" name=\"dtransform[".$otarr['id']."]\" value=\"".$otarr['cnt']."\" size=\"7\" /></td></tr>";
						}

						tableEnd();
						echo "<input type=\"submit\" name=\"dtransform_submit\" value=\"Verladen\" /></form><br/>";
					}

					$otres = dbquery("
						SELECT
							d.ship_id as id,
							d.ship_name as name,
							l.shiplist_count as cnt
						FROM
							ships d
						INNER JOIN
							obj_transforms t
							ON t.ship_id=d.ship_id
						INNER JOIN
							shiplist l
							ON l.shiplist_ship_id=d.ship_id
							AND l.shiplist_user_id=".$cu->id."
							AND l.shiplist_entity_id=".$cp->id."
							AND l.shiplist_count > 0");
					if (mysql_num_rows($otres) > 0) {
						echo "<form action=\"?page=$page&mode=$mode\" method=\"post\">";
						tableStart("Mobile Verteidigung installieren");
						echo "<tr><th>Typ</th><th>Anzahl</th></tr>";
						while ($otarr = mysql_fetch_assoc($otres)) {
							echo "<tr><td>".$otarr['name']."</td>
								<td><input type=\"text\" name=\"stransform[".$otarr['id']."]\" value=\"".$otarr['cnt']."\" size=\"7\" /></td></tr>";
						}

						tableEnd();
						echo "<input type=\"submit\" name=\"stransform_submit\" value=\"Ausladen und installieren\" /></form><br/>";
					}
				}
				else
				{
					info_msg("Keine mobilen Anlagen vorhanden!", 1);
				}
			}
		}
	}
?>
