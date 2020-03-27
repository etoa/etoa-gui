<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

$twig->addGlobal("title", MODUL_NAME);

if (isset($_POST['apply_submit']))
{
	foreach ($_POST as $key=>$val)
	{
		if ($key!="apply_submit" && $key!="del")
		{
			foreach ($val as $k=>$vl)
			{
				dbquery("UPDATE ".DB_TABLE." set $key='$vl' WHERE ".DB_TABLE_ID."=$k;");
			}
		}
	}
	if (!mysql_error())
		echo MessageBox::ok("", "&Auml;nderungen wurden &uuml;bernommen!");
	else
		echo MessageBox::error("Fehler", mysql_error());

	$deleted=false;
	foreach ($_POST as $key=>$val)
	{
		if ($key=="del")
		{
			foreach ($val as $k=>$vl)
			{
				dbquery("DELETE FROM ".DB_TABLE." WHERE ".DB_TABLE_ID."='$k';");
			}
			$deleted=true;
		}
	}
	if ($deleted)
	{
		if (!mysql_error())
			echo MessageBox::ok("", "Bestimmte Daten wurden gel&ouml;scht!");
		else
			echo MessageBox::error("Fehler:", mysql_error());
	}
}
if (isset($_POST['new_submit']))
{
	$cnt = 1;
	$fsql = "";
	$vsql = "";
	$vsqlsp = "";
	foreach ($db_fields as $k=>$a)
	{
		$fsql .= "`".$a['name']."`";
		if ($cnt < sizeof($db_fields)) $fsql .= ",";
		$cnt++;
	}
	$cnt = 1;
	foreach ($db_fields as $k=>$a)
	{
		$vsql .= "'".$a['def_val']."'";
		if ($cnt < sizeof($db_fields)) $vsql .= ",";
		$cnt++;
	}

	$sql = "INSERT INTO ".DB_TABLE." (";
	$sql.= $fsql;
	$sql.= ") VALUES(";
	$sql.= $vsql.$vsqlsp;
	$sql.= ");";

	dbquery($sql);
	if (!mysql_error())
		echo MessageBox::ok("", "Neuer leerer Datensatz wurde hinzugef&uuml;gt!");
}

echo "<form action=\"?".URL_SEARCH_STRING."\" method=\"post\">";
if (!defined("DB_OVERVIEW_ORDER")) define("DB_OVERVIEW_ORDER","ASC");
if (defined("DB_CONDITION"))
	$sql = "SELECT * FROM ".DB_TABLE." WHERE ".DB_CONDITION." ORDER BY `".DB_OVERVIEW_ORDER_FIELD."` ".DB_OVERVIEW_ORDER.";";
else
	$sql = "SELECT * FROM ".DB_TABLE." ORDER BY `".DB_OVERVIEW_ORDER_FIELD."` ".DB_OVERVIEW_ORDER.";";
$res = dbquery($sql);
if (mysql_num_rows($res)!=0)
{
	echo "<table>";
	echo "<tr>";
	foreach ($db_fields as $k=>$a)
	{
		if ($a['show_overview']==1)
		{
			echo "<th class=\"tbltitle\">".$a['text']."</th>";
		}
	}
	echo "<th class=\"tbltitle\">L&ouml;schen</th>";
	echo "</tr>";
	while ($arr = mysql_fetch_assoc($res))
	{
		echo "<tr>";
		foreach ($db_fields as $k=>$a)
		{
			echo "<td class=\"tbldata\">";
			switch ($a['type'])
			{
				case "text":
					echo "<input type=\"text\" name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\" value=\"".$arr[$a['name']]."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" /></td>\n";
				break;
				case "email":
					echo "<input type=\"text\" name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\" value=\"".$arr[$a['name']]."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" /></td>\n";
				break;
				case "url":
					echo "<input type=\"text\" name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\" value=\"".$arr[$a['name']]."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" /></td>\n";
				break;
				case "numeric":
					echo "<input type=\"text\" name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\" value=\"".$arr[$a['name']]."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" /></td>\n";
				break;
				case "password":
					echo "<input type=\"password\" name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\" value=\"".$arr[$a['name']]."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" /></td>\n";
				break;
				case "timestamp":
					echo "<input type=\"text\" name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\" value=\"".date(DATE_FORMAT,$arr[$a['name']])."\" /></td>\n";
				break;
				case "textarea":
					echo "<input type=\"text\" name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\" value=\"";
					if (strlen($arr[$a['name']])>20)
						echo stripslashes(substr($arr[$a['name']],0,18)."...");
					else
						echo stripslashes($arr[$a['name']]);
					echo "\" /></td>\n";
				break;
				case "radio":
					echo "<input type=\"text\" name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\" value=\"".$arr[$a['name']]."\" /></td>\n";
				break;
				case "checkbox":

				break;
				case "select":
					echo "<select name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\">\n";
					if ($arr[$a['name']] == 0 || $arr[$a['name']] == "")
						echo "<option selected=\"selected\">(W&auml;hlen...)</option>";
					foreach ($a['select_elem'] as $sd => $sv)
					{
						echo "<option value=\"$sv\"";
						if ($arr[$a['name']]==$sv) echo " selected=\"selected\"";
						echo ">$sd</option>\n";
					}
					echo "</select></td>\n";
				break;
				case "hidden":
					echo "<input type=\"hidden\" name=\"".$a['name']."[".$arr[DB_TABLE_ID]."]\" value=\"".$arr[$a['name']]."\" size=\"".$a['size']."\" maxlength=\"".$a['maxlen']."\" />\n";
				break;
			}
		}
		echo "<td class=\"tbldata\"><input type=\"checkbox\" name=\"del[".$arr[DB_TABLE_ID]."]\" value=\"1\" /></td>\n";
		echo "</tr>\n";
	}
	echo "</table><br/>";
	echo "<input type=\"submit\" name=\"apply_submit\" value=\"&Uuml;bernehmen\" />&nbsp;";
	echo "<input type=\"submit\" name=\"new_submit\" value=\"Neuer Datensatz\" />&nbsp;";
}
else
{
	echo "<p align=\"center\"><i>Es existieren keine Datens&auml;tze!</i></p>";
	echo "<p align=\"center\"><input type=\"submit\" name=\"new_submit\" value=\"Neuer Datensatz\" />&nbsp;</p>";
}
echo "</form>";
?>
