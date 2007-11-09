<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: solsys.php														//
	// Topic: Sonnensystem-Modul	 									//
	// Version: 0.1																	//
	// Letzte Änderung: 01.10.2004									//
	//////////////////////////////////////////////////

	// DATEN LADEN

	define('INACTIVE_TIME',time()-(24*3600*$conf['user_inactive_days']['v']));
	//define('COLOR_BANNED',$conf['color_banned']['v']);
	//define('COLOR_UMOD',$conf['color_umod']['v']);
	//define('COLOR_INACTIVE',$conf['color_inactive']['v']);
	//define('COLOR_ALLIANCE',$conf['color_alliance']['v']);
	define('COLOR_ENEMY',$conf['color_enemy']['v']);
	define('COLOR_FRIEND',$conf['color_friend']['v']);
	define('COLOR_NOOB_SAFE',$conf['color_noob_safe']['v']);
	//define('COLOR_DEFAULT',$conf['color_default']['v']);
	define('COLOR_OWN',"#0f0");

	// BEGIN SKRIPT //

	if ($_GET['id']>0)
	{
		if (isset($_GET['mode']))
		{
			$mode=$_GET['mode'];
		}
		else
		{
			$mode="default";
		}
		
		// Systemnamen updaten
		if (isset($_POST['submit']) && $_POST['submit']!="" && $_POST['cell_solsys_name']!="")
		{
			$check_name=check_illegal_signs($_POST['cell_solsys_name']);
			if(!$check_name)
			{
      	dbquery("
      	UPDATE 
      		".$db_table['space_cells']." 
      	SET 
      		cell_solsys_name='".$_POST['cell_solsys_name']."'
       	WHERE 
       		cell_id='".intval($_GET['id'])."'
       	;");
      }
      else
      {
      	echo "Unerlaubtes Zeichen (".$check_name.") im Namen!<br>";
      }
		}

		// Systemnamen anzeigen
		$reso=dbquery("
		SELECT 
            space_cells.cell_sx,
            space_cells.cell_sy,
            space_cells.cell_cx,
            space_cells.cell_cy,
            space_cells.cell_solsys_solsys_sol_type,
            space_cells.cell_solsys_name,
            sol_types.type_name 
		FROM 
            ".$db_table['space_cells']."
    INNER JOIN
            ".$db_table['sol_types']." 
            ON 	space_cells.cell_solsys_solsys_sol_type=sol_types.type_id
		WHERE 
		 				space_cells.cell_id='".intval($_GET['id'])."';");
		if (mysql_num_rows($reso))
		{
			$arro=mysql_fetch_array($reso);
			echo "<h1>Sonnensystem ".$arro['cell_sx']."/".$arro['cell_sy']." : ".$arro['cell_cx']."/".$arro['cell_cy']."</h2>";
			
			if ($mode=="image")
			{
				$_SESSION['sol']=array();
				$_SESSION['sol']['image']=IMAGE_PATH."/galaxy/sol".$arro['cell_solsys_solsys_sol_type'].".gif";
				if ($arro['cell_solsys_name']!="")
					echo "<h2>".$arro['cell_solsys_name']."</b> (".$arro['type_name'].")</h2>";
				else
					echo "<h2>".$arro['type_name']."</h2";			
			}
			else
			{
				echo "<table width=\"450\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\" class=\"tbldata\">";
				echo "<tr><td class=\"tbldata\" width=\"39\" height=\"39\" style=\"color:#000;\">";
				echo "<img src=\"".IMAGE_PATH."/galaxy/sol".$arro['cell_solsys_solsys_sol_type'].".gif\" border=\"0\" width=\"40\" height=\"40\"></td>";
				echo "<td class=\"tbldata\" style=\"text-align:center;font-size:12pt;vertical-align:middle;\">";
				if ($arro['cell_solsys_name']!="")
					echo "<b>".$arro['cell_solsys_name']."</b> (".$arro['type_name'].")";
				else
					echo $arro['type_name'];
				echo "</td>";
				echo "</table><br>";
			}

			$sx=$arro['cell_sx'];
			$sy=$arro['cell_sy'];

			// System benennen
			if ($arro['cell_solsys_name']=="")
			{
      	$snres = dbquery("
      	SELECT 
      		users.user_id 
      	FROM 
      		".$db_table['planets']."
      	INNER JOIN
      		".$db_table['users']." 
      		ON planets.planet_user_id=users.user_id 
      	WHERE            
        	planets.planet_solsys_id='".intval($_GET['id'])."' 
      	ORDER BY 
      		planets.planet_user_main DESC,
      		users.user_points DESC 
      	LIMIT 1;");
      	if (mysql_num_rows($snres)>0)
      	{
      		$snarr = mysql_fetch_array($snres);
      		if ($_SESSION[ROUNDID]['user']['id']==$snarr['user_id'])
      		{
      		    echo "<form action=\"?page=$page&id=".intval($_GET['id'])."\" method=\"post\">";
      		    echo "Du darfst diesen Stern benennen: <input type=\"text\" name=\"cell_solsys_name\" value=\"\" maxlength=\"30\"/> <input type=\"submit\" name=\"submit\" value=\"Speichern\" /><br/><br/></form>";
      		}
      	}
      }


			//
			// Planeten
			//
			
			if ($mode!="image")
			{
				infobox_start("Planeten",1);
				echo "<tr><th colspan=\"2\" class=\"tbltitle\">Pos</th><th class=\"tbltitle\">Typ</th><th class=\"tbltitle\">Name</th><th class=\"tbltitle\">Besitzer</th><th class=\"tbltitle\">Allianz</th><th class=\"tbltitle\">&nbsp;</th></tr>";
			}
			$res=dbquery("
			SELECT 
				planets.planet_id,
				planets.planet_name,
				planets.planet_desc,
				planets.planet_image,
				planets.planet_fields,
				planets.planet_user_id,
				planets.planet_wf_metal,
				planets.planet_wf_crystal,
				planets.planet_wf_plastic,
				planets.planet_solsys_pos,
				planets.planet_semi_major_axis,
				planets.planet_ecccentricity,
				planets.planet_mass,
				planet_types.*,
				users.user_id,
				users.user_points,
        users.user_acttime,
        users.user_hmode_from,
        users.user_nick,
        users.user_alliance_id,
        users.user_alliance_application,
        users.user_blocked_to,
        users.user_blocked_from,
        alliances.alliance_tag				
			FROM 
				".$db_table['planets']."
			INNER JOIN
				".$db_table['planet_types']." 
				ON planets.planet_type_id=planet_types.type_id     
			LEFT JOIN
			(
				".$db_table['users']."
				LEFT JOIN
					 ".$db_table['alliances']." 
					ON user_alliance_id=alliance_id
					AND user_alliance_application=''
			)
			ON  user_id=planet_user_id
			WHERE 			 
				planets.planet_solsys_id='".intval($_GET['id'])."' 
			ORDER BY 
				planets.planet_solsys_pos ASC");  
				
			$_SESSION['planets']=array();
			while ($arr=mysql_fetch_array($res))
			{
				if ($mode=="image")
				{
					$_SESSION['planets'][$arr['planet_solsys_pos']]['name']=$arr['planet_name'];
					$_SESSION['planets'][$arr['planet_solsys_pos']]['semi_major_axis']=$arr['planet_semi_major_axis'];
					$_SESSION['planets'][$arr['planet_solsys_pos']]['ecccentricity']=$arr['planet_ecccentricity'];
					$_SESSION['planets'][$arr['planet_solsys_pos']]['mass']=$arr['planet_mass'];
					$_SESSION['planets'][$arr['planet_solsys_pos']]['image']=IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['planet_image']."_small.gif";
					
				}
				else
				{
					if ($arr['user_id']>0)
					{
						// Bündnisse laden
						$bnd=dbquery("
						SELECT 
							COUNT(alliance_bnd_id)
						FROM 
							".$db_table['alliance_bnd']." 
						WHERE 
							(
								(
									alliance_bnd_alliance_id1=".$_SESSION[ROUNDID]['user']['alliance_id']." 
									AND alliance_bnd_alliance_id2=".$arr['user_alliance_id']."
								) 
								OR 
								(
									alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id']." 
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
							".$db_table['alliance_bnd']." 
						WHERE 
							(
								(
									alliance_bnd_alliance_id1=".$_SESSION[ROUNDID]['user']['alliance_id']." 
									AND alliance_bnd_alliance_id2=".$arr['user_alliance_id']."
								) 
								OR 
								(
									alliance_bnd_alliance_id2=".$_SESSION[ROUNDID]['user']['alliance_id']." 
									AND alliance_bnd_alliance_id1=".$arr['user_alliance_id']."
								)
							) 
							AND alliance_bnd_level=3
						");
						$wararr=mysql_fetch_row($war);
						// Krieg
						if ($wararr[0]>0)
						{
							$addstyle=" style=\"color:".COLOR_ENEMY.";\"";
							$tm_info="(<span style=\'color:".COLOR_ENEMY."\'>Krieg</span>)";
						}
						// Bündniss
						elseif ($bndarr[0]>0)
						{
							$addstyle=" style=\"color:".COLOR_FRIEND.";\"";
							$tm_info="(<span style=\'color:".COLOR_FRIEND."\'>B&uuml;ndnis</span>)";
						}
						// Gesperrt
						elseif ($arr['user_blocked_from']>0 && $arr['user_blocked_from']<time() && $arr['user_blocked_to']>time())
						{
							$addstyle=" style=\"color:".COLOR_BANNED.";\"";
							$tm_info="(<span style=\'color:".COLOR_BANNED."\'>Gesperrt</span>)";
						}
						// Urlaub
						elseif ($arr['user_hmode_from']>0 && $arr['user_hmode_from']<time())
						{
							$addstyle=" style=\"color:".COLOR_UMOD.";\"";
							$tm_info="(<span style=\'color:".COLOR_UMOD."\'>Urlaubsmodus</span>)";
						}
						// Inaktiv
						elseif ($arr['user_acttime']<INACTIVE_TIME)
						{
							$addstyle=" style=\"color:".COLOR_INACTIVE.";\"";
							$tm_info="(<span style=\'color:".COLOR_INACTIVE."\'>Inaktiv</span>)";
						}
						// Eigener Planet
						elseif($_SESSION[ROUNDID]['user']['id']==$arr['user_id'])
						{
							$addstyle=" style=\"color:".COLOR_OWN.";\"";
							$tm_info="";
						}					
						// Allianzmitglied
						elseif($_SESSION[ROUNDID]['user']['alliance_id']==$arr['user_alliance_id'] && $arr['user_alliance_id']!=0 && $arr['user_alliance_application']=="")
						{
							$addstyle=" style=\"color:".COLOR_ALLIANCE.";\"";
							$tm_info="(<span style=\'color:".COLOR_ALLIANCE."\'>Allianzmitglied</span>)";
						}
						// Noob
						elseif (
						($_SESSION[ROUNDID]['user']['points']*USER_ATTACK_PERCENTAGE>$arr['user_points'] || $_SESSION[ROUNDID]['user']['points']/USER_ATTACK_PERCENTAGE<$arr['user_points'])
						&& $arr['planet_user_id']!=$_SESSION[ROUNDID]['user']['id'])
						{
							$addstyle=" style=\"color:".COLOR_NOOB_SAFE.";\"";
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
					$class="tbldata";
      	
					echo "<tr>";
					$p_img = IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['planet_image']."_small.gif";
					$p_img_full = IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr['planet_image'].".gif";
					echo "<td style=\"background:#000\" width=\"20\" height=\"20\" ".tm("","<div style=\'background:#000;\'><img src=\"$p_img_full\" alt=\"Planet\" /></div>")."><a href=\"javascript:;\" onclick=\"window.open('img_popup.php?image_url=$p_img_full','popup','status=no,scrollbars=yes')\"><img src=\"$p_img\" width=\"20\" height=\"20\" border=\"0\" /></a></td>";
					echo "<td class=\"$class\" $addstyle width=\"30\" height=\"20\">".$arr['planet_solsys_pos']."";
					if ($arr['planet_wf_metal']>0 || $arr['planet_wf_crystal']>0 || $arr['planet_wf_plastic']>0)
					{
						echo "&nbsp;<img src=\"images/wreckage.png\" ".tm("Tr&uuml;mmerfeld","".RES_METAL.": ".nf($arr['planet_wf_metal'])."<br>".RES_CRYSTAL.": ".nf($arr['planet_wf_crystal'])."<br>".RES_PLASTIC.": ".nf($arr['planet_wf_plastic'])."")." width=\"12\" border=\"0\"/>";
					}
					echo "</td>";
					$tm="";
					$tm.= "<b>Felder:</b>: ".$arr['planet_fields']."<br>";
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
      	
					echo "<td class=\"$class\" $addstyle ".tm($arr['type_name'],$tm).">".$arr['type_name']."</td>";
					if ($arr['planet_name']!="")
					{
						if ($arr['planet_desc']!="")$pdesc=$arr['planet_desc'];else $pdesc="<i>-</i>";
						echo "<td class=\"$class\" $addstyle ".tm($arr['planet_name'],$pdesc).">".$arr['planet_name']."</td>";
					}
					else
					{
						echo "<td class=\"$class\"><i>Kein Name</a></td>";
					}
					if ($arr['planet_user_id']!=0)
					{
						if ($arr['user_alliance_id']>0)
						{
							$tm_alliance="<br>Allianz: ".$arr['alliance_tag']."";
							$link_alliance = "<a href=\"?page=alliance&amp;info_id=".$arr['user_alliance_id']."\">".$arr['alliance_tag']."</a>";
						}
						else
						{
							$tm_alliance="";
							$link_alliance="";
						}
						echo "<td class=\"$class\" $addstyle ".tm("".$arr['user_nick']." $tm_info","Punkte: ".nf($arr['user_points'])."".$tm_alliance."").">".$arr['user_nick']."</td>";
						echo "<td class=\"$class\">";
      	
						echo "".$link_alliance."&nbsp;</td>";
					}
					else
						echo "<td class=\"$class\" colspan=\"2\" align=\"center\"><i>Unbewohnter Planet</a></td>";
					echo "<td class=\"$class\"><a href=\"?page=planet&planet_info_id=".$arr['planet_id']."&solsys_id=".intval($_GET['id'])."\" title=\"Planeteninfo\">Info</a>";
					if ($_SESSION[ROUNDID]['user']['id']!=$arr['planet_user_id'] && $arr['planet_user_id']>0)
						echo "&nbsp;<a href=\"?page=messages&mode=new&message_user_to=".$arr['planet_user_id']."\" title=\"Nachricht senden\">Mail</a>";
					if ($c->id!=$arr['planet_id'])
						echo "&nbsp;<a href=\"?page=haven&planet_to=".$arr['planet_id']."\" title=\"Flotte hinschicken\">Flotte</a>";
					if ($_SESSION[ROUNDID]['user']['id']!=$arr['planet_user_id'])
						echo "&nbsp;<a href=\"?page=bookmarks&amp;add_planet_id=".$arr['planet_id']."\" title=\"Zu den Favoriten hinzuf&uuml;gen\">Favorit</a>";
					echo "</td></tr>"; 
				}
			}
			
			if ($mode=="image")
			{				
				echo "<img src=\"misc/solsys.image.php?sol=".$_GET['id']."\" alt=\"Solarsystem\" usemap=\"solsys\" style=\"border:1px solid #fff;\" /><br/><br/>";
			}
			else
			{			
				echo "</table><br/><br/>";
			}

			infobox_start("Legende");
			echo "<span style=\"color:".COLOR_OWN.";\">Eigener Planet</span>, <span style=\"color:".COLOR_BANNED.";\">Gesperrt</span>, <span style=\"color:".COLOR_UMOD.";\">Urlaubsmodus</span>, <span style=\"color:".COLOR_INACTIVE.";\">Inaktiv (7 Tage)</span>, <span style=\"color:".COLOR_NOOB_SAFE.";\">Anf&auml;ngerschutz</span>, <span style=\"color:".COLOR_FRIEND.";\">B&uuml;ndnis</span>, <span style=\"color:".COLOR_ENEMY.";\">Krieg</span>, <span style=\"color:".COLOR_ALLIANCE.";\">Allianzmitglied</span>";
			infobox_end();
			echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumkarte\" onclick=\"document.location='?page=space&sx=$sx&sy=$sy'\" /> &nbsp; ";
			if ($mode=="image")
			{				
				echo "<input type=\"button\" value=\"Tabellarische Ansicht\" onclick=\"document.location='?page=solsys&id=".$_GET['id']."'\" />";
			}
			else
			{
				echo "<input type=\"button\" value=\"Grafische Ansicht (Beta)\" onclick=\"document.location='?page=solsys&id=".$_GET['id']."&mode=image'\" />";
			}
		}
		else
		{
			echo "<h1>Fehler!</h1>System nicht gefunden!<br/><br/>";
			echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumkarte\" onclick=\"document.location='?page=space'\" />";
		}
	}
	else
	{
		echo "<h1>Fehler!</h1>System-ID nicht angegeben!<br/><br/>";
		echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumkarte\" onclick=\"document.location='?page=space'\" />";
	}
?>

