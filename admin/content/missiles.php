<?PHP


	//
	// Raketen
	//	
	if ($sub=="data")
	{
		advanced_form("missiles");
	}

	//
	// RaketenVoraussetzungen
	//
	elseif ($sub=="req")
	{
		define("TITLE","Raletemanforderungen");
		define("ITEMS_TBL","missiles");
		define("REQ_TBL","missile_requirements");
		define("REQ_ITEM_FLD","req_missile_id");
		define("ITEM_ID_FLD","missile_id");
		define("ITEM_NAME_FLD","missile_name");
		define("ITEM_SHOW_FLD","missile_show");
		define("ITEM_ORDER_FLD","missile_name");
		define("NO_ITEMS_MSG","In dieser Kategorie gibt es keine Raketen!");
	
		echo "<h1>".TITLE."</h1>";

		if ($_POST['submit_changes']!="")
		{
			// Gebäudeänderungen speichern			
			foreach ($_POST['building_id'] as $id=>$val)
			{
				if ($_POST['building_level'][$id]<1)
					dbquery("DELETE FROM ".REQ_TBL." WHERE req_id=$id;");
				else
					dbquery("UPDATE ".REQ_TBL." SET req_req_building_id=$val,req_req_building_level=".$_POST['building_level'][$id]." WHERE req_id=$id;");
			}			
			// Technologieänderungen speichern
			foreach ($_POST['tech_id'] as $id=>$val)
			{
				if ($_POST['tech_level'][$id]<1)
					dbquery("DELETE FROM ".REQ_TBL." WHERE req_id=$id;");
				else
					dbquery("UPDATE ".REQ_TBL." SET req_req_tech_id=$val,req_req_tech_level=".$_POST['tech_level'][$id]." WHERE req_id=$id;");
			}							
		}

		// Gebäudeverknüpfung speichern
		if ($_POST['add_building']!="")
		{
			if ($_POST['new_item_id']!="")
			{			
				if (mysql_num_rows(dbquery("SELECT req_id FROM ".REQ_TBL." WHERE ".REQ_ITEM_FLD."=".$_POST['new_id']." AND req_req_building_id=".$_POST['new_item_id'].";"))==0)
				{
					dbquery("INSERT INTO ".REQ_TBL." (".REQ_ITEM_FLD.",req_req_building_id,req_req_building_level) VALUES ('".$_POST['new_id']."','".$_POST['new_item_id']."','".$_POST['new_item_level']."');");
				}			
				else
					echo "Fehler! Diese Geb&auml;udeverkn&uuml;pfung existiert bereits!<br/><br/>";
			}
			else
				echo "Fehler! Kein verkn&uuml;pfendes Geb&auml;ude ausgew&auml;hlt!<br/><br/>";
		}
		
		// Technologieverknüpfung speicher
		if ($_POST['add_tech']!="")
		{
			if ($_POST['new_item_id']!="")
			{			
				if (mysql_num_rows(dbquery("SELECT req_id FROM ".REQ_TBL." WHERE ".REQ_ITEM_FLD."=".$_POST['new_id']." AND req_req_tech_id=".$_POST['new_item_id'].";"))==0)
				{
					dbquery("INSERT INTO ".REQ_TBL." (".REQ_ITEM_FLD.",req_req_tech_id,req_req_tech_level) VALUES ('".$_POST['new_id']."','".$_POST['new_item_id']."','".$_POST['new_item_level']."');");
				}			
				else
					echo "Fehler! Diese Forschungsverkn&uuml;pfung existiert bereits!<br/><br/>";
			}
			else
				echo "Fehler! Keine verkn&uuml;pfende Forschung ausgew&auml;hlt!<br/><br/>";
		}
		
		// Gebäudeverknüpfungen löschen
		if ($_POST['del_building']!="")
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
			}
		}		

		// Technologieknüpfungen löschen
		if ($_POST['del_tech']!="")
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
			if ($_GET['action']=="new_building" || $_GET['action']=="new_tech")
				$form_addition=" disabled=\"disabled\"";

			while ($arr=mysql_fetch_array($res))
			{
				echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
				echo "<table style=\"width:400px\" class=\"tb\">";
				echo "<tr><th colspan=\"3\" >".$arr[ITEM_NAME_FLD]."</th></tr>";
				$using_something=0;

				// Gespeicherte Gebäudeanforderungen		
				if (count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
				{
					foreach ($b_req[$arr[ITEM_ID_FLD]]['b'] as $b=>$l)
					{
						echo "<tr>";
						echo "<td class=\"tbldata\" width=\"200\"><select name=\"building_id[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" $form_addition>";
						if ($b==0)
						echo "<option value=\"\"><i>Geb&auml;ude w&auml;hlen</i></option>";
						foreach ($bu_name as $key=>$val)
						{
							echo "<option value=\"$key\"";
							if ($b==$key) echo " selected=\"selected\"";
							echo ">$val</option>";
						}
						echo "</select></td><td class=\"tbldata\" width=\"50\"><input type=\"text\" name=\"building_level[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" size=\"1\" maxlength=\"3\" value=\"$l\"$form_addition /></td>";
						if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
							echo "<td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"del_building[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\" /></td></tr>";
						else
							echo "<td class=\"tbldata\">&nbsp;</td></tr>";
					}
					$using_something=1;
				}
				// Neue Gebäudeanforderung
				if ($_GET['action']=="new_building" && $_GET['id']==$arr[ITEM_ID_FLD])
				{
					echo "<input type=\"hidden\" name=\"new_id\" value=\"".$arr[ITEM_ID_FLD]."\">";
					echo "<tr><td class=\"tbldata\" width=\"200\"><select name=\"new_item_id\">";
					echo "<option value=\"\" style=\"font-style:italic;\">Geb&auml;ude w&auml;hlen</option>";
					foreach ($bu_name as $key=>$val)
					{
						if ($key!=$arr[ITEM_ID_FLD])
							echo "<option value=\"$key\">$val</option>";
					}			
					echo "</select></td><td class=\"tbldata\"><input type=\"text\" name=\"new_item_level\" size=\"1\" maxlength=\"3\" value=\"1\" /></td>";
					echo "<td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"add_building\" value=\"&Uuml;bernehmen\" /></td></tr>";
				}
				
				// Gespeicherte Forschungsanforderungen
				if (count($b_req[$arr[ITEM_ID_FLD]]['t'])>0)
				{
					foreach ($b_req[$arr[ITEM_ID_FLD]]['t'] as $b=>$l)
					{
						echo "<tr><td class=\"tbldata\" width=\"200\"><select name=\"tech_id[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" $form_addition>";
						if ($b==0)
						echo "<option value=\"\"><i>Geb&auml;ude w&auml;hlen</i></option>";
						foreach ($te_name as $key=>$val)
						{
							echo "<option value=\"$key\"";
							if ($b==$key) echo " selected=\"selected\"";
							echo ">$val</option>";
						}
						echo "</select></td><td class=\"tbldata\" width=\"50\"><input type=\"text\" name=\"tech_level[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" size=\"1\" maxlength=\"3\" value=\"$l\"$form_addition /></td>";
						if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
							echo "<td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"del_tech[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\"$form_addition /></td></tr>";
						else
							echo "<td class=\"tbldata\">&nbsp;</td></tr>";
					}		
					$using_something=1;
				}				
				// Neue Forschungsanforderung
				if ($_GET['action']=="new_tech" && $_GET['id']==$arr[ITEM_ID_FLD])
				{
					echo "<input type=\"hidden\" name=\"new_id\" value=\"".$arr[ITEM_ID_FLD]."\">";
					echo "<tr><td class=\"tbldata\" width=\"200\"><select name=\"new_item_id\">";
					echo "<option value=\"\" style=\"font-style:italic;\">Technologie w&auml;hlen</option>";
					foreach ($te_name as $key=>$val)
					{
						echo "<option value=\"$key\">$val</option>";
					}			
					echo "</select></td><td class=\"tbldata\"><input type=\"text\" name=\"new_item_level\" size=\"1\" maxlength=\"3\" value=\"1\" /></td>";
					echo "<td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"add_tech\" value=\"&Uuml;bernehmen\" /></td></tr>";
				}
				if ($using_something==0)
					echo "<tr><td width=\"200\" class=\"tbldata\">&nbsp;</td><td colspan=\"2\" class=\"techtreeBuildingNoReq\">Keine Voraussetzungen</td></tr>";
				if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
				{
					echo "<tr><td class=\"tbldata\">Neue Voraussetzung?</td>";
					echo "<td class=\"tbldata\" colspan=\"2\"><input type=\"button\" class=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=new_building&amp;id=".$arr[ITEM_ID_FLD]."';\" value=\"Geb&auml;ude\" />&nbsp;";
					echo "<input type=\"button\" class=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=new_tech&amp;id=".$arr[ITEM_ID_FLD]."';\" value=\"Forschung\" /></tr>";
				}
				echo "</table><br/>"; 	
			}
			if ($form_addition=="")
				echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit_changes\" value=\"&Auml;nderungen &uuml;bernehmen\" /></p>";
		}
		else
			echo "<p class=\"infomsg\">".NO_ITEMS_MSG."</p>";
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

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(missilelist_id) FROM missilelist;"));
			echo "Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/>";	
		
		
	}

?>