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
	// 	Dateiname: advanced_forms.php
	// 	Topic: Vorlage f&uuml;r erweiterte Formulare
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	// Form: Adding a new dataset

	if (isset($_GET['action']) && $_GET['action']=="new")
	{
		echo "<h1>".MODUL_NAME." - Neuer Datensatz</h1>";
		echo "Gib die Daten des neuen Datensatzes in die untenstehende Maske ein:<br/><br/>";
		echo "<form action=\"?".URL_SEARCH_STRING."\" method=\"post\">";
		echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\">";
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
		echo "<h1>".MODUL_NAME." - Datensatz bearbeiten</h1>";
		echo "&Auml;ndere die Daten des Datensatzes und klicke auf '&Uuml;bernehmen', um die Daten zu speichern:<br/><br/>";
		echo "<form action=\"?".URL_SEARCH_STRING."\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"".DB_TABLE_ID."\" value=\"".$_GET['id']."\" />";
		echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\">";
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
		echo "<h1>".MODUL_NAME." - Datensatz l&ouml;schen</h1>";
		echo "Bitte best&auml;tige das L&ouml;schen des folgenden Datensatzes:<br/><br/>";
		echo "<form action=\"?".URL_SEARCH_STRING."\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"".DB_TABLE_ID."\" value=\"".$_GET['id']."\" />";
		echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\">";
		admin_delete_dataset($db_fields,$arr);
		echo "</table><br/>";
		echo "<input type=\"submit\" value=\"L&ouml;schen\" name=\"del\" />&nbsp;";
		echo "<input type=\"button\" value=\"Abbrechen\" name=\"delcancel\" onclick=\"document.location='?".URL_SEARCH_STRING."'\" />";
		echo "</form>";
	}

	// Actions and show overview

	else
	{
		/*
		$strtr_arr = array("'"=>"\'");
		if (isset($_POST))
		{
			foreach($_POST as $k => $v);
			{
				$_POST[$k]=strtr($v,$strtr_arr);
			}
		}*/

		// Show Title
		echo "<h1>".MODUL_NAME." - &Uuml;bersicht</h1>";
		echo "Um einen Datensatz hinzuzuf&uuml;gen, zu &auml;ndern oder zu l&ouml;schen klicke  bitte auf die entsprechenden Links oder Buttons!<br/><br/>";

		// Add new dataset query
		if (isset($_POST['new']) && $_POST['new']!="")
		{
			$sql = admin_create_new_dataset_query($db_fields,$_POST);
			dbquery($sql);
			if (!mysql_error())
			{
				echo "<p class='amsgok'>Neuer Datensatz gespeichert!</p>";
			}
			else
			{
				echo "<p class='amsgerr'>".mysql_error()."</p>";
			}
		}

  	// Delete dataset query
		if (isset($_POST['del']) && $_POST['del']!="")
		{
			dbquery("DELETE FROM ".DB_TABLE." WHERE ".DB_TABLE_ID."='".$_POST[DB_TABLE_ID]."';");
			if (!mysql_error())
			{
				echo "<p class='amsgok'>Datensatz wurde gel&ouml;scht!</p>";
			}
			else
			{
				echo "<p class='amsgerr'>".mysql_error()."</p>";
			}
		}

		// Edit dataset query
		if (isset($_POST['edit']) && $_POST['edit']!="")
		{
			$sql = admin_edit_dataset_query($db_fields,$_POST);
			dbquery($sql);
			if (!mysql_error())
			{
				cms_ok_msg("Datensatz ge&auml;ndert!");
			}
			else
			{
				cms_err_msg(mysql_error());
			}
		}

		// Show overview
		echo "<form action=\"?".URL_SEARCH_STRING."\" method=\"post\">";
		echo "<input type=\"button\" value=\"Neuer Datensatz hinzuf&uuml;gen\" name=\"new\" onclick=\"document.location='?".URL_SEARCH_STRING."&amp;action=new'\" /><br/><br/>";

		if (!defined("DB_OVERVIEW_ORDER")) define("DB_OVERVIEW_ORDER","ASC");
		if (defined("DB_CONDITION"))
			$sql = "SELECT * FROM ".DB_TABLE." WHERE ".DB_CONDITION." ORDER BY ".DB_OVERVIEW_ORDER_FIELD." ".DB_OVERVIEW_ORDER.";";
		else
			$sql = "SELECT * FROM ".DB_TABLE." ORDER BY ".DB_OVERVIEW_ORDER_FIELD." ".DB_OVERVIEW_ORDER.";";
		$res = dbquery($sql);
		if (!mysql_error())
		{
			echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\"><tr>";
			foreach ($db_fields as $k=>$a)
			{
				if ($a['show_overview']==1)
				{
					echo "<th valign=\"top\" class=\"tbltitle\">".$a['text']."</a>";
				}
			}
			echo "<th valign=\"top\" width=\"50\" colspan=\"2\">&nbsp;</td></tr>";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr>";
				admin_show_overview($db_fields,$arr);
				echo "<td valign=\"top\" class=\"tbldata\" style=\"width:50px\">".edit_button("?".URL_SEARCH_STRING."&amp;action=edit&amp;id=".$arr[DB_TABLE_ID])." ";
				echo del_button("?".URL_SEARCH_STRING."&amp;action=del&amp;id=".$arr[DB_TABLE_ID])."</td>";
				echo "</tr>\n";
			}
		}
		echo "</table></form>";
	}

?>
