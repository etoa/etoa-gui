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
	// $Author$
	// $Date$
	// $Rev$
	//
	
	/**
	* Displays objects dependencies
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// Definitionen

	if(isset($_GET['mode']) && $_GET['mode']!="") 
		$mode=$_GET['mode']; 
	else 
		$mode="";
	

	if ($mode=="tech")
	{
		define('ITEMS_TBL',"technologies");
		define('TYPES_TBL',"tech_types");
		define('REQ_TBL',"tech_requirements");
		define('ITEM_ID_FLD',"tech_id");
		define('ITEM_NAME_FLD',"tech_name");
		define('ITEM_RACE_FLD',"");
		define('ITEM_SHOW_FLD',"tech_show");
		define('ITEM_TYPE_FLD',"tech_type_id");
		define('ITEM_ORDER_FLD',"tech_order");
		define('TYPE_ORDER_FLD',"type_order");
		define('TYPE_ID_FLD',"type_id");
		define('TYPE_NAME_FLD',"type_name");
		define('NO_ITEMS_MSG',"In dieser Kategorie gibt es keine Technologien!");
		define('HELP_URL',"research");
	}
	elseif ($mode=="ships")
	{
		define('ITEMS_TBL',"ships");
		define('TYPES_TBL',"ship_cat");
		define('REQ_TBL',"ship_requirements");
		define('ITEM_ID_FLD',"ship_id");
		define('ITEM_NAME_FLD',"ship_name");
		define('ITEM_RACE_FLD',"ship_race_id");
		define('ITEM_SHOW_FLD',"ship_buildable");
		define('ITEM_TYPE_FLD',"ship_cat_id");
		define('ITEM_ORDER_FLD',"ship_name");
		define('TYPE_ORDER_FLD',"cat_order");
		define('TYPE_ID_FLD',"cat_id");
		define('TYPE_NAME_FLD',"cat_name");
		define('NO_ITEMS_MSG',"In dieser Kategorie gibt es keine Schiffe!");
		define('HELP_URL',"shipyard");
	}
	elseif ($mode=="defense")
	{
		define('ITEMS_TBL',"defense");
		define('TYPES_TBL',"def_cat");
		define('REQ_TBL',"def_requirements");
		define('ITEM_ID_FLD',"def_id");
		define('ITEM_NAME_FLD',"def_name");
		define('ITEM_RACE_FLD',"def_race_id");
		define('ITEM_SHOW_FLD',"def_buildable");
		define('ITEM_TYPE_FLD',"def_cat_id");
		define('ITEM_ORDER_FLD',"def_order,def_name");
		define('TYPE_ORDER_FLD',"cat_order");
		define('TYPE_ID_FLD',"cat_id");
		define('TYPE_NAME_FLD',"cat_name");
		define('NO_ITEMS_MSG',"In dieser Kategorie gibt es keine Verteidigungsanlagen!");
		define('HELP_URL',"defense");
	}
	elseif ($mode=="missiles")
	{
		define('ITEMS_TBL',"missiles");
		define('REQ_TBL',"missile_requirements");
		define('ITEM_ID_FLD',"missile_id");
		define('ITEM_NAME_FLD',"missile_name");
		define('ITEM_RACE_FLD',"");
		define('ITEM_SHOW_FLD',"missile_show");
		define('ITEM_ORDER_FLD',"missile_name");
		define('NO_ITEMS_MSG',"In dieser Kategorie gibt es keine Raketen!");
		define('HELP_URL',"missiles");
	}	
	elseif ($mode=="buildings")
	{
		define('ITEMS_TBL',"buildings");
		define('TYPES_TBL',"building_types");
		define('REQ_TBL',"building_requirements");
		define('ITEM_ID_FLD',"building_id");
		define('ITEM_NAME_FLD',"building_name");
		define('ITEM_RACE_FLD',"");
		define('ITEM_SHOW_FLD',"building_show");
		define('ITEM_TYPE_FLD',"building_type_id");
		define('ITEM_ORDER_FLD',"building_order");
		define('TYPE_ORDER_FLD',"type_order");
		define('TYPE_ID_FLD',"type_id");
		define('TYPE_NAME_FLD',"type_name");
		define('NO_ITEMS_MSG',"In dieser Kategorie gibt es keine Geb&auml;ude!");
		define('HELP_URL',"buildings");
	}


	if (isset($cp))
	{
		
		// Daten anzeigen
		echo "<h1>Technikbaum des Planeten ".$cp->name()."</h1>";

		// Tab-Navigation anzeigen
		show_tab_menu("mode",array(
		""=>"Grafik",
		"buildings"=>"Geb&auml;ude",
		"tech"=>"Technologien",
		"ships"=>"Schiffe",
		"defense"=>"Verteidigung",
		"missiles"=>"Raketen"
		));
		echo "<br>";	
		
		if ($mode!="")
		{	
		
		//
		// Läd alle benötigten Daten
		//

		// Lade Rassennamen
		$race=array();
		$rres = dbquery("
		SELECT 
			race_id,
			race_name
		FROM 
			races ;");
		while ($rarr = mysql_fetch_array($rres))
		{
			$race[$rarr['race_id']] = $rarr['race_name'];
		}

		// Lade Gebäudelistenlevel
		$buildlist=array();
		$bres = dbquery("
		SELECT 
			buildlist_current_level,
			buildlist_building_id
		FROM 
			buildlist 
		WHERE 
			buildlist_entity_id='".$cp->id()."'
		;");
		while ($barr = mysql_fetch_array($bres))
		{
			$buildlist[$barr['buildlist_building_id']] = $barr['buildlist_current_level'];
		}
		
		// Lade Techlistenlevel
		$techlist=array();
		$tres = dbquery("
		SELECT 
			techlist_current_level,
			techlist_tech_id
		FROM 
			techlist 
		WHERE 
			techlist_user_id='".$cu->id."'
		;");
		while ($tarr = mysql_fetch_array($tres))
		{
			$techlist[$tarr['techlist_tech_id']]=$tarr['techlist_current_level'];
		}

		// Lade Gebäudenamen
		$bu_name=array();
		$bures = dbquery("
		SELECT 	
			building_id,
			building_name 
		FROM 
			buildings 
		;");
		while ($buarr = mysql_fetch_array($bures))
		{
			$bu_name[$buarr['building_id']]=$buarr['building_name'];
		}
		
		// Lade Technologienamen
		$te_name=array();
		$teres = dbquery("
		SELECT 
			tech_id,
			tech_name 
		FROM 
			technologies 
		");
		while ($tearr = mysql_fetch_array($teres))
		{
			$te_name[$tearr['tech_id']]=$tearr['tech_name'];
		}

		// Lade Anforderungen
		$b_req=array();
		$rres = dbquery("
		SELECT 
			* 
		FROM 
			".REQ_TBL.";
		");
		while ($rarr = mysql_fetch_array($rres))
		{
			if ($rarr['req_building_id']>0) $b_req[$rarr['obj_id']]['b'][$rarr['req_building_id']]=$rarr['req_level'];
			if ($rarr['req_tech_id']>0) $b_req[$rarr['obj_id']]['t'][$rarr['req_tech_id']]=$rarr['req_level'];
		}

		if ($mode=="ships")
			$sl = new ShipList($cp->id,$cu->id);

		// Wenn Kategorien vorhanden sind (Gebäude, Forschungen)
		if (defined("TYPES_TBL") && defined("ITEM_TYPE_FLD") && defined("TYPE_ORDER_FLD"))
		{
			$tres = dbquery("
			SELECT 
				* 
			FROM 
				".TYPES_TBL." 
			ORDER BY 
				".TYPE_ORDER_FLD." ASC;");
			while ($tarr=mysql_fetch_array($tres))
			{
				tableStart($tarr[TYPE_NAME_FLD]);

				if (ITEM_RACE_FLD!="")
				{
					$res = dbquery("
					SELECT 
						* 
					FROM 
						".ITEMS_TBL." 
					WHERE 
						".ITEM_SHOW_FLD."=1 
						AND 
						(
							".ITEM_RACE_FLD."=0 
							OR ".ITEM_RACE_FLD."=".$cu->raceId."
						) 
						AND ".ITEM_TYPE_FLD."=".$tarr[TYPE_ID_FLD]." 
					ORDER BY 
						".ITEM_ORDER_FLD.";
					");
				}
				else
				{
					$res = dbquery("
					SELECT 
						* 
					FROM 
						".ITEMS_TBL." 
					WHERE 
						".ITEM_SHOW_FLD."=1 
						AND ".ITEM_TYPE_FLD."=".$tarr[TYPE_ID_FLD]." 
					ORDER BY 
						".ITEM_ORDER_FLD."
					;");
				}
				
				$cntr=0;
				if (mysql_num_rows($res)>0)
				{
					while ($arr=mysql_fetch_array($res))
					{
						// Make sure epic special ships are only shown when already built
						$show = true;
						if ($mode=="ships" && $arr['special_ship']==1)
						{
							if ($sl->count($arr[ITEM_ID_FLD])==0)
								$show = false;
						}
						
						if ($show)
						{
							if(isset($b_req[$arr[ITEM_ID_FLD]]['b']))
							{
								$b_cnt = count($b_req[$arr[ITEM_ID_FLD]]['b']);
							}
							else
							{
								$b_cnt = 0;
							}
							
							if(isset($b_req[$arr[ITEM_ID_FLD]]['t']))
							{
								$t_cnt = count($b_req[$arr[ITEM_ID_FLD]]['t']);
							}
							else
							{
								$t_cnt = 0;
							}
							
							if ($b_cnt + $t_cnt>0)
							{
								echo "<tr><td width=\"200\" rowspan=\"".($b_cnt + $t_cnt)."\"><b>".$arr[ITEM_NAME_FLD]."</b> ".helpLink(HELP_URL."&amp;id=".$arr[ITEM_ID_FLD])."";
							}
							else
							{
								echo "<tr><td width=\"200\"><b>".$arr[ITEM_NAME_FLD]."</b> ".helpLink(HELP_URL."&amp;id=".$arr[ITEM_ID_FLD])."";
							}
							
							if (ITEM_RACE_FLD!="" && $arr[ITEM_RACE_FLD]>0)
							{
								echo "<br/>".$race[$arr[ITEM_RACE_FLD]]."</td>";
							}
							else
							{
								echo "</td>";
							}
	
							$using_something=0;
							if (isset($b_req[$arr[ITEM_ID_FLD]]['b']) && count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
							{
								$cnt=0;
								foreach ($b_req[$arr[ITEM_ID_FLD]]['b'] as $b=>$l)
								{
									if ($cnt==0 && count($b_req[$arr[ITEM_ID_FLD]]['b'])>1) 
									{
										$bstyle="border-bottom:none;";
									}
									elseif (
										($cnt>0 
										&& $cnt < count($b_req[$arr[ITEM_ID_FLD]]['b'])-1) 
									|| 
										(isset($b_req[$arr[ITEM_ID_FLD]]['t']) 
										&& count($b_req[$arr[ITEM_ID_FLD]]['t'])>0))
									{
										$bstyle="border-top:none;border-bottom:none;";
									}
									elseif ($cnt!=0)
									{
										$bstyle="border-top:none;";
									}
									else
									{
										$bstyle="";
									}
	
									if (!isset($buildlist[$b]) || $buildlist[$b]<$l)
									{
										echo "<td style=\"color:#f00;border-right:none;".$bstyle."\" width=\"130\">".$bu_name[$b]."</td><td style=\"color:#f00;border-left:none;".$bstyle."\" width=\"70\">Stufe ".$l."</td></tr>";
									}
									else
									{
										echo "<td style=\"color:#0f0;border-right:none;".$bstyle."\" width=\"130\">".$bu_name[$b]."</td><td style=\"color:#0f0;border-left:none;".$bstyle."\" width=\"70\">Stufe $l</td></tr>";
									}
									$cnt++;
								}
								$using_something=1;
							}
							
							if (isset($b_req[$arr[ITEM_ID_FLD]]['t']) && count($b_req[$arr[ITEM_ID_FLD]]['t'])>0)
							{
								$cnt=0;
								foreach ($b_req[$arr[ITEM_ID_FLD]]['t'] as $b=>$l)
								{
									if ($cnt==0 && count($b_req[$arr[ITEM_ID_FLD]]['t'])>1 && isset($b_req[$arr[ITEM_ID_FLD]]['b']) && count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
									{
										$bstyle="border-top:none;border-bottom:none;";
									}
									elseif ($cnt==0 && count($b_req[$arr[ITEM_ID_FLD]]['t'])>1)
									{
										$bstyle="border-bottom:none;";
									}
									elseif (($cnt>0 && $cnt<count($b_req[$arr[ITEM_ID_FLD]]['t'])-1))
									{
										$bstyle="border-top:none;border-bottom:none;";
									}
									elseif ($cnt!=0)
									{
										$bstyle="border-top:none;";
									}
									elseif (count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
									{
										$bstyle="border-top:none;";
									}
									else
									{
										$bstyle="";
									}
	
	
									if (!isset($techlist[$b]) || $techlist[$b]<$l)
									{
										echo "<td style=\"color:#f00;border-right:none;".$bstyle."\" width=\"130\">".$te_name[$b]."</td><td style=\"color:#f00;border-left:none;".$bstyle."\" width=\"70\">Stufe ".$l."</td></tr>";
									}
									else
									{
										echo "<td style=\"color:#0f0;border-right:none;".$bstyle."\" width=\"130\">".$te_name[$b]."</td><td style=\"color:#0f0;border-left:none;".$bstyle."\" width=\"70\">Stufe ".$l."</td></tr>";
									}
									$cnt++;
								}
								$using_something=1;
							}
							
							if ($using_something==0)
							{
								echo "<td colspan=\"2\"><i>Keine Voraussetzungen n&ouml;tig</i></td></tr>";
							}
							$cntr++;
						}
					}
					if ($cntr==0)
					{
						echo "<tr><td colspan=\"2\">Keine Infos vorhanden!</td></tr>";
					}
				}
				else
					echo "<tr><td align=\"center\" colspan=\"3\">".NO_ITEMS_MSG."</td></tr>";
				
				tableEnd();
			}
		}
		// Wenn keine Kategorien vorhanden sind (Schiffe, Verteidigungsanlagen)
		else
		{
			tableStart();
			if (ITEM_RACE_FLD!="")
			{
				$res = dbquery("
				SELECT 
					* 
				FROM 
					".ITEMS_TBL." 
				WHERE 
					".ITEM_SHOW_FLD."=1 
					AND 
					(
						".ITEM_RACE_FLD."=0 
						OR ".ITEM_RACE_FLD."=".$cu->raceId."
					) 
					ORDER BY 
						".ITEM_ORDER_FLD.";
				");
			}
			else
			{
				$res = dbquery("
				SELECT 
					* 
				FROM 
					".ITEMS_TBL." 
				WHERE 
					".ITEM_SHOW_FLD."=1 
				ORDER BY 
					".ITEM_ORDER_FLD.";");
			}
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_array($res))
				{
					if (count($b_req[$arr[ITEM_ID_FLD]]['b'])+count($b_req[$arr[ITEM_ID_FLD]]['t'])>0)
					{
						echo "<tr><td width=\"200\" rowspan=\"".(count($b_req[$arr[ITEM_ID_FLD]]['b'])+count($b_req[$arr[ITEM_ID_FLD]]['t']))."\"><b>".$arr[ITEM_NAME_FLD]."</b> ".helpLink(HELP_URL."&amp;id=".$arr[ITEM_ID_FLD])."</td>";
					}
					else
					{
						echo "<tr><td width=\"200\"><b>".$arr[ITEM_NAME_FLD]."</b> ".helpLink(HELP_URL."&amp;id=".$arr[ITEM_ID_FLD])."</td>";
					}
					$using_something=0;
					
					if (isset($b_req[$arr[ITEM_ID_FLD]]['b']) && count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
					{
						$cnt=0;
						foreach ($b_req[$arr[ITEM_ID_FLD]]['b'] as $b=>$l)
						{
							if ($cnt==0 && count($b_req[$arr[ITEM_ID_FLD]]['b'])>1)
							{
								$bstyle="border-bottom:none;";
							}
							elseif (($cnt>0 && $cnt<count($b_req[$arr[ITEM_ID_FLD]]['b'])-1) || count($b_req[$arr[ITEM_ID_FLD]]['t'])>0)
							{
								$bstyle="border-top:none;border-bottom:none;";
							}
							elseif ($cnt!=0)
							{
								$bstyle="border-top:none;";
							}
							else
							{
								$bstyle="";
							}

							if (!isset($buildlist[$b]) || $buildlist[$b]<$l)
							{
								echo "<td style=\"color:#f00;border-right:none;$bstyle\" width=\"130\">".$bu_name[$b]."</td><td style=\"color:#f00;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
							}
							else
							{
								echo "<td style=\"color:#0f0;border-right:none;$bstyle\" width=\"130\">".$bu_name[$b]."</td><td style=\"color:#0f0;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
							}
							$cnt++;
						}
						$using_something=1;
					}
					
					if (isset($b_req[$arr[ITEM_ID_FLD]]['t']) && count($b_req[$arr[ITEM_ID_FLD]]['t'])>0)
					{
						$cnt=0;
						foreach ($b_req[$arr[ITEM_ID_FLD]]['t'] as $b=>$l)
						{
							if ($cnt==0 && count($b_req[$arr[ITEM_ID_FLD]]['t'])>1 && count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
							{
								$bstyle="border-top:none;border-bottom:none;";
							}
							elseif ($cnt==0 && count($b_req[$arr[ITEM_ID_FLD]]['t'])>1)
							{
								$bstyle="border-bottom:none;";
							}
							elseif (($cnt>0 && $cnt<count($b_req[$arr[ITEM_ID_FLD]]['t'])-1))
							{
								$bstyle="border-top:none;border-bottom:none;";
							}
							elseif ($cnt!=0)
							{
								$bstyle="border-top:none;";
							}
							elseif (count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
							{
								$bstyle="border-top:none;";
							}
							else
							{
								$bstyle="";
							}


							if (!isset($techlist[$b]) || $techlist[$b]<$l)
							{
								echo "<td style=\"color:#f00;border-right:none;$bstyle\" width=\"130\">".$te_name[$b]."</td><td style=\"color:#f00;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
							}
							else
							{
								echo "<td style=\"color:#0f0;border-right:none;$bstyle\" width=\"130\">".$te_name[$b]."</td><td style=\"color:#0f0;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
							}
							$cnt++;
						}
						$using_something=1;
					}
					if ($using_something==0)
						echo "<td colspan=\"2\"><i>Keine Voraussetzungen n&ouml;tig</i></td></tr>";
				}
			}
			else
				echo "<tr><td align=\"center\" colspan=\"3\">".NO_ITEMS_MSG."</td></tr>";
			tableEnd();
	}
	
	}
	else
	{
		$startItem = 6;
		
		echo "<select onchange=\"xajax_reqInfo(this.value,'b')\">
		<option value=\"0\">Gebäude wählen...</option>";
		$bures = dbquery("SELECT building_id,building_name FROM buildings WHERE building_show=1 ORDER BY building_name;");
		while ($buarr = mysql_fetch_array($bures))
		{
			echo "<option value=\"".$buarr['building_id']."\">".$buarr['building_name']."</option>";
		}
		echo "</select> ";
		
				
		echo "<select onchange=\"xajax_reqInfo(this.value,'t')\">
		<option value=\"0\">Technologie wählen...</option>";
		$teres = dbquery("SELECT tech_id,tech_name FROM technologies WHERE tech_show=1 ORDER BY tech_name;");
		while ($tearr = mysql_fetch_array($teres))
		{
			echo "<option value=\"".$tearr['tech_id']."\">".$tearr['tech_name']."</option>";
		}	
		echo "</select> ";
	
		echo "<select onchange=\"xajax_reqInfo(this.value,'s')\">
		<option value=\"0\">Schiff wählen...</option>";
		$teres = dbquery("SELECT ship_id,ship_name FROM ships WHERE ship_show=1 AND special_ship=0 ORDER BY ship_name;");
		while ($tearr = mysql_fetch_array($teres))
		{
			echo "<option value=\"".$tearr['ship_id']."\">".$tearr['ship_name']."</option>";
		}	
		echo "</select> ";
		
		echo "<select onchange=\"xajax_reqInfo(this.value,'d')\">
		<option value=\"0\">Verteidigung wählen...</option>";
		$teres = dbquery("SELECT def_id,def_name FROM defense WHERE def_show=1 ORDER BY def_name;");
		while ($tearr = mysql_fetch_array($teres))
		{
			echo "<option value=\"".$tearr['def_id']."\">".$tearr['def_name']."</option>";
		}			
		echo "</select><br/><br/>";
		
		iBoxStart("Grafische Darstellung");
		showTechTree("b",$startItem);
		iBoxEnd();
		
	}
}
?>
