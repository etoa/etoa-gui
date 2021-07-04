<h1>Start-Objekte</h1>
Hier definierte Objekte werden einem Spieler beim ersten Login auf dem Hauptplanet erstellt.
Existieren mehrere aktive Sets, erhält der Spieler eine Auswahl. Existiert kein Set oder sind alle
Sets inaktiv, erhält der Spieler keine Objekte. Klicke auf einen Objektnamen um die Anzahl zu ändern.<br/><br/>
<?PHP

// Switch set status (active or inactive)
// Trick: Modulo operation reduces code
use EtoA\DefaultItem\DefaultItemRepository;

/** @var DefaultItemRepository $defaultItemRepository */
$defaultItemRepository = $app[DefaultItemRepository::class];

if (isset($_GET['switchsetstatus']) && $_GET['switchsetstatus']>0) {
    $defaultItemRepository->toggleSetActive((int) $_GET['switchsetstatus']);
	echo "Set Status geändert!<br/><br/>";
}

// Create new set
if (isset($_POST['new_set_submit'])) {
    $defaultItemRepository->createSet($_POST['new_set_name']);
}

// Delte set
if (isset($_GET['delset']) && $_GET['delset']>0) {
    $defaultItemRepository->deleteSet((int) $_GET['delset']);
	echo "Set gelöscht!<br/><br/>";
}

$defaultItemSets = $defaultItemRepository->getSets(true);
if (count($defaultItemSets) > 0) {
    foreach ($defaultItemSets as $defaultItemSet) {
		echo "<fieldset><legend>";
		if ($defaultItemSet->active) {
			echo "<span style=\"color:#0f0;\">".$defaultItemSet->name."</span> (<a href=\"?page=$page&amp;sub=$sub&amp;switchsetstatus=".$defaultItemSet->id."\">Deaktivieren</a>)";
		}
		else
		{
			echo "<span style=\"color:#999;\">".$defaultItemSet->name."</span>  (<a href=\"?page=$page&amp;sub=$sub&amp;switchsetstatus=".$defaultItemSet->id."\">Aktivieren</a>)";
		}
		echo " [<a href=\"?page=$page&amp;sub=$sub&amp;delset=".$defaultItemSet->id."\" onclick=\"return confirm('Gesamtes Set wirklich löschen?');\">Löschen</a>]";
		echo "</legend>";
		echo "<div id=\"setcontent_".$defaultItemSet->id."\">Wird geladen...</div>";
			echo "<br/><br/>
			<form action=\"?\" method=\"post\" id=\"set_".$defaultItemSet->id."\">Hinzufügen:
			<select name=\"new_item_cat\" id=\"new_item_cat\" onchange=\"showLoaderInline('itemlist_".$defaultItemSet->id."');xajax_loadItemSelector(this.value,".$defaultItemSet->id.")\">
			<option value=\"\">Kategorie wählen...</option>
			<option value=\"b\">Gebäude</option>
			<option value=\"t\">Technologien</option>
			<option value=\"s\">Schiffe</option>
			<option value=\"d\">Verteidigung</option>
			</select> <span id=\"itemlist_".$defaultItemSet->id."\"></span></form>";
		echo "</fieldset><br/>
		<script type=\"text/javascript\">$(function(){ showLoaderInline('setcontent_".$defaultItemSet->id."');xajax_loadItemSet(".$defaultItemSet->id."); });</script>";
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
