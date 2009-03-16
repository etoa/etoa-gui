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


			$entities = $cell->getEntities();
			
			echo "<h1>System ".$cell."</h1>";

			$sx_num=$cfg->param1('num_of_sectors');
			$sy_num=$cfg->param2('num_of_sectors');
			$cx_num=$cfg->param1('num_of_cells');
			$cy_num=$cfg->param2('num_of_cells');

			
			if ($cu->discovered($cell->absX(),$cell->absY()))
			{
			$ares = dbquery("SELECT
								player_id
							FROM
								admin_users
							WHERE
								player_id<>0;");
			$admins = array();
			while ($arow = mysql_fetch_row($ares)) {
				array_push($admins,$arow[0]);
			}
			
			//
			// Systamkarte
			//
			tableStart("Systemkarte");

			echo "<tr><td colspan=\"6\" style=\"text-align:center;\">
			<a href=\"?page=galaxy\">Galaxie</a> &gt;&nbsp;
			<a href=\"?page=sector&sector=".$cell->sx.",".$cell->sy."\">Sektor ".$cell->sx."/".$cell->sy."</a> &gt; &nbsp;";
			$cres = dbquery("
			SELECT
				id
			FROM
				cells
			WHERE
				sx=".$cell->sx."
				AND sy=".$cell->sy."
				AND cx=1
				AND cy=1;
			");
			$carr = mysql_fetch_row($cres);
			$cid = $carr[0];
			echo "<select name=\"cell\" onchange=\"document.location='?page=$page&id='+this.value\">";
			for ($x=1;$x<=$cx_num;$x++)
			{
				for ($y=1;$y<=$cy_num;$y++)
				{		
					echo "<option value=\"".$cid."\"";
					if ($cell->cx==$x && $cell->cy==$y)
						echo " selected=\"selected\"";
					echo ">System $x/$y &nbsp;</option>";
					$cid++;
				}
			}
			echo "</select>";
			echo "</td></tr>";				

			
				echo "<tr>
					<th colspan=\"2\" class=\"tbltitle\" style=\"width:60px;\">Position</th>
					<th class=\"tbltitle\">Typ</th>
					<th class=\"tbltitle\">Name</th>
					<th class=\"tbltitle\">Besitzer</th>
					<th class=\"tbltitle\" style=\"width:150px;\">Aktionen</th>
				</tr>";
	
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
					
					$class = " class=\"";
					if ($ent->ownerId()>0)
					{
					  //Admin
					  if (in_array($ent->ownerId(),$admins)) {
						  $class .= "adminColor";
						  $tm_info = "Admin/Entwickler";						  
					  }
					  // Krieg
					  elseif ($ent->owner->allianceId>0 && $cu->allianceId>0 && $cu->alliance->checkWar($ent->owner->allianceId))
					  {
						  $class .= "enemyColor";
						  $tm_info = "Krieg";
					  }
					  // Bündniss
					  elseif ($ent->owner->allianceId>0 && $cu->allianceId>0 && $cu->alliance->checkBnd($ent->owner->allianceId))
					  {
						  $class .= "friendColor";
						  $tm_info = "B&uuml;ndnis";
					  }
					  // Gesperrt
					  elseif ($ent->ownerLocked())
					  {
						  $class .= "userLockedColor";
						  $tm_info = "Gesperrt";
					  }
					  // Urlaub
					  elseif ($ent->ownerHoliday())
					  {
						  $class .= "userHolidayColor";
						  $tm_info = "Urlaubsmodus";
					  }
					  // Lange Inaktiv
					  elseif ($ent->owner->acttime<time()-USER_INACTIVE_LONG*86400)
					  {
						  $class .= "userLongInactiveColor";
						  $tm_info = "Inaktiv";
					  }		
					  // Inaktiv
					  elseif ($ent->owner->acttime<time()-USER_INACTIVE_SHOW*86400)
					  {
						  $class .= "userInactiveColor";
						  $tm_info = "Inaktiv";
					  }
					  // Eigener Planet
					  elseif($cu->id==$ent->ownerId())
					  {
						  $class .= "userSelfColor";
						  $tm_info = "";
					  }
					  // Allianzmitglied
					  elseif($cu->allianceId()==$ent->owner->allianceId() && $cu->allianceId())
					  {
						  $class .= "userAllianceMemberColor";
						  $tm_info = "Allianzmitglied";
					  }
					  // Noob
					  elseif (
						  ($cu->points*USER_ATTACK_PERCENTAGE>$ent->ownerPoints() || $cu->points/USER_ATTACK_PERCENTAGE<$ent->ownerPoints())
						  && $ent->ownerId()!=$cu->id)
					  {
						  $class .= "noobColor";
						  $tm_info = "Anf&auml;ngerschutz";
					  }
					  else
					  {
						  $class .= "tbldata";
						  $tm_info="";
					  }
					}
					else
					{
					  $class .= "tbldata";
					  $tm_info="";
					}
					$class .="\" ";
					
					echo "<tr>
						<td $class style=\"width:40px;background:#000;\">
							<a href=\"?page=entity&amp;id=".$ent->id()."\">
								<img src=\"".$ent->imagePath()."\" alt=\"icon\" />
							</a>
						</td>
						<td $class style=\"text-align:center;vertical-align:middle;background:#000\"><b>".$ent->pos()."</b></td>
						<td $class $addstyle>".$ent->type();
						
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
						<td $class $addstyle><a href=\"?page=entity&amp;id=".$ent->id()."\">".$ent->name()."</a></td>
						<td $class $addstyle>";
						if ($ent->ownerId()>0)
						{
							$header = $ent->owner();
							$tm = "Punkte: ".nf($ent->owner->points)."<br style=\"clear:both\" />";
							if ($ent->ownerAlliance()>0)
								$tm .= "Allianz: ".$ent->owner->alliance."<br style=\"clear:both\" />";
							if ($tm_info!="")
								$header .= " (<span $class>".$tm_info."</span>)";
							echo "<span style=\"color:#817339;font-weight:bold\" ".tm($header,$tm)."><a href=\"?page=userinfo&amp;id=".$ent->ownerId()."\">".$ent->owner()."</a></span> ";
						}
						else
							echo $ent->owner();
						echo "</td>
						<td $class $addstyle>";
	
							// Favorit
						if ($cu->id!=$ent->ownerId())
						{
							echo "<a href=\"?page=bookmarks&amp;add=".$ent->id()."\" title=\"Zu den Favoriten hinzuf&uuml;gen\">".icon("favorite")."</a> ";
						}		
	
						// Flotte
						if ($ent->entityCode()=='p' || $ent->entityCode()=='a' || $ent->entityCode()=='w' || $ent->entityCode()=='n' || $ent->entityCode()=='e')
						{
							echo "<a href=\"?page=haven&amp;target=".$ent->id()."\" title=\"Flotte hinschicken\">".icon('fleet')."</a> ";
						}
	
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
							if ($ent->ownerId()>0 && $cu->id==$ent->ownerId())
							{
								$hasPlanetInSystem = true;
							}
							
							// Nachrichten-Link
							if ($ent->ownerId()>0 && $cu->id!=$ent->ownerId())
							{
								echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$ent->ownerId()."\" title=\"Nachricht senden\">".icon("mail")."</a> ";
							}
								
							// Diverse Links
							if ($cu->id!=$ent->ownerId())
							{
								// Besiedelte Planete
								if($ent->ownerId() > 0)
								{
									echo "<a href=\"javascript:;\" onclick=\"xajax_launchSypProbe(".$ent->id().");\" title=\"Ausspionieren\">".icon("spy")."</a> ";
									echo "<a href=\"?page=missiles&amp;target=".$ent->id()."\" title=\"Raketenangriff starten\">".icon("missile")."</a> ";
									echo "<a href=\"?page=crypto&amp;target=".$ent->id()."\" title=\"Flottenbewegungen analysieren\">".icon("crypto")."</a> ";					
								}
							}
						}
						

			
						echo "</td></tr>";
						
	
					/*
	      	
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
				echo "
				<span class=\"userSelfColor\">Eigener Planet</span>, 
				<span class=\"userLockedColor\">Gesperrt</span>, 
				<span class=\"userHolidayColor\">Urlaubsmodus</span>, 
				<span class=\"userInactiveColor\">Inaktiv (".USER_INACTIVE_SHOW." Tage)</span>, 
				<span class=\"userLongInactiveColor\">Inaktiv (".USER_INACTIVE_LONG." Tage)</span><br/>
				<span class=\"noobColor\">Anf&auml;ngerschutz</span>,
				<span class=\"friendColor\">B&uuml;ndnis</span>, 
				<span class=\"enemyColor\">Krieg</span>, 
				<span class=\"userAllianceMemberColor\">Allianzmitglied</span>,
				<span class=\"adminColor\" ".tm("Admin/Entwickler","Gemäss §14.2 ist es strengstens untersagt einen Adminaccount anzugreifen oder auszuspionieren. Wer dies tut ist selber schuld und kann mit einer Sperre von 24h bestraft werden!<br style=\"clear:both\" />").">Admin/Entwickler</span>";
				iBoxEnd();
			}
			else
			{
				iBoxStart("Fehler");
				echo "<div style=\"text-align:center;\">
				<a href=\"?page=galaxy\">Galaxie</a> &gt;&nbsp;
				<a href=\"?page=sector&sector=".$cell->sx.",".$cell->sy."\">Sektor ".$cell->sx."/".$cell->sy."</a> &gt; &nbsp;";
				$cres = dbquery("
				SELECT
					id
				FROM
					cells
				WHERE
					sx=".$cell->sx."
					AND sy=".$cell->sy."
					AND cx=1
					AND cy=1;
				");
				$carr = mysql_fetch_row($cres);
				$cid = $carr[0];
				echo "<select name=\"cell\" onchange=\"document.location='?page=$page&id='+this.value\">";
				for ($x=1;$x<=$cx_num;$x++)
				{
					for ($y=1;$y<=$cy_num;$y++)
					{		
						echo "<option value=\"".$cid."\"";
						if ($cell->cx==$x && $cell->cy==$y)
							echo " selected=\"selected\"";
						echo ">System $x/$y &nbsp;</option>";
						$cid++;
					}
				}
				echo "</select></div><br/>";				
				echo "System noch nicht erkundet. Erforsche das System mit einer Erkundungsflotte um es sichtbar zu machen!<br/><br/>";
				iBoxEnd();
				echo button("Erkundungsflotte senden","?page=haven&cellTarget=".$cellId)." &nbsp; ";
			}
			
			echo "<input type=\"button\" value=\"Zur Raumkarte\" onclick=\"document.location='?page=sector&amp;sx=".$cell->sx."&amp;sy=".$cell->sy."'\" /> &nbsp; ";

		}
		else
		{
			echo "<h1>Fehler!</h1>System nicht gefunden!<br/><br/>";
			echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumkarte\" onclick=\"document.location='?page=sector'\" />";
		}

?>

