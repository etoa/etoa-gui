<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

// Form: Adding a new dataset
if (isset($_GET['action']) && $_GET['action']=="new")
{
    $twig->addGlobal("title", MODUL_NAME);
    $twig->addGlobal("subtitle", "Neuer Datensatz");
	echo "<p>Gib die Daten des neuen Datensatzes in die untenstehende Maske ein:</p>";
	echo "<form action=\"?".URL_SEARCH_STRING."\" method=\"post\">";
	echo "<table>";
	admin_create_new_dataset($db_fields);
	echo "</table><br/>";
	echo "<input type=\"submit\" value=\"Neuen Datensatz speichern\" name=\"new\" />&nbsp;";
	echo "<input type=\"button\" value=\"Abbrechen\" name=\"newcancel\" onclick=\"document.location='?".URL_SEARCH_STRING."'\" />";
	echo "</form>";
}

// Form: Editing a dataset
elseif (isset($_GET['action']) && $_GET['action']=="edit")
{
	$res = mysql_query("SELECT * FROM ".DB_TABLE." WHERE ".DB_TABLE_ID."='".$_GET['id']."';");
	$arr=mysql_fetch_array($res);
    $twig->addGlobal("title", MODUL_NAME);
    $twig->addGlobal("subtitle", "Datensatz bearbeiten");
	echo "<p>&Auml;ndere die Daten des Datensatzes und klicke auf '&Uuml;bernehmen', um die Daten zu speichern:</p>";
	echo "<form action=\"?".URL_SEARCH_STRING."\" method=\"post\">";
	echo "<input type=\"submit\" value=\"&Uuml;bernehmen\" name=\"edit\" />&nbsp;";
	echo "<input type=\"button\" value=\"Abbrechen\" name=\"editcancel\" onclick=\"document.location='?".URL_SEARCH_STRING."'\" /><br/><br/>";

	echo "<input type=\"hidden\" name=\"".DB_TABLE_ID."\" value=\"".$_GET['id']."\" />";
	echo "<table>";
	admin_edit_dataset($db_fields,$arr);
	echo "</table><br/>";
	echo "<input type=\"submit\" value=\"&Uuml;bernehmen\" name=\"edit\" />&nbsp;";
	echo "<input type=\"button\" value=\"Abbrechen\" name=\"editcancel\" onclick=\"document.location='?".URL_SEARCH_STRING."'\" />";
	echo "</form>";
}

// Form: Deleting a dataset
elseif (isset($_GET['action']) && $_GET['action']=="del")
{
	$res = mysql_query("SELECT * FROM ".DB_TABLE." WHERE ".DB_TABLE_ID."='".$_GET['id']."';");
	$arr=mysql_fetch_array($res);
    $twig->addGlobal("title", MODUL_NAME);
    $twig->addGlobal("subtitle", "Datensatz löschen");
	echo "<p>Bitte best&auml;tige das L&ouml;schen des folgenden Datensatzes:</p>";
	echo "<form action=\"?".URL_SEARCH_STRING."\" method=\"post\">";
	echo "<input type=\"hidden\" name=\"".DB_TABLE_ID."\" value=\"".$_GET['id']."\" />";
	echo "<table>";
	admin_delete_dataset($db_fields,$arr);
	echo "</table><br/>";
	echo "<input type=\"submit\" value=\"L&ouml;schen\" name=\"del\" />&nbsp;";
	echo "<input type=\"button\" value=\"Abbrechen\" name=\"delcancel\" onclick=\"document.location='?".URL_SEARCH_STRING."'\" />";
	echo "</form>";
}

