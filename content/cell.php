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
	// 	File: solsys.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Stellar system map
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// DATEN LADEN

	define('COLOR_ENEMY',$conf['color_enemy']['v']);
	define('COLOR_FRIEND',$conf['color_friend']['v']);
	define('COLOR_NOOB_SAFE',$conf['color_noob_safe']['v']);
	define('COLOR_OWN',"#0f0");

	// BEGIN SKRIPT //

	if (isset($_GET['id']) && $_GET['id']>0)
	{
		$cellId = $_GET['id'];
	}
	else
	{
		$cellId = $cp->cellId();
	}
	
	$_SESSION['currentEntity']=serialize($cp);
	
	// Systemnamen updaten
	if (isset($_POST['starname_submit']) && $_POST['starname']!="" && $_POST['starname_id']>0 && checker_verify())
	{
		$star = new Star($_POST['starname_id']);
		if ($star->isValid())
		{
			if ($star->setNewName($_POST['starname']))
			{
				echo "Der Stern wurde benannt!<br/><br/>";
			}
			else
			{
				echo "Es gab ein Problem beim Setzen des Namens!<br/><br/>";
			}
		}
		unset($star);
	}
		
		$cell = new Cell($cellId);
		if ($cell->isValid())
		{

			if ($cu->discovered($cell->absX(),$cell->absY()))
			{

				$entities = $cell->getEntities();
				
				echo "<h1>System ".$cell."</h1>";
						
				//
				// Systamkarte
				//
				tableStart("Karte");
				echo "<tr>
					<th colspan=\"2\" class=\"tbltitle\" style=\"width:60px;\">Position</th>
					<th class=\"tbltitle\">Typ</th>
					<th class=\"tbltitle\">Name</th>
					<th class=\"tbltitle\">Besitzer</th>
					<th class=\"tbltitle\">Aktionen</th>
				</tr>"; //<th class=\"tbltitle\">Allianz</th>
	
				$hasPlanetInSystem = false;
				$starNameEmpty = false;
				foreach ($entities as $ent)
				{
					if ($ent->pos()==1)
					{
						echo "<tr>
							<td class=\"tbldata\" style=\"height:3px;background:#000;\" colspan=\"6\"></td>
						</tr>";			
					}
					$addstyle=" style=\"vertical-align:middle;";
					if (isset($_GET['hl']) && $_GET['hl']==$ent->id())
					{
						$addstyle.="background:#003D6F;";
					}
					$addstyle.="\" ";
					
					echo "<tr>
						<td class=\"tbldata\" style=\"width:40px;background:#000;\">
							<a href=\"?page=entity&amp;id=".$ent->id()."\">
								<img src=\"".$ent->imagePath()."\" alt=\"icon\" />
							</a>
						</td>
						<td class=\"tbldata\" style=\"text-align:center;vertical-align:middle;background:#000\"><b>".$ent->pos()."</b></td>
						<td class=\"tbldata\" $addstyle>".$ent->type();
						
						if ($ent->entityCode()=='w')
						{
							$tent = new Wormhole($ent->targetId());
							echo "<br/>Ziel: <a href=\"?page=cell&amp;id=".$tent->cellId()."\">".$tent."</a>";
						}				
						elseif ($ent->entityCode()=='p' && $ent->debrisField)					
						{				
							echo "<br/><span style=\"color:#817339;font-weight:bold\" ".tm("Trümmerfeld",RES_ICON_METAL.nf($ent->debrisMetal)." ".RES_METAL."<br style=\"clear:both\" />".RES_ICON_CRYSTAL.nf($ent->debrisCrystal)." ".RES_CRYSTAL."<br style=\"clear:both\" />".RES_ICON_PLASTIC.nf($ent->debrisPlastic)." ".RES_PLASTIC."<br style=\"clear:both\" />").">Trümmerfeld</span> ";
						}	
						echo "</td>
						<td class=\"tbldata\" $addstyle><a href=\"?page=entity&amp;id=".$ent->id()."\">".$ent->name()."</a></td>
						<td class=\"tbldata\" $addstyle>";
						if ($ent->ownerId()>0)
							echo "<a href=\"?page=userinfo&amp;id=".$ent->ownerId()."\">".$ent->owner()."</a>";
						else
							echo $ent->owner();					
						echo "</td>
						<td class=\"tbldata\" $addstyle>";
	
						if ($ent->entityCode()=='s')					
						{
							if (!$ent->named)
							{
								$starNameEmpty=true;
								$starToBeNamed = $ent->id();
							}
						}
						elseif ($ent->entityCode()=='p')					
						{
							if ($ent->ownerId()>0 && $cu->id()==$ent->ownerId())
							{
								$hasPlanetInSystem = true;
							}
							
							// Nachrichten-Link
							if ($ent->ownerId()>0 && $cu->id()!=$ent->ownerId())
							{
								echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$ent->ownerId()."\" title=\"Nachricht senden\">Mail</a> ";
							}
								
							// Diverse Links
							if ($cu->id()!=$ent->ownerId())
							{
								// Besiedelte Planete
								if($ent->ownerId() > 0)
								{
									echo "<a href=\"javascript:;\" onclick=\"xajax_launchSypProbe(".$ent->id().");\" title=\"Ausspionieren\">Spionage</a> ";
									echo "<a href=\"?page=missiles&amp;target=".$ent->id()."\" title=\"Raketenangriff starten\">Rakete</a> ";
									echo "<a href=\"?page=crypto&amp;target=".$ent->id()."\" title=\"Flottenbewegungen analysieren\">Kryptoscan</a> ";					
								}
							}
						}
						
						// Flotte
						if ($ent->entityCode()=='p' || $ent->entityCode()=='a' || $ent->entityCode()=='w' || $ent->entityCode()=='n' || $ent->entityCode()=='e')
						{
							echo "<a href=\"?page=haven&amp;target=".$ent->id()."\" title=\"Flotte hinschicken\">Flotte</a> ";
						}
	
						// Favorit
						if ($cu->id()!=$ent->ownerId())
						{
							echo "<a href=\"?page=bookmarks&amp;add=".$ent->id()."\" title=\"Zu den Favoriten hinzuf&uuml;gen\">Favorit</a> ";
						}					
						echo "</td></tr>";
						
	
					
					/*
						if ($arr['user_id']>0)
						{
							// Bündnisse laden
							$bnd=dbquery("
							SELECT 
								COUNT(alliance_bnd_id)
							FROM 
								alliance_bnd 
							WHERE 
								(
									(
										alliance_bnd_alliance_id1=".$s['user']['alliance_id']." 
										AND alliance_bnd_alliance_id2=".$arr['user_alliance_id']."
									) 
									OR 
									(
										alliance_bnd_alliance_id2=".$s['user']['alliance_id']." 
										AND alliance_bnd_alliance_id1=".$arr['user_alliance_id']."
									)
								) 
								AND alliance_bnd_level=2
							");
							$bndarr=mysql_fetch_row($bnd);
							// Kriege laden
							$war=dbquery("
							SELECT 
								COUNT(alliance_bnd_id) 
							FROM 
								alliance_bnd 
							WHERE 
								(
									(
										alliance_bnd_alliance_id1=".$s['user']['alliance_id']." 
										AND alliance_bnd_alliance_id2=".$arr['user_alliance_id']."
									) 
									OR 
									(
										alliance_bnd_alliance_id2=".$s['user']['alliance_id']." 
										AND alliance_bnd_alliance_id1=".$arr['user_alliance_id']."
									)
								) 
								AND alliance_bnd_level=3
							");
							$wararr=mysql_fetch_row($war);
							// Krieg
							if ($wararr[0]>0)
							{
								$addstyle="color:".COLOR_ENEMY.";";
								$tm_info="(<span style=\'color:".COLOR_ENEMY."\'>Krieg</span>)";
							}
							// Bündniss
							elseif ($bndarr[0]>0)
							{
								$addstyle="color:".COLOR_FRIEND.";";
								$tm_info="(<span style=\'color:".COLOR_FRIEND."\'>B&uuml;ndnis</span>)";
							}
							// Gesperrt
							elseif ($arr['user_blocked_from']>0 && $arr['user_blocked_from']<time() && $arr['user_blocked_to']>time())
							{
								$addstyle="color:".COLOR_BANNED.";";
								$tm_info="(<span style=\'color:".COLOR_BANNED."\'>Gesperrt</span>)";
							}
							// Urlaub
							elseif ($arr['user_hmode_from']>0 && $arr['user_hmode_from']<time())
							{
								$addstyle="color:".COLOR_UMOD.";";
								$tm_info="(<span style=\'color:".COLOR_UMOD."\'>Urlaubsmodus</span>)";
							}
							// Lange Inaktiv
							elseif ($arr['user_acttime']<USER_INACTIVE_TIME_LONG)
							{
								$addstyle="color:".COLOR_INACTIVE_LONG.";";
								$tm_info="(<span style=\'color:".COLOR_INACTIVE_LONG."\'>Inaktiv</span>)";
							}						
							// Inaktiv
							elseif ($arr['user_acttime']<USER_INACTIVE_TIME)
							{
								$addstyle="color:".COLOR_INACTIVE.";";
								$tm_info="(<span style=\'color:".COLOR_INACTIVE."\'>Inaktiv</span>)";
							}
							// Eigener Planet
							elseif($s['user']['id']==$arr['user_id'])
							{
								$addstyle="color:".COLOR_OWN.";";
								$tm_info="";
							}					
							// Allianzmitglied
							elseif($s['user']['alliance_id']==$arr['user_alliance_id'] && $arr['user_alliance_id']!=0 && $_SESSION[ROUNDID]['user']['alliance_application']==0)
							{
								$addstyle="color:".COLOR_ALLIANCE.";";
								$tm_info="(<span style=\'color:".COLOR_ALLIANCE."\'>Allianzmitglied</span>)";
							}
							// Noob
							elseif (
							($s['user']['points']*USER_ATTACK_PERCENTAGE>$arr['user_points'] || $s['user']['points']/USER_ATTACK_PERCENTAGE<$arr['user_points'])
							&& $arr['planet_user_id']!=$s['user']['id'])
							{
								$addstyle="color:".COLOR_NOOB_SAFE.";";
								$tm_info="(<span style=\'color:".COLOR_NOOB_SAFE."\'>Anf&auml;ngerschutz</span>)";
							}
							else
							{
								$addstyle="";
								$tm_info="";
							}
						}
						else
						{
							$addstyle="";
						}
						$addstyle="";
						$class="tbldata";
	      	
						echo "<tr>";
						$p_img = IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['planet_image']."_small.gif";
						//$p_img_full = IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['planet_image'].".gif";
						//$tm_text = "<img src=\"".$p_img_full."\" alt=\"Planet\" style=\"background:#000;\" />";
						echo "<td style=\"background:#000;width:20px;height:20px\">
							<img src=\"$p_img\" style=\"width:20px;height:20px;border:none;\" alt=\"".$arr['type_name']."\" />
						</td>";
						echo "<td class=\"$class\" style=\"width:30px;height:20px;".$addstyle."\">".$arr['planet_solsys_pos']."";
						if ($arr['planet_wf_metal']>0 || $arr['planet_wf_crystal']>0 || $arr['planet_wf_plastic']>0)
						{
							echo "&nbsp;<img src=\"images/wreckage.png\" ".tm("Tr&uuml;mmerfeld","".RES_METAL.": ".nf($arr['planet_wf_metal'])."<br/>".RES_CRYSTAL.": ".nf($arr['planet_wf_crystal'])."<br/>".RES_PLASTIC.": ".nf($arr['planet_wf_plastic'])."")." style=\"width:12px;border:none\" />";
						}
						echo "</td>";
						$tm="";
						$tm.= "<b>Felder:</b>: ".$arr['planet_fields']."<br/>";
						$tm.= "<b>Bewohnbar</b>: ";
						if ($arr['type_habitable']==1) $tm.= "Ja"; else $tm.= "Nein	";
						if ($arr['type_f_metal']>1)
							$tm.="<br/><b>".RES_METAL.":</b> <span style=\'color:#0f0\'>+".get_percent_string($arr['type_f_metal'])."</span>";
						elseif ($arr['type_f_metal']<1)
							$tm.="<br/><b>".RES_METAL.":</b> <span style=\'color:#f00\'>".get_percent_string($arr['type_f_metal'])."</span>";
						if ($arr['type_f_crystal']>1)
							$tm.="<br/><b>".RES_CRYSTAL.":</b> <span style=\'color:#0f0\'>+".get_percent_string($arr['type_f_crystal'])."</span>";
						elseif ($arr['type_f_crystal']<1)
							$tm.="<br/><b>".RES_CRYSTAL.":</b> <span style=\'color:#f00\'>".get_percent_string($arr['type_f_crystal'])."</span>";
						if ($arr['type_f_plastic']>1)
							$tm.="<br/><b>".RES_PLASTIC.":</b> <span style=\'color:#0f0\'>+".get_percent_string($arr['type_f_plastic'])."</span>";
						elseif ($arr['type_f_plastic']<1)
							$tm.="<br/><b>".RES_PLASTIC.":</b> <span style=\'color:#f00\'>".get_percent_string($arr['type_f_plastic'])."</span>";
						if ($arr['type_f_fuel']>1)
							$tm.="<br/><b>".RES_FUEL.":</b> <span style=\'color:#0f0\'>+".get_percent_string($arr['type_f_fuel'])."</span>";
						elseif ($arr['type_f_fuel']<1)
							$tm.="<br/><b>".RES_FUEL.":</b> <span style=\'color:#f00\'>".get_percent_string($arr['type_f_fuel'])."</span>";
						if ($arr['type_f_food']>1)
							$tm.="<br/><b>".RES_FOOD.":</b> <span style=\'color:#0f0\'>+".get_percent_string($arr['type_f_food'])."</span>";
						elseif ($arr['type_f_food']<1)
							$tm.="<br/><b>".RES_FOOD.":</b> <span style=\'color:#f00\'>".get_percent_string($arr['type_f_food'])."</span>";
	      	
						if ($arr['type_f_power']>1)
							$tm.="<br/><b>Energie:</b> <span style=\'color:#0f0\'>+".get_percent_string($arr['type_f_power'])."</span>";
						elseif ($arr['type_f_power']<1)
							$tm.="<br/><b>Energie:</b> <span style=\'color:#f00\'>".get_percent_string($arr['type_f_power'])."</span>";
						if ($arr['type_f_population']>1)
							$tm.="<br/><b>Wachstum:</b> <span style=\'color:#0f0\'>+".get_percent_string($arr['type_f_population'])."</span>";
						elseif ($arr['type_f_population']<1)
							$tm.="<br/><b>Wachstum:</b> <span style=\'color:#f00\'>".get_percent_string($arr['type_f_population'])."</span>";
						if ($arr['type_f_researchtime']<1)
							$tm.="<br/><b>Forschungszeit:</b> <span style=\'color:#0f0\'>".get_percent_string($arr['type_f_researchtime'])."</span>";
						elseif ($arr['type_f_researchtime']>1)
							$tm.="<br/><b>Forschungszeit:</b> <span style=\'color:#f00\'>+".get_percent_string($arr['type_f_researchtime'])."</span>";
						if ($arr['type_f_buildtime']<1)
							$tm.="<br/><b>Bauzeit:</b> <span style=\'color:#0f0\'>".get_percent_string($arr['type_f_buildtime'])."</span>";
						elseif ($arr['type_f_buildtime']>1)
							$tm.="<br/><b>Bauzeit:</b> <span style=\'color:#f00\'>+".get_percent_string($arr['type_f_buildtime'])."</span>";
	      	
						echo "<td class=\"$class\" style=\"".$addstyle."\" ".tm($arr['type_name'],$tm).">".$arr['type_name']."</td>";
						if ($arr['planet_name']!="")
						{
							if ($arr['planet_desc']!="")$pdesc=text2html($arr['planet_desc']);else $pdesc="<i>Keine Beschreibung vorhanden</i>";
							echo "<td class=\"$class\"  style=\"".$addstyle."\" ".tm($arr['planet_name'],$pdesc).">";
							if ($c->id==$arr['id'])
							{
								echo "<b>".$arr['planet_name']."</b>";
							}
							else
							{
								echo $arr['planet_name'];
							}						
							echo "</td>";
						}
						else
						{
							echo "<td class=\"$class\"><i>Kein Name</i></td>";
						}
						if ($arr['planet_user_id']!=0)
						{
							if ($arr['user_alliance_id']>0)
							{
								$tm_alliance="<br/>Allianz: ".$arr['alliance_tag']."";
								$link_alliance = "<a href=\"?page=alliance&amp;info_id=".$arr['user_alliance_id']."\">".$arr['alliance_tag']."</a>";
							}
							else
							{
								$tm_alliance="";
								$link_alliance="";
							}
							echo "<td class=\"$class\"  style=\"".$addstyle."\" ".tm("".$arr['user_nick']." $tm_info","Punkte: ".nf($arr['user_points'])."".$tm_alliance."").">".$arr['user_nick']."</td>";
							echo "<td class=\"$class\">";
	      	
							echo "".$link_alliance."&nbsp;</td>";
						}
						else
							echo "<td class=\"$class\" colspan=\"2\" align=\"center\"><i>Unbewohnter Planet</i></td>";
						echo "<td class=\"$class\" style=\"width:100px;\">
							}
						}*/
						
						echo "</td></tr>"; 
					
				}
				
				echo "</table><br/><br/>";
				
				
				// System benennen
				if ($hasPlanetInSystem && $starNameEmpty)
				{
	 		    echo "<form action=\"?page=$page&amp;id=".intval($cellId)."\" method=\"post\">";
	 		    checker_init();
	 		    echo "Du darfst diesen Stern benennen: 
	 		    <input type=\"text\" name=\"starname\" value=\"\" maxlength=\"30\"/> 
	 		    <input type=\"hidden\" name=\"starname_id\" value=\"". $starToBeNamed."\" /> 
	 		    <input type=\"submit\" name=\"starname_submit\" value=\"Speichern\" /><br/><br/></form>";
	      }
							
				
				
				echo '<div id="spy_info_box" style="display:none;">';
				iBoxStart("Spionage");
				echo '<div id="spy_info"></div>';
				iBoxEnd();
				echo '</div>';
				
				iBoxStart("Legende");
				echo "<span style=\"color:".COLOR_OWN.";\">Eigener Planet</span>, <span style=\"color:".COLOR_BANNED.";\">Gesperrt</span>, <span style=\"color:".COLOR_UMOD.";\">Urlaubsmodus</span>, 
				<span style=\"color:".COLOR_INACTIVE.";\">Inaktiv (".USER_INACTIVE_SHOW." Tage)</span>, 
				<span style=\"color:".COLOR_INACTIVE_LONG.";\">Inaktiv (".USER_INACTIVE_LONG." Tage)</span><br/>
				<span style=\"color:".COLOR_NOOB_SAFE.";\">Anf&auml;ngerschutz</span>,
				<span style=\"color:".COLOR_FRIEND.";\">B&uuml;ndnis</span>, 
				<span style=\"color:".COLOR_ENEMY.";\">Krieg</span>, 
				<span style=\"color:".COLOR_ALLIANCE.";\">Allianzmitglied</span>";
				iBoxEnd();
				echo "<input type=\"button\" value=\"Zur Raumkarte\" onclick=\"document.location='?page=map&amp;sx=".$cell->sx."&amp;sy=".$cell->sy."'\" /> &nbsp; ";

			}
			else
			{
			echo "<h1>Fehler!</h1>System noch nicht erkundet. Erforsche das System mit einer Erkundungsflotte um es sichtbar zu machen!<br/><br/>";
			echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumkarte\" onclick=\"document.location='?page=map'\" />";
			}
		}
		else
		{
			echo "<h1>Fehler!</h1>System nicht gefunden!<br/><br/>";
			echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumkarte\" onclick=\"document.location='?page=map'\" />";
		}

?>

