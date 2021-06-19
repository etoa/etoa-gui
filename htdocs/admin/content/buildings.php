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
	// 	Dateiname: buildings.php
	// 	Topic: Gebäudeverwaltung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	//
	// Kategorien bearbeiten
	//

use Doctrine\DBAL\Connection;

/** @var Connection */
$conn = $app['db'];

if ($sub=="prices")
	{
		echo "<h1>Preisrechner</h1>";
		echo "<script type=\"text/javascript\">
		function showPrices()
		{
			xajax_buildingPrices(
			document.getElementById('c1_id').options[document.getElementById('c1_id').selectedIndex].value,
			document.getElementById('c1_level').options[document.getElementById('c1_level').selectedIndex].value
			)
		}

		function showTotalPrices()
		{
			xajax_totalBuildingPrices(xajax.getFormValues('totalCosts'));
		}
		</script>";

		$buildingNames = buildingNames($conn);

		echo "<h2>(Aus)baukosten (von Stufe x-1 auf Stufe x)</h2>";
		echo "<table class=\"tb\">
			<tr>
				<th>Gebäude</th>
				<th>Stufe</th>
				<th>Zeit</th>
				<th>".RES_METAL."</th>
				<th>".RES_CRYSTAL."</th>
				<th>".RES_PLASTIC."</th>
				<th>".RES_FUEL."</th>
				<th>".RES_FOOD."</th>
				<th>Energie</th>
			</tr>";
		echo "<tr>
		<td><select id=\"c1_id\" onchange=\"showPrices()\">";
		foreach ($buildingNames as $key => $value)
		{
			echo "<option value=\"".$key."\">".$value."</option>";
		}
		echo "</select></td>
		<td><select id=\"c1_level\" onchange=\"showPrices()\">";
		for ($x=1; $x <= 40; $x++)
		{
			echo "<option value=\"".($x-1)."\">".$x."</option>";
		}
		echo "</select></td>
		<td id=\"c1_time\">-</td>
		<td id=\"c1_metal\">-</td>
		<td id=\"c1_crystal\">-</td>
		<td id=\"c1_plastic\">-</td>
		<td id=\"c1_fuel\">-</td>
		<td id=\"c1_food\">-</td>
		<td id=\"c1_power\">-</td>
		";
		echo "</tr></table>";

		echo "<h2>Totale Kosten</h2>
		<form action=\"?page=$page&amp;sub=$sub\" method=\"post\" id=\"totalCosts\">";
		ecHo "<table class=\"tb\">
		<tr>
			<th>Gebäude</th>
			<th>Level</th>
			<th>".RES_METAL."</th>
			<th>".RES_CRYSTAL."</th>
			<th>".RES_PLASTIC."</th>
			<th>".RES_FUEL."</th>
			<th>".RES_FOOD."</th>
		</tr>";
		foreach ($buildingNames as $key => $value)
		{
			echo "<tr>
			<td>".$value."</td>
			<td>Level 0 bis <select name=\"b_lvl[".$key."]\" onchange=\"showTotalPrices()\">";
			for ($x=0; $x <= 40; $x++)
			{
				echo "<option value=\"".$x."\">".$x."</option>";
			}
			echo "</select></td>
			<td id=\"b_metal_".$key."\">-</td>
			<td id=\"b_crystal_".$key."\">-</td>
			<td id=\"b_plastic_".$key."\">-</td>
			<td id=\"b_fuel_".$key."\">-</td>
			<td id=\"b_food_".$key."\">-</td>
			</tr>";
		}
		echo "<tr><td style=\"height:2px;\" colspan=\"7\"></tr>";
		echo "<tr>
			<td colspan=\"2\">Total</td>
			<td id=\"t_metal\">-</td>
			<td id=\"t_crystal\">-</td>
			<td id=\"t_plastic\">-</td>
			<td id=\"t_fuel\">-</td>
			<td id=\"t_food\">-</td>
		</tr>";
		echo "</table></form>";
	}

	//
	// Gebäudepunkte
	//
	elseif ($sub=="points")
	{
		echo "<h1>Geb&auml;udepunkte</h1>";
		echo "<h2>Geb&auml;udepunkte neu berechnen</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
		if (isset($_POST['recalc']) && $_POST['recalc']!="")
		{
			echo MessageBox::ok("", Ranking::calcBuildingPoints());
		}
		echo "Nach jeder &Auml;nderung an den Geb&auml;uden m&uuml;ssen die Geb&auml;udepunkte neu berechnet werden.<br/><br/>
		Diese Aktion kann eine Weile dauern! ";
		echo "<input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";

		echo "<h2>Geb&auml;udepunkte</h2>";
		$buildingNames = buildingNames($conn);
		if (count($buildingNames) > 0)
		{
			echo "<table class=\"tb\">";
			foreach ($buildingNames as $key => $value)
			{
				echo "<tr><th>".$value."</th><td style=\"width:70%\"><table class=\"tb\">";
				$pointsData = fetchPointsForBuilding($conn, $key);
				if (count($pointsData) > 0)
				{
					$cnt=0;
					foreach ($pointsData as $parr)
					{
						if ($cnt==0) {
							echo "<tr>";
						}
						echo "<th>".$parr['bp_level']."</th><td>".$parr['bp_points']."</td>";
						if ($cnt=="3")
						{
							echo "</tr>";
							$cnt=0;
						}
						else {
							$cnt++;
						}
					}
					if ($cnt!=0)
					{
						for ($x=$cnt;$x<4;$x++)
						{
							echo "<td colspan=\"2\"></td>";
						}
						echo "</tr>";
					}
				}
				echo "</table></td></tr>";
			}
			echo "</table>";
		}
	}

	//
	// Kategorien bearbeiten
	//
	elseif ($sub=="type")
	{
		simple_form("building_types", $twig);
	}

	//
	// Gebäude bearbeiten
	//
	elseif ($sub=="data")
	{
		advanced_form("buildings", $twig);
	}

	//
	// Voraussetzungen
	//
	elseif ($sub=="req")
	{
		define('TITLE',"Gebäudeanforderungen");
		define('ITEMS_TBL',"buildings");
		define('TYPES_TBL',"building_types");
		define('REQ_TBL',"building_requirements");
		define('ITEM_ID_FLD',"building_id");
		define('ITEM_NAME_FLD',"building_name");
		define('ITEM_ENABLE_FLD',"1");
		define('ITEM_ORDER_FLD',"building_type_id,building_order,building_name");

		define("ITEM_IMAGE_PATH",IMAGE_PATH."/buildings/building<DB_TABLE_ID>_small.".IMAGE_EXT);

		include("inc/requirements.inc.php");
	}

	//
	// Liste
	//
	else
	{
		$twig->addGlobal('title', 'Gebäudeliste');

		$buildTypes = Building::getBuildTypes();

		$build_colors = [];
		$build_colors[0]="inherit";
		$build_colors[1]="red";
		$build_colors[2]="orange";
		$build_colors[3]="#0f0";
		$build_colors[4]="orange";

		if (isset($_POST['save']))
		{
			updateBuildingListEntry($conn,
				$_POST['buildlist_id'],
				$_POST['buildlist_current_level'],
				$_POST['buildlist_build_type'],
				$_POST['buildlist_build_start_time'],
				$_POST['buildlist_build_end_time']);
		}
		elseif (isset($_POST['del']))
		{
			deleteBuildingListEntry($conn, $_POST['buildlist_id']);
		}

		//
		// Datensatz bearbeiten
		//
		if (isset($_GET['action']) && $_GET['action']=="edit")
		{
			echo "<h2>Datensatz bearbeiten</h2>";
			$arr = fetchBuildingListEntry($conn, $_GET['buildlist_id']);
			if ($arr != null)
			{
				echo "<form action=\"?page=$page&sub=$sub&amp;action=search\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"buildlist_id\" value=\"".$arr['buildlist_id']."\" />";
				echo "<table class=\"tb\">";
				echo "<tr><th>ID</th><td>".$arr['buildlist_id']."</td></tr>";
				echo "<tr><th>Planet</th><td>".$arr['planet_name']."</td></tr>";
				echo "<tr><th>Spieler</th><td>".$arr['user_nick']."</td></tr>";
				echo "<tr><th>Geb&auml;ude</th><td>".$arr['building_name']."</td></tr>";
				echo "<tr><th>Level</th><td><input type=\"text\" name=\"buildlist_current_level\" value=\"".$arr['buildlist_current_level']."\" size=\"2\" maxlength=\"3\" /></td></tr>";
				echo "<tr><th>Baustatus</th><td><select name=\"buildlist_build_type\">";
				foreach ($buildTypes as $id=>$val)
				{
					echo "<option value=\"$id\"";
					if ($arr['buildlist_build_type']==$id) echo " selected=\"selected\"";
					echo ">$val</option>";
				}
				echo "</select></td></tr>";
				$bst = $arr['buildlist_build_start_time'] > 0 ? date(DATE_FORMAT, $arr['buildlist_build_start_time']) : "";
				$bet = $arr['buildlist_build_end_time'] > 0 ? date(DATE_FORMAT, $arr['buildlist_build_end_time']) : "";
				echo "<tr><th>Baustart</th><td><input type=\"text\" name=\"buildlist_build_start_time\" id=\"buildlist_build_start_time\" value=\"$bst\" size=\"20\" maxlength=\"30\" /> <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('buildlist_build_start_time').value='".date("Y-d-m h:i")."'\" /></td></tr>";
				echo "<tr><th>Bauende</th><td><input type=\"text\" name=\"buildlist_build_end_time\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
				echo "</table>";
				echo "<br/><input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />&nbsp;";
				echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" />&nbsp;";
				echo "<input type=\"submit\" value=\"Zur&uuml;ck zu den Suchergebnissen\" />&nbsp;";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" />";
				echo "</form>";
			}
			else {
				echo "Dieser Datensatz wurde gel&ouml;scht!<br/><br/><a href=\"?page=$page&amp;sub=$sub\">Neue Suche</a>";
			}
		}

		//
		// Suchergebnisse anzeigen
		//
		elseif ((isset($_POST['buildlist_search']) || isset($_POST['new']) || isset($_SESSION['search']['buildings']['query'])) && (isset($_GET['action']) &&$_GET['action']=="search"))
		{
			if (isset($_GET['query']) && $_GET['query']!="")
			{
				$qs = searchQueryDecode($_GET['query']);
				foreach($qs as $k=>$v)
				{
					$_POST[$k]=$v;
				}
				$_SESSION['search']['buildings']['query'] = null;
				$_SESSION['search']['buildings']['parameters'] = null;
			}

			echo "<h2>Suchergebnisse</h2>";

			if (isset($_SESSION['search']['buildings']['query']) && isset($_SESSION['search']['buildings']['parameters'])) {
				$res = $conn
					->executeQuery(
						$_SESSION['search']['buildings']['query'],
						$_SESSION['search']['buildings']['parameters']
					);
			} else {
				$qry = $conn
					->createQueryBuilder()
					->select('*')
					->from('buildlist', 'l')
					->innerJoin('l', 'planets', 'p', 'p.id = l.buildlist_entity_id')
					->innerJoin('l', 'users', 'u', 'u.user_id = l.buildlist_user_id')
					->innerJoin('l', 'buildings', 'b', 'b.building_id = l.buildlist_building_id')
					->groupBy('buildlist_id')
					->orderBy('buildlist_user_id')
					->addOrderBy('buildlist_entity_id')
					->addOrderBy('building_type_id')
					->addOrderBy('building_order')
					->addOrderBy('building_name');

				if ($_POST['entity_id'] != "") {
					$qry->andWhere('id = :id')
						->setParameter('id', $_POST['entity_id']);
				}
				if ($_POST['planet_name'] != "") {
					$qry = fieldComparisonQuery($qry, $_POST, 'planet_name', 'planet_name');
				}
				if ($_POST['user_id'] != "") {
					$qry->andWhere('user_id = :userid')
						->setParameter('userid', $_POST['user_id']);
				}
				if ($_POST['user_nick'] != "") {
					$qry = fieldComparisonQuery($qry, $_POST, 'user_nick', 'user_nick');
				}
				if ($_POST['building_id']!="") {
					$qry->andWhere('building_id = :building')
						->setParameter('building', $_POST['building_id']);
				}

				$res = $qry->execute();
			}
			$data = $res->fetchAllAssociative();
			if (count($data) > 0)
			{
				if (isset($qry)) {
					$_SESSION['search']['buildings']['query'] = $qry->getSQL();
					$_SESSION['search']['buildings']['parameters'] = $qry->getParameters();
				}

				echo count($data)." Datens&auml;tze vorhanden<br/><br/>";
				if (count($data) > 20) {
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" /><br/><br/>";
				}

				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<th class=\"tbltitle\">Planet</th>";
				echo "<th class=\"tbltitle\">Spieler</th>";
				echo "<th class=\"tbltitle\">Geb&auml;ude</th>";
				echo "<th class=\"tbltitle\">Stufe</th>";
				echo "<th class=\"tbltitle\">Status</th>";
				echo "<th></th>";
				echo "</tr>";
				foreach ($data as $arr)
				{
					echo "<tr>";
					echo "<td class=\"tbldata\"><a href=\"?page=galaxy&amp;sub=edit&amp;id=".$arr['buildlist_entity_id']."\" title=\"".$arr['planet_name']."\">".cut_string($arr['planet_name'] != '' ? $arr['planet_name'] : 'Unbenannt',11)."</a> [".$arr['id']."]</td>";

					echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['buildlist_user_id']."\" title=\"".$arr['user_nick']."\">".cut_string($arr['user_nick'],11)."</a></td>";
					$style=" ";

					echo "<td class=\"tbldata\">".$arr['building_name']."</td>";
					echo "<td class=\"tbldata\">".nf($arr['buildlist_current_level'])."</td>";
					echo "<td class=\"tbldata\" style=\"background:".$build_colors[$arr['buildlist_build_type']]."\">".$buildTypes[$arr['buildlist_build_type']]."</td>";
					echo "<td class=\"tbldata\">".edit_button("?page=$page&amp;sub=$sub&amp;action=edit&amp;buildlist_id=".$arr['buildlist_id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Zur&uuml;ck\" />";
			}
		}

		//
		// Suchformular
		//
		else
		{
			$buildingNames = buildingNames($conn);

			echo '<div class="tabs">
			<ul>
				<li><a href="#tabs-1">Schnellsuche</a></li>
				<li><a href="#tabs-2">Erweiterte Suche</a></li>
			</ul>
			<div id="tabs-1">';

			// Hinzufügen
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\" name=\"selector\">";

			//Sonnensystem
			echo '<table class="tb">';
			echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
			<select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor X</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor Y</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle X</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle Y</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";

			//User
			echo "<tr><th class=\"tbltitle\"><i>oder</i> User</th><td class=\"tbldata\">";
			echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onchange=\"xajax_searchUserList(this.value,'showBuildingsOnPlanet');\" onkeyup=\"xajax_searchUserList(this.value,'showBuildingsOnPlanet');\"><br>
			<div id=\"userlist\">&nbsp;</div>";
			echo "</td></tr>";

			//Planeten
			echo "<tr><th class=\"tbltitle\">Planeten</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User w&auml;hlen...</td></tr>";

			//Gebäude Hinzufügen
			echo "<tr><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
			<input type=\"text\" name=\"buildlist_current_level\" value=\"1\" size=\"7\" maxlength=\"10\" />
			<select name=\"building_id\">";
			foreach ($buildingNames as $key => $value)
			{
				echo "<option value=\"".$key."\">".$value."</option>";
			}
			echo "</select> &nbsp;
			<input type=\"button\" onclick=\"showLoaderPrepend('shipsOnPlanet');xajax_addBuildingToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" />
			<input type=\"button\" onclick=\"showLoaderPrepend('shipsOnPlanet');xajax_addAllBuildingToPlanet(xajax.getFormValues('selector'),".count($buildingNames).");\" value=\"Alle hinzuf&uuml;gen\" /></td></tr>";

			//Gebäude wählen
			echo "<tr><td class=\"tbldata\" id=\"shipsOnPlanet\" colspan=\"2\">Planet w&auml;hlen...</td></tr>";
			tableEnd();
			echo "</form>";

			//Focus
			echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').focus();</script>";

			//Add User
			if (searchQueryArray($sa,$so))
			{
				if (isset($sa['user_nick']))
				{
					echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').value=\"".$sa['user_nick'][1]."\";xajax_searchUserList('".$sa['user_nick'][1]."','showBuildingsOnPlanet');</script>";
				}
			}

			echo '</div><div id="tabs-2">';

			$_SESSION['search']['buildings']['query'] = null;
			$_SESSION['search']['buildings']['parameters'] = null;

			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
			echo '<table class="tb">';
			echo "<tr><th class=\"tbltitle\">Planet ID</th><td class=\"tbldata\"><input type=\"text\" name=\"entity_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Planetname</th><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" />&nbsp;";
			echo fieldComparisonSelectBox('planet_name');
			echo "</td></tr>";
			echo "<tr><th class=\"tbltitle\">Spieler ID</th><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Spieler Nick</th><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\" />&nbsp;";
			echo fieldComparisonSelectBox('user_nick');
			echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
			echo "<tr><th class=\"tbltitle\">Geb&auml;ude</th><td class=\"tbldata\"><select name=\"building_id\"><option value=\"\"><i>---</i></option>";
			foreach ($buildingNames as $key => $value)
			{
				echo "<option value=\"".$key."\">".$value."</option>";
			}
			echo "</select></td></tr>";
			tableEnd();
			echo "<br/><input type=\"submit\" name=\"buildlist_search\" value=\"Suche starten\" />";
			echo "</form>";

			echo '
				</div>
			</div>';

			echo "<p>Es sind <b>".nf(numBuildingListEntries($conn))."</b> Eintr&auml;ge in der Datenbank vorhanden.</p>";
		}
	}

	function numBuildingListEntries(Connection $conn): int
	{
		return (int) $conn
			->createQueryBuilder()
			->select('COUNT(buildlist_id)')
			->from('buildlist')
			->execute()
			->fetchOne();
	}

	function buildingNames(Connection $conn): array
	{
		return $conn
			->createQueryBuilder()
			->select('building_id', 'building_name')
			->from('buildings')
			->orderBy('building_type_id')
			->addOrderBy('building_order')
			->addOrderBy('building_name')
			->execute()
			->fetchAllKeyValue();
	}

	function fetchBuildingListEntry(Connection $conn, int $id)
	{
		return $conn
			->createQueryBuilder()
			->select(
				'bl.buildlist_id',
				'bl.buildlist_current_level',
				'bl.buildlist_build_start_time',
				'bl.buildlist_build_end_time',
				'bl.buildlist_build_type',
				'p.planet_name',
				'u.user_nick',
				'b.building_name'
			)
			->from('buildlist', 'bl')
			->innerJoin('bl', 'planets', 'p', 'bl.buildlist_entity_id = p.id')
			->innerJoin('bl', 'users', 'u', 'bl.buildlist_user_id = u.user_id')
			->innerJoin('bl', 'buildings', 'b', 'bl.buildlist_building_id = b.building_id AND bl.buildlist_id = :id')
			->setParameter('id', $id)
			->execute()
			->fetchAssociative();
	}

	function updateBuildingListEntry(Connection $conn, int $id, int $level, string $type, string $start, string $end): bool
	{
		$affected = $conn
			->createQueryBuilder()
			->update('buildlist')
			->set('buildlist_current_level', ':level')
			->set('buildlist_build_type', ':type')
			->set('buildlist_build_start_time', 'UNIX_TIMESTAMP(:start)')
			->set('buildlist_build_end_time', 'UNIX_TIMESTAMP(:end)')
			->where('buildlist_id = :id')
			->setParameters([
				'level' => $level,
				'type' => $type,
				'start' => $start,
				'end' => $end,
				'id' => $id,
			])
			->execute();

		return (int) $affected > 0;
	}

	function deleteBuildingListEntry(Connection $conn, int $id): bool
	{
		$affected = $conn
			->createQueryBuilder()
			->delete('buildlist')
			->where('buildlist_id = :id')
			->setParameter('id', $id)
			->execute();
		return (int) $affected > 0;
	}

	function fetchPointsForBuilding(Connection $conn, int $buildingId): array
	{
		return $conn
			->createQueryBuilder()
			->select('bp_level', 'bp_points')
			->from('building_points')
			->where('bp_building_id = :buildingId')
			->orderBy('bp_level' ,'ASC')
			->setParameter('buildingId', $buildingId)
			->execute()
			->fetchAllAssociative();
	}
