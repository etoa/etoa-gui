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
	* Displays objects dependencies
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyRepository;

/** @var BuildingDataRepository $buildRepository */
	$buildRepository = $app[BuildingDataRepository::class];
	/** @var TechnologyDataRepository $technologyDataRepository */
	$technologyDataRepository = $app[TechnologyDataRepository::class];
	/** @var ShipDataRepository $shipDataRepository */
	$shipDataRepository = $app[ShipDataRepository::class];
	/** @var DefenseDataRepository $defenseRepository */
	$defenseRepository = $app[DefenseDataRepository::class];
	/** @var ShipRepository $shipRepository */
	$shipRepository = $app[ShipRepository::class];

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
		/** @var RaceDataRepository */
		$raceRepository = $app[RaceDataRepository::class];

		$raceNames = $raceRepository->getRaceNames();

		// Lade Gebäudelistenlevel
        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
        $buildlist = $buildingRepository->getBuildingLevels((int) $cp->id);

		// Lade Techlistenlevel
        /** @var TechnologyRepository $technologyRepository */
        $technologyRepository = $app[TechnologyRepository::class];
        $techlist = $technologyRepository->getTechnologyLevels($cu->getId());

		$buildingNames = $buildRepository->getBuildingNames();
		$technologyNames = $technologyDataRepository->getTechnologyNames();

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
				    $shipCounts = $shipRepository->getEntityShipCounts($cu->getId(), (int) $cp->id);
					while ($arr=mysql_fetch_array($res))
					{
						// Make sure epic special ships are only shown when already built
						$show = true;
						if ($mode=="ships" && $arr['special_ship']==1) {
							if (!isset($shipCounts[(int) $arr[ITEM_ID_FLD]])) {
                                $show = false;
                            }
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
								echo "<br/>".$raceNames[$arr[ITEM_RACE_FLD]]."</td>";
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
										echo "<td style=\"color:#f00;border-right:none;".$bstyle."\" width=\"130\">".$buildingNames[$b]."</td><td style=\"color:#f00;border-left:none;".$bstyle."\" width=\"70\">Stufe ".$l."</td></tr>";
									}
									else
									{
										echo "<td style=\"color:#0f0;border-right:none;".$bstyle."\" width=\"130\">".$buildingNames[$b]."</td><td style=\"color:#0f0;border-left:none;".$bstyle."\" width=\"70\">Stufe $l</td></tr>";
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
										echo "<td style=\"color:#f00;border-right:none;".$bstyle."\" width=\"130\">".$technologyNames[$b]."</td><td style=\"color:#f00;border-left:none;".$bstyle."\" width=\"70\">Stufe ".$l."</td></tr>";
									}
									else
									{
										echo "<td style=\"color:#0f0;border-right:none;".$bstyle."\" width=\"130\">".$technologyNames[$b]."</td><td style=\"color:#0f0;border-left:none;".$bstyle."\" width=\"70\">Stufe ".$l."</td></tr>";
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
								echo "<td style=\"color:#f00;border-right:none;$bstyle\" width=\"130\">".$buildingNames[$b]."</td><td style=\"color:#f00;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
							}
							else
							{
								echo "<td style=\"color:#0f0;border-right:none;$bstyle\" width=\"130\">".$buildingNames[$b]."</td><td style=\"color:#0f0;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
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
								echo "<td style=\"color:#f00;border-right:none;$bstyle\" width=\"130\">".$technologyNames[$b]."</td><td style=\"color:#f00;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
							}
							else
							{
								echo "<td style=\"color:#0f0;border-right:none;$bstyle\" width=\"130\">".$technologyNames[$b]."</td><td style=\"color:#0f0;border-left:none;$bstyle\" width=\"70\">Stufe $l</td></tr>";
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
		$buildingNames = $buildRepository->getBuildingNames();
		foreach ($buildingNames as $buildingId => $buildingName) {
			echo "<option value=\"".$buildingId."\">".$buildingName."</option>";
		}
		echo "</select> ";


		echo "<select onchange=\"xajax_reqInfo(this.value,'t')\">
		<option value=\"0\">Technologie wählen...</option>";
		$technologyNames = $technologyDataRepository->getTechnologyNames();
		foreach ($technologyNames as $technologyId => $technologyName) {
			echo "<option value=\"".$technologyId."\">".$technologyName."</option>";
		}
		echo "</select> ";

		echo "<select onchange=\"xajax_reqInfo(this.value,'s')\">
		<option value=\"0\">Schiff wählen...</option>";
		$shipNames = $shipDataRepository->getShipNames();
		foreach ($shipNames as $shipId => $shipName) {
			echo "<option value=\"".$shipId."\">".$shipName."</option>";
		}
		echo "</select> ";

		echo "<select onchange=\"xajax_reqInfo(this.value,'d')\">
		<option value=\"0\">Verteidigung wählen...</option>";
		$defenseNames = $defenseRepository->getDefenseNames();
		foreach ($defenseNames as $defenseId => $defenseName) {
			echo "<option value=\"".$defenseId."\">".$defenseName."</option>";
		}
		echo "</select><br/><br/>";

		iBoxStart("Grafische Darstellung");
		showTechTree("b",$startItem);
		iBoxEnd();

	}
}
