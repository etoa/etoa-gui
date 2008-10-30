<?PHP


		//
		// Anforderungen
		//
		echo "<h1>".TITLE."</h1>";

		if (isset($_POST['submit_changes']))
		{
			// Gebäudeänderungen speichern
			foreach ($_POST['building_level'] as $id=>$val)
			{
				if ($_POST['building_level'][$id]<1)
					dbquery("DELETE FROM ".REQ_TBL." WHERE req_id=$id;");
				else
					dbquery("UPDATE ".REQ_TBL." SET req_req_building_id=$val,req_req_building_level=".$_POST['building_level'][$id]." WHERE req_id=$id;");
			}
			// Technologieänderungen speichern
			foreach ($_POST['tech_level'] as $id=>$val)
			{
				if ($_POST['tech_level'][$id]<1)
					dbquery("DELETE FROM ".REQ_TBL." WHERE req_id=$id;");
				else
					dbquery("UPDATE ".REQ_TBL." SET req_req_tech_id=$val,req_req_tech_level=".$_POST['tech_level'][$id]." WHERE req_id=$id;");
			}
			ok_msg("Änderungen übernommen!");
		}

		// Gebäudeverknüpfung speichern
		if (isset($_POST['add_building']))
		{
			foreach ($_POST['add_building'] as $itemId => $tmp)
			{
				if ($_POST['new_b_item_id'][$itemId]>0)
				{
					if (mysql_num_rows(dbquery("SELECT req_id FROM ".REQ_TBL." WHERE ".REQ_ITEM_FLD."=".$itemId." AND req_req_building_id=".$_POST['new_b_item_id'][$itemId].";"))==0)
					{
						dbquery("INSERT INTO ".REQ_TBL." (".REQ_ITEM_FLD.",req_req_building_id,req_req_building_level) VALUES ('".$itemId."','".$_POST['new_b_item_id'][$itemId]."','".$_POST['new_b_item_level'][$itemId]."');");
						ok_msg("Bedingung hinzugefügt!");
					}
					else
						err_msg("Diese Geb&auml;udeverkn&uuml;pfung existiert bereits!");
				}
				else
					err_msg("Kein verkn&uuml;pfendes Geb&auml;ude ausgew&auml;hlt!");
				break;
			}
		}

		// Technologieverknüpfung speicher
		if (isset($_POST['add_tech']))
		{
			foreach ($_POST['add_tech'] as $itemId => $tmp)
			{			
				if ($_POST['new_t_item_id'][$itemId]>0)
				{
					if (mysql_num_rows(dbquery("SELECT req_id FROM ".REQ_TBL." WHERE ".REQ_ITEM_FLD."=".$itemId." AND req_req_tech_id=".$_POST['new_t_item_id'][$itemId].";"))==0)
					{
						dbquery("INSERT INTO ".REQ_TBL." (".REQ_ITEM_FLD.",req_req_tech_id,req_req_tech_level) VALUES ('".$itemId."','".$_POST['new_t_item_id'][$itemId]."','".$_POST['new_t_item_level'][$itemId]."');");
						ok_msg("Bedingung hinzugefügt!");
					}
					else
						err_msg("Fehler! Diese Forschungsverkn&uuml;pfung existiert bereits!");
				}
				else
					err_msg("Fehler! Keine verkn&uuml;pfende Forschung ausgew&auml;hlt!");
				break;
			}
		}

		// Gebäudeverknüpfungen löschen
		if (isset($_POST['del_building']))
		{
			if (count($_POST['del_building'])>0)
			{
				foreach ($_POST['del_building'] as $req_building_id=>$req_req_building_id)
				{
					foreach ($req_req_building_id as $key=>$val)
					{
						dbquery("DELETE FROM ".REQ_TBL." WHERE ".REQ_ITEM_FLD."=$req_building_id AND req_req_building_id=$key;");
					}
				}
				ok_msg("Bedingung gelöscht!");
			}
		}

		// Technologieknüpfungen löschen
		if (isset($_POST['del_tech']))
		{
			if (count($_POST['del_tech'])>0)
			{
				foreach ($_POST['del_tech'] as $req_building_id=>$req_req_tech_id)
				{
					foreach ($req_req_tech_id as $key=>$val)
					{
						dbquery("DELETE FROM ".REQ_TBL." WHERE ".REQ_ITEM_FLD."=$req_building_id AND req_req_tech_id=$key;");
					}
				}
				ok_msg("Bedingung gelöscht!");
			}
		}


		// Lade Gebäude- & Technologienamen
		$bures = dbquery("SELECT building_id,building_name FROM buildings WHERE building_show=1;");
		while ($buarr = mysql_fetch_array($bures))
		{
			$bu_name[$buarr['building_id']]=$buarr['building_name'];
		}
		$teres = dbquery("SELECT tech_id,tech_name FROM technologies WHERE tech_show=1;");
		while ($tearr = mysql_fetch_array($teres))
		{
			$te_name[$tearr['tech_id']]=$tearr['tech_name'];
		}

		// Lade Anforderungen
		$rres = dbquery("SELECT * FROM ".REQ_TBL.";");
		while ($rarr = mysql_fetch_array($rres))
		{
			$b_req[$rarr[REQ_ITEM_FLD]]['i'][$rarr['req_req_building_id']]=$rarr['req_id'];
			$b_req[$rarr[REQ_ITEM_FLD]]['i'][$rarr['req_req_tech_id']]=$rarr['req_id'];
			if ($rarr['req_req_building_id']>0) $b_req[$rarr[REQ_ITEM_FLD]]['b'][$rarr['req_req_building_id']]=$rarr['req_req_building_level'];
			if ($rarr['req_req_tech_id']>0) $b_req[$rarr[REQ_ITEM_FLD]]['t'][$rarr['req_req_tech_id']]=$rarr['req_req_tech_level'];
		}

		$res = dbquery("SELECT * FROM ".ITEMS_TBL." WHERE ".ITEM_SHOW_FLD."=1 ORDER BY ".ITEM_ORDER_FLD.";");
		if (mysql_num_rows($res)>0)
		{
			if (isset($_GET['action']) && ($_GET['action']=="new_building" || $_GET['action']=="new_tech"))
				$form_addition=" disabled=\"disabled\"";

			while ($arr=mysql_fetch_array($res))
			{
				echo "<form action=\"?page=$page&sub=$sub#i".$arr[ITEM_ID_FLD]."\" method=\"post\">";
				echo "<a name=\"i".$arr[ITEM_ID_FLD]."\"></a>";
				echo "<table class=\"tb\">";
				echo "<tr><th colspan=\"4\" >".$arr[ITEM_NAME_FLD]." 
				<input type=\"submit\" class=\"button\" name=\"submit_changes\" value=\"&Auml;nderungen &uuml;bernehmen\" /></th></tr>";
				$using_something=0;

				// Gespeicherte Gebäudeanforderungen
				if (count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
				{
					$cnt=0;
					foreach ($b_req[$arr[ITEM_ID_FLD]]['b'] as $b=>$l)
					{
						if ($cnt==0)
							echo "<tr><td rowspan=\"".count($b_req[$arr[ITEM_ID_FLD]]['b'])."\"><b>Gebäude:</b></td>";
						else
							echo "<tr>";
						echo "<td class=\"tbldata\" width=\"200\" style=\"color:#ff0\">
						".$bu_name[$b]."
						</td>
						<td class=\"tbldata\" width=\"50\">
							<input type=\"text\" style=\"color:#ff0\" name=\"building_level[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" size=\"1\" maxlength=\"3\" value=\"$l\"$form_addition />
						</td>";
						echo "<td class=\"tbldata\">
							<input type=\"submit\" class=\"button\" name=\"del_building[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\" />
						</td></tr>";
						$cnt++;
					}
					$using_something=1;
					echo "<tr><td colspan=\"4\" style=\"height:2px;background:#000\"></td></tr>";
				}

				// Gespeicherte Forschungsanforderungen
				if (count($b_req[$arr[ITEM_ID_FLD]]['t'])>0)
				{
					$cnt=0;
					foreach ($b_req[$arr[ITEM_ID_FLD]]['t'] as $b=>$l)
					{
						if ($cnt==0)
							echo "<tr><td rowspan=\"".count($b_req[$arr[ITEM_ID_FLD]]['t'])."\"><b>Techs:</b></td>";
						else
							echo "<tr>";
						
						echo "<td class=\"tbldata\" width=\"200\" style=\"color:#ff0\">
						".$te_name[$b]."
						</td>
						<td class=\"tbldata\" width=\"50\">
						<input type=\"text\" style=\"color:#ff0\" name=\"tech_level[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" size=\"1\" maxlength=\"3\" value=\"$l\"$form_addition /></td>";
						echo "<td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"del_tech[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\"$form_addition /></td></tr>";
						$cnt++;
					}
					$using_something=1;
					echo "<tr><td colspan=\"4\" style=\"height:2px;background:#000\"></td></tr>";
				}

				if ($using_something==0)
					echo "<tr><td colspan=\"4\" style=\"color:#f90\" class=\"tlbdata\">Keine Voraussetzungen</td></tr>";

				
				// Neue Gebäudeanforderung
				echo "<tr>
				<td rowspan=\"2\"><b>Hinzufügen:</b></td>
				<td class=\"tbldata\" width=\"200\">
				<select name=\"new_b_item_id[".$arr[ITEM_ID_FLD]."]\">";
				echo "<option value=\"\" style=\"font-style:italic;\">Geb&auml;ude w&auml;hlen</option>";
				foreach ($bu_name as $key=>$val)
				{
					if ($key!=$arr[ITEM_ID_FLD])
						echo "<option value=\"$key\">$val</option>";
				}
				echo "</select></td>
				<td class=\"tbldata\">
					<input type=\"text\" name=\"new_b_item_level[".$arr[ITEM_ID_FLD]."]\" size=\"1\" maxlength=\"3\" value=\"1\" /></td>";
				echo "<td class=\"tbldata\">
					<input type=\"submit\" class=\"button\" name=\"add_building[".$arr[ITEM_ID_FLD]."]\" value=\"Hinzufügen\" /></td></tr>";
				
				
				// Neue Forschungsanforderung
				echo "<tr><td class=\"tbldata\" width=\"200\">
				<select name=\"new_t_item_id[".$arr[ITEM_ID_FLD]."]\">";
				echo "<option value=\"\" style=\"font-style:italic;\">Technologie w&auml;hlen</option>";
				foreach ($te_name as $key=>$val)
				{
					echo "<option value=\"$key\">$val</option>";
				}
				echo "</select></td><td class=\"tbldata\">
				<input type=\"text\" name=\"new_t_item_level[".$arr[ITEM_ID_FLD]."]\" size=\"1\" maxlength=\"3\" value=\"1\" /></td>";
				echo "<td class=\"tbldata\">
				<input type=\"submit\" class=\"button\" name=\"add_tech[".$arr[ITEM_ID_FLD]."]\" value=\"Hinzufügen\" /></td></tr>";


				echo "</table>
				</form><br/>";
			}
			//if ($form_addition=="")
			//	echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit_changes\" value=\"&Auml;nderungen &uuml;bernehmen\" /></p>";
		}
		else
			echo "<p class=\"infomsg\">".NO_ITEMS_MSG."</p>";


?>