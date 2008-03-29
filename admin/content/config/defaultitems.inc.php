<h2>Start-Objekte</h2>
Hier definierte Objekte werden einem Spieler beim ersten Login auf dem Hauptplanet erstellt.
Existieren mehrere aktive Sets, erhält der Spieler eine Auswahl. Existiert kein Set oder sind alle 
Sets inaktiv, erhält der Spieler keine Objekte. Klicke auf einen Objektnamen um die Anzahl zu ändern.<br/><br/>
<?

// Switch set status (active or inactive)
// Trick: Modulo operation reduces code
if (isset($_GET['switchsetstatus']) && $_GET['switchsetstatus']>0)
{
	dbquery("
	UPDATE
		default_item_sets
	SET
		set_active=(set_active+1)%2
	WHERE
		set_id=".$_GET['switchsetstatus']."
	");
	echo "Set Status geändert!<br/><br/>";
}

// Create new set
if (isset($_POST['new_set_submit']))
{
	dbquery("
	INSERT INTO
		default_item_sets
	(
		set_name,
		set_active
	)
	VALUES
	(
		'".addslashes($_POST['new_set_name'])."',
		0
	);
	");
}

// Delte set
if (isset($_GET['delset']) && $_GET['delset']>0)
{
	dbquery("
	DELETE FROM
		default_item_sets
	WHERE
		set_id=".$_GET['delset']."
	");
	dbquery("
	DELETE FROM
		default_items
	WHERE
		item_set_id=".$_GET['delset']."
	");
	echo "Set gelöscht!<br/><br/>";
}



$res = dbquery("SELECT * FROM default_item_sets ORDER BY set_name;");
if (mysql_num_rows($res)>0)
{
	while ($arr=mysql_fetch_array($res))
	{
		echo "<fieldset><legend>";
		if ($arr['set_active']==1)
		{
			echo "<span style=\"color:#0f0;\">".$arr['set_name']."</span> (<a href=\"?page=$page&amp;sub=$sub&amp;switchsetstatus=".$arr['set_id']."\">Deaktivieren</a>)";
		}
		else
		{
			echo "<span style=\"color:#999;\">".$arr['set_name']."</span>  (<a href=\"?page=$page&amp;sub=$sub&amp;switchsetstatus=".$arr['set_id']."\">Aktivieren</a>)";
		}
		echo " [<a href=\"?page=$page&amp;sub=$sub&amp;delset=".$arr['set_id']."\" onclick=\"return confirm('Gesamtes Set wirklich löschen?');\">Löschen</a>]";
		echo "</legend>";	
		echo "<div id=\"setcontent_".$arr['set_id']."\">Wird geladen...</div>";
			echo "<br/><br/>
			<form action=\"?\" method=\"post\" id=\"set_".$arr['set_id']."\">Hinzufügen: 
			<select name=\"new_item_cat\" id=\"new_item_cat\" onchange=\"showLoaderInline('itemlist_".$arr['set_id']."');xajax_loadItemSelector(this.value,".$arr['set_id'].")\">
			<option value=\"\">Kategorie wählen...</option>
			<option value=\"b\">Gebäude</option>
			<option value=\"t\">Technologien</option>
			<option value=\"s\">Schiffe</option>
			<option value=\"d\">Verteidigung</option>
			</select> <span id=\"itemlist_".$arr['set_id']."\"></span></form>";			
		echo "</fieldset><br/>
		<script type=\"text/javascript\">showLoaderInline('setcontent_".$arr['set_id']."');xajax_loadItemSet(".$arr['set_id'].");</script>";
	}
}
else
{
	echo "Keine Sets definiert! Spieler starten ohne irgendwelche Objekte.";
}
echo "<h3>Neues Set erstellen</h3>
<form action=\"\" method=\"post\">
Name: <input type=\"text\" name=\"new_set_name\" value=\"\" /> 
<input type=\"submit\" value=\"Erstellen\" name=\"new_set_submit\" />";
echo "</form>";
?>