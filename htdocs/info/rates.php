<?PHP

use EtoA\Support\RuntimeDataStore;
use EtoA\Universe\Resources\ResourceNames;

/** @var RuntimeDataStore $runtimeDataStore */
$runtimeDataStore = $app[RuntimeDataStore::class];

$currentRates = [];
foreach (array_keys(ResourceNames::NAMES) as $i) {
    $currentRates[$i] = $runtimeDataStore->get('market_rate_' . $i, (string) 1);
}

echo "<h2>Rohstoffkurse</h2>";

HelpUtil::breadCrumbs(array("Rohstoffkurse", "rates"));

echo "Die Rohstoffkurse sind dynamisch und ändern sich automatisch je nach dem<br/>
     wie gross das Angebot und die Nachfrage
    nach einem Rohstoff im Markt ist.<br/><br/>";
echo "<table class=\"tb\">";

echo "<tr>
        <th style=\"width:15%\"></th>";
foreach (ResourceNames::NAMES as $resourceName) {
    echo "		<th style=\"width:17%\">" . $resourceName . "</th>";
}
echo "</tr>";
foreach (ResourceNames::NAMES as $i => $resourceName) {
    echo "<tr>
            <th>" . $resourceName . "</th>";
    foreach (array_keys(ResourceNames::NAMES) as $j) {
        if ($i == $j)
            echo "<td>-</td>";
        else {
            $r = round($currentRates[$i] / $currentRates[$j], 2);
            echo "<td" . HelpUtil::colorizeMarketRate($r) . ">" . $r . "</td>";
        }
    }
    echo "</tr>";
}
echo "</table><br/>";

echo "<b>Beispiel:</b> Eine Tonne " . ResourceNames::NAMES[0] . " hat den Wert von " . round($currentRates[0] / $currentRates[1], 2) . " Tonnen " . ResourceNames::NAMES[1] . ".<br/>
    Für eine Tonne " . ResourceNames::NAMES[1] . " muss man " . round($currentRates[1] / $currentRates[0], 2) . " Tonnen " . ResourceNames::NAMES[0] . " aufwenden.<br/><br/>";
echo "<b>Legende:</b><br/><br/> kleiner Bedarf/grosses Angebot
        <span style=\"background:#0f0;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span style=\"background:#ff0;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span style=\"background:#fa0;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span style=\"background:#f70;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span style=\"background:#f40;width;50px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        grosser Bedarf/kleines Angebot
        <br/><br/>";


echo "<div style=\"text-align:center;\"><table class=\"tb\" style=\"width:200px;\">";
echo "<tr>
        <th>Rohstoff</th>
        <th>Absoluter Kurs</th>
    </tr>";
foreach (ResourceNames::NAMES as $i => $resourceName) {
    $r = $currentRates[$i];
    echo "<tr>
            <th>" . $resourceName . "</th>
            <td" . HelpUtil::colorizeMarketRate($r) . ">" . $r . "</td>
        </tr>";
}
echo "</table><br/>";

echo '<img src="misc/market.image.php" alt="Kursverlauf" />
            </div><br/>';