// Actions and show overview
else
{
	// Show Title
    $twig->addGlobal("title", MODUL_NAME);
    $twig->addGlobal("subtitle", "Übersicht");
	echo "<p>Um einen Datensatz hinzuzuf&uuml;gen, zu &auml;ndern oder zu l&ouml;schen klicke  bitte auf die entsprechenden Links oder Buttons!</p>";

	// Add new dataset query
	if (isset($_POST['new']) && $_POST['new']!="")
	{
		$sql = admin_create_new_dataset_query($db_fields);
		dbquery($sql);
		if (defined('POST_INSERT_UPDATE_METHOD'))
		{
			$fname = POST_INSERT_UPDATE_METHOD;
      if (stristr($fname, '::')) {
        list($class, $method) = explode('::', $fname);
        $hookResult = $class::$method();
      } else {
        $hookResult = $fname();
      }
			echo MessageBox::ok("", "Datensatz ge&auml;ndert! ".$hookResult);
		}
		else
		{
			echo MessageBox::ok("", "Neuer Datensatz gespeichert!");
		}
	}

	// Delete dataset query
	if (isset($_POST['del']) && $_POST['del']!="")
	{
		if (dbquery("DELETE FROM ".DB_TABLE." WHERE ".DB_TABLE_ID."='".$_POST[DB_TABLE_ID]."';"))
		{
			echo "<p class='amsgok'>Datensatz wurde gel&ouml;scht!</p>";
		}
	}

	// Edit dataset query
	if (isset($_POST['edit']) && $_POST['edit']!="")
	{
		$sql = admin_edit_dataset_query($db_fields);
		dbquery($sql);
		if (defined('POST_INSERT_UPDATE_METHOD'))
		{
			$fname = POST_INSERT_UPDATE_METHOD;
      if (stristr($fname, '::')) {
        list($class, $method) = explode('::', $fname);
        $hookResult = $class::$method();
      } else {
        $hookResult = $fname();
      }
			echo MessageBox::ok("", "Datensatz ge&auml;ndert! ".$hookResult);
		}
		else
		{
			echo MessageBox::ok("", "Datensatz ge&auml;ndert!");
		}
	}

	if (isset($_GET['action']) && $_GET['action']=="copy" && isset($_GET['id']))
	{
		DuplicateMySQLRecord(DB_TABLE,DB_TABLE_ID,$_GET['id']);
		success_msg("Datensatz kopiert!");
	}

	if (isset($_GET['sortup']) && isset($_GET['parentid']))
	{
		$res = dbquery("SELECT ".DB_TABLE_ID." FROM ".DB_TABLE." WHERE ".DB_TABLE_SORT_PARENT."=".$_GET['parentid']." ORDER BY ".DB_TABLE_SORT."");
		$cnt = 0;
		while ($arr = mysql_fetch_array($res))
		{
			dbquery("UPDATE ".DB_TABLE." SET ".DB_TABLE_SORT."=".$cnt." WHERE ".DB_TABLE_ID."=".$arr[DB_TABLE_ID]."");
			if ($_GET['sortup'] == $arr[DB_TABLE_ID])
			{
				$sorter = $cnt;
			}
			$cnt++;
		}
		dbquery("UPDATE ".DB_TABLE." SET ".DB_TABLE_SORT."=".($sorter)." WHERE ".DB_TABLE_SORT_PARENT."=".$_GET['parentid']." AND ".DB_TABLE_SORT."=".($sorter-1)."");
		dbquery("UPDATE ".DB_TABLE." SET ".DB_TABLE_SORT."=".($sorter-1)." WHERE ".DB_TABLE_ID."=".$_GET['sortup']."");
	}

	if (isset($_GET['sortdown']) && isset($_GET['parentid']))
	{
		$res = dbquery("SELECT ".DB_TABLE_ID." FROM ".DB_TABLE." WHERE ".DB_TABLE_SORT_PARENT."=".$_GET['parentid']." ORDER BY ".DB_TABLE_SORT.";");
		$cnt = 0;
		while ($arr = mysql_fetch_array($res))
		{
			dbquery("UPDATE ".DB_TABLE." SET ".DB_TABLE_SORT."=".$cnt." WHERE ".DB_TABLE_ID."=".$arr[DB_TABLE_ID]."");
			if ($_GET['sortdown'] == $arr[DB_TABLE_ID])
			{
				$sorter = $cnt;
			}
			$cnt++;
		}
		dbquery("UPDATE ".DB_TABLE." SET ".DB_TABLE_SORT."=".($sorter)." WHERE ".DB_TABLE_SORT_PARENT."=".$_GET['parentid']." AND ".DB_TABLE_SORT."=".($sorter+1)."");
		dbquery("UPDATE ".DB_TABLE." SET ".DB_TABLE_SORT."=".($sorter+1)." WHERE ".DB_TABLE_ID."=".$_GET['sortdown']."");
	}


	// Switcher
	if (isset($form_switches) && isset($_GET['switch']) && $_GET['id']>0)
	{
		dbquery("UPDATE ".DB_TABLE." SET `".$_GET['switch']."`=(`".$_GET['switch']."`+1)%2 WHERE `".DB_TABLE_ID."`=".$_GET['id']."");
		success_msg("Aktion ausgeführt!");
	}

	// Show overview
	echo "<form action=\"?".URL_SEARCH_STRING."\" method=\"post\">";
	echo "<input type=\"button\" value=\"Neuer Datensatz hinzuf&uuml;gen\" name=\"new\" onclick=\"document.location='?".URL_SEARCH_STRING."&amp;action=new'\" /><br/><br/>";

	if (!defined("DB_OVERVIEW_ORDER")) define("DB_OVERVIEW_ORDER","ASC");
	if (defined("DB_CONDITION"))
		$sql = "SELECT * FROM ".DB_TABLE." WHERE ".DB_CONDITION." ORDER BY ".DB_OVERVIEW_ORDER_FIELD." ".DB_OVERVIEW_ORDER.";";
	else
		$sql = "SELECT * FROM ".DB_TABLE." ORDER BY ".DB_OVERVIEW_ORDER_FIELD." ".DB_OVERVIEW_ORDER.";";
	if ($res = dbquery($sql))
	{
		echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\"><tr>";
		if (defined('DB_IMAGE_PATH'))
		{
			echo "<th valign=\"top\" class=\"tbltitle\">Bild</a>";
		}
		foreach ($db_fields as $k=>$a)
		{
			if ($a['show_overview']==1)
			{
				echo "<th valign=\"top\" class=\"tbltitle\">".$a['text']."</a>";
			}
		}
		if (isset($form_switches))
		{
			foreach ($form_switches as $k=>$v)
			{
				echo "<th valign=\"top\" class=\"tbltitle\">";
				echo "$k";
				echo "</th>";
			}
		}
		if (defined('DB_TABLE_SORT') && defined('DB_TABLE_SORT_PARENT'))
		{
			echo "<th valign=\"top\" class=\"tbltitle\">";
			echo "Sort";
			echo "</th>";
		}

		echo "<th valign=\"top\" width=\"70\" colspan=\"2\">&nbsp;</td></tr>";
		$cnt=0;
		while ($arr=mysql_fetch_array($res))
		{
			echo "<tr>";
			if (defined('DB_IMAGE_PATH'))
			{
				$path = preg_replace('/<DB_TABLE_ID>/',$arr[DB_TABLE_ID],DB_IMAGE_PATH);
				if (is_file($path))
				{
					$imsize = getimagesize($path);
					echo "<td class=\"tbldata\" style=\"background:#000;width:".$imsize[0]."px;\">
					<a href=\"?".URL_SEARCH_STRING."&amp;action=edit&amp;id=".$arr[DB_TABLE_ID]."\">
					<img src=\"".$path."\" align=\"top\"/>
					</a></td>";
				}
				else
				{
					echo "<td class=\"tbldata\" style=\"background:#000;width:40px;\">
					<a href=\"?".URL_SEARCH_STRING."&amp;action=edit&amp;id=".$arr[DB_TABLE_ID]."\">
					<img src=\"../images/blank.gif\" style=\"width:40px;height:40px;\" align=\"top\"/>
					</a></td>";
				}

			}

			admin_show_overview($db_fields,$arr);

			if (isset($form_switches))
			{
				foreach ($form_switches as $k=>$v)
				{
					echo "<td valign=\"top\" class=\"tbldata\">
					<a href=\"?".URL_SEARCH_STRING."&amp;switch=".$v."&amp;id=".$arr[DB_TABLE_ID]."\">";
					if ($arr[$v]==1)
						echo "<img src=\"../images/true.gif\" alt=\"true\" />";
					else
						echo "<img src=\"../images/false.gif\" alt=\"true\" />";
					echo "</td>";
				}
			}

			if (defined('DB_TABLE_SORT') && defined('DB_TABLE_SORT_PARENT'))
			{
				echo "<td valign=\"top\" class=\"tbldata\" style=\"width:40px;\">";

				if ($cnt < mysql_num_rows($res)-1)
					echo "<a href=\"?".URL_SEARCH_STRING."&amp;sortdown=".$arr[DB_TABLE_ID]."&amp;parentid=".$arr[DB_TABLE_SORT_PARENT]."\"><img src=\"../images/down.gif\" alt=\"down\" /></a> ";
				else
					echo "<img src=\"../images/blank.gif\" alt=\"blank\" style=\"width:16px;\" /> ";

				if ($cnt!=0 && $parId==$arr[DB_TABLE_SORT_PARENT])
					echo "<a href=\"?".URL_SEARCH_STRING."&amp;sortup=".$arr[DB_TABLE_ID]."&amp;parentid=".$arr[DB_TABLE_SORT_PARENT]."\"><img src=\"../images/up.gif\" alt=\"up\" /></a> ";
				else
					echo "<img src=\"../images/blank.gif\" alt=\"blank\" style=\"width:16px;\" /> ";

				echo "</td>";

				$parId = $arr[DB_TABLE_SORT_PARENT];
			}

			echo "<td valign=\"top\" class=\"tbldata\" style=\"width:50px\">
			".edit_button("?".URL_SEARCH_STRING."&amp;action=edit&amp;id=".$arr[DB_TABLE_ID])." 
			".copy_button("?".URL_SEARCH_STRING."&amp;action=copy&amp;id=".$arr[DB_TABLE_ID])." 
			".del_button("?".URL_SEARCH_STRING."&amp;action=del&amp;id=".$arr[DB_TABLE_ID])." 
			</td>";
			echo "</tr>\n";
			$cnt++;
		}
	}
	echo "</table></form>";
}
?>
