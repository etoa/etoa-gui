<?PHP


	//
	// Raketen
	//
	if ($sub=="data")
	{
		advanced_form("missiles", $twig);
	}

	//
	// RaketenVoraussetzungen
	//
	elseif ($sub=="req")
	{

		define("TITLE","Raketemanforderungen");
		define("ITEMS_TBL","missiles");
		define("REQ_TBL","missile_requirements");
		define("REQ_ITEM_FLD","obj_id");
		define("ITEM_ID_FLD","missile_id");
		define("ITEM_NAME_FLD","missile_name");
		define("ITEM_ENABLE_FLD","missile_show");
		define("ITEM_ORDER_FLD","missile_name");

		define("ITEM_IMAGE_PATH",IMAGE_PATH."/missiles/missile<DB_TABLE_ID>_small.".IMAGE_EXT);

		include("inc/requirements.inc.php");


	}

	//
	// Übersicht
	//
	else
	{
		echo "<h1>Listen bearbeiten</h1>";

			// Objekte laden
			$bres = dbquery("SELECT missile_id,missile_name FROM missiles ORDER BY missile_name;");
			$slist=array();
			while ($barr=mysql_fetch_array($bres))
			{
				$slist[$barr['missile_id']]=$barr['missile_name'];
			}

			// Hinzufügen
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\">";
			tableStart();

			//Sonnensystem
			echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
			<select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showMissilesOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor X</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showMissilesOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor Y</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showMissilesOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle X</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showMissilesOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle Y</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";

			//User
			echo "<tr><th class=\"tbltitle\"><i>oder</i> User</th><td class=\"tbldata\">";
			echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onkeyup=\"xajax_searchUserList(this.value,'showMissilesOnPlanet');\"><br>
			<div id=\"userlist\">&nbsp;</div>";
			echo "</td></tr>";

			//Planeten
			echo "<tr><th class=\"tbltitle\">Planeten</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User w&auml;hlen...</td></tr>";

			//Schiffe Hinzufügen
			echo "<tr><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
			<input type=\"text\" name=\"shiplist_count\" value=\"1\" size=\"1\" maxlength=\"3\" />
			<select name=\"ship_id\">";
			foreach ($slist as $k=>$v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select> &nbsp; <input type=\"button\" onclick=\"xajax_addMissileToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" /></td></tr>";

			//Vorhandene Schiffe
			echo "<tr><th class=\"tbltitle\">Vorhandene Raketen:</th><td class=\"tbldata\" id=\"shipsOnPlanet\">Planet w&auml;hlen...</td></tr>";
			tableEnd();
			echo "</form>";
			echo '<script type="text/javascript">document.forms[0].user_nick.focus();</script>';

			//Add User
			if (searchQueryArray($sa,$so))
			{
				if (isset($sa['user_nick']))
				{
					echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').value=\"".$sa['user_nick'][1]."\";xajax_searchUserList('".$sa['user_nick'][1]."','showMissilesOnPlanet');</script>";
				}
			}

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(missilelist_id) FROM missilelist;"));
			echo "Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/>";


	}

?>
