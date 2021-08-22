<?PHP

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Market\MarketRepository;
use EtoA\Support\StringUtils;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];
/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];
/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];

/********
 *	Datei-Struktur
 *	Die Datei market.php ist wie folgt aufgebaut
 *
 *	if(Markt gebaut)
 *	{
 *		-> Navigation
 *
 *		-> Rohstoffverkauf speichern
 *		-> Schiffskauf speichern
 *		-> Auktionsgebot speichern
 *
 *		-> Rohstoffverkauf speichern
 *		-> Schiffverkauf speichern
 *		-> Auktion Speichern
 *
 *		-> Rohstoff Angebote anzeigen
 *		-> Schiffs Angebote anzeigen
 *		-> Auktionen anzeigen
 *		-> Einzelne Auktion anzeigen
 *
 *		-> Suchmaske
 *		-> Eigene Angebote anzeigen
 *		-> Angebote aufgeben
 *	}
 *
 ********/

$planet = $planetRepo->find($cp->id);

if ($config->getBoolean('market_enabled')) {
    $mode = isset($_GET['mode']) ? $_GET['mode'] : "";

    $market = $buildingRepository->getEntityBuilding($cu->getId(), $planet->id, MARKTPLATZ_ID);

    //Überprüfung ob der Marktplatz schon gebaut wurde
    if ($market !== null && $market->currentLevel > 0) {
        // Header
        //<editor-fold>

        define("MARKET_LEVEL",$market->currentLevel);
        define("MARKET_TAX", max(1, MARKET_SELL_TAX * $cu->specialist->tradeBonus));

        // Show title
        echo '<h1>Marktplatz (Stufe ' . $market->currentLevel . ') des Planeten ' . $planet->name . '</h1>';
        echo $resourceBoxDrawer->getHTML($planet);

        if (!$market->isDeactivated()) {
            // Load javascript
            require("content/market/js.php");

            // Läd die Anzahl aller eingestellter Angebote auf dem aktuellen Planeten
            /** @var MarketRepository $marketRepository */
            $marketRepository = $app[MarketRepository::class];
            $anzahl = $marketRepository->getOfferCountOnCurrentEntity($cu->getId(), $planet->id);

            $possible = $market->currentLevel - $anzahl;

            // Lädt Stufe des Allianzmarktplatzes
            if ($cu->allianceId() > 0)
                $alliance_market_level = $allianceBuildingRepository->getLevel($cu->allianceId(), AllianceBuildingId::MARKET);
            else
                $alliance_market_level = 0;

            // Calculate cooldown
            if ($alliance_market_level < 5) {
                $factor = 0.2 * $alliance_market_level;
            } else {
                $factor = $alliance_market_level - 4;
            }
            $cooldown = ($factor == 0) ? 0 : 3600 / $factor;
            if ($alliance_market_level > 0) {
                $allianceMarketCooldown = $allianceBuildingRepository->getCooldown($cu->allianceId(), AllianceBuildingId::MARKET);
                if ($allianceMarketCooldown > time()) {
                    $status_text = "Bereit in <span id=\"cdcd\">" . StringUtils::formatTimespan($allianceMarketCooldown - time()) . "</span>";
                    $cd_enabled = true;
                } else {
                    $status_text = "Bereit";
                    $cd_enabled = false;
                }
            } else {
                $status_text = "Es wurde noch kein Handelszentrum gebaut!";
                $cd_enabled = false;
            }

            // Definiert den Rückgabefaktor beim zurückziehen eines Angebots
            $return_factor = floor((1 - 1 / ($market->currentLevel + 1)) * 100) / 100;


            //Marktinof Bof
            tableStart("Marktplatz-Infos");
            echo "<tr><th>Angebote:</th>
                <td>Im Moment hast du " . $anzahl . " Angebote von diesem Planet auf dem Markt</td></tr>";
            echo "<tr><th>Mögliche Angebote:</th>
                <td>Du kannst noch " . $possible . " Angebote einstellen</td></tr>";
            echo "<tr><th>Rückzugsgebühren:</th>
                <td>Wenn du ein Angebot von diesem Planet zur&uuml;ckziehst erh&auml;lst du " . ($return_factor * 100) . "% des Angebotes zur&uuml;ck (abgerundet).</td></tr>";
            echo "<tr><th>Verkaufsgebühren:</th>
                <td>Die Verkaufsgeb&uuml;hr des Marktplatzes betr&auml;gt " . get_percent_string(MARKET_TAX, 1, 1) . "";
            if ($cu->specialist->tradeBonus != 1) {
                echo " (inkl " . get_percent_string($cu->specialist->tradeBonus, 1, 1) . " Kostenverringerung durch " . $cu->specialist->name . "!";
            }
            echo "	</td></tr>";
            if ($cu->specialist->tradeTime != 1) {
                echo "<tr><th>Handelsflottengeschwindigkeit:</th>
                    <td>Die Handelsflotten fliegen durch " . $cu->specialist->name . " mit " . get_percent_string($cu->specialist->tradeTime, 1) . " Geschwindigkeit!
                    </td></tr>";
            }
            if ($cu->allianceId() > 0) {
                echo "<tr><th>Allianzmarktstatus:</th>
                    <td>" . $status_text . "</td></tr>";
            }


            echo "<tr><th>Rohstoffkurse:</th><td>" . popUp("Details anzeigen", "page=help&amp;site=rates") . "</td></tr>";
            tableEnd();

            // Navigation
            $tabitems = array(
                "user_home" => "Angebote aufgeben",
                "user_sell" => "Eigene Angebote",
                "search" => "Angebotssuche",
            );
            show_tab_menu("mode", $tabitems);

            echo "<br/>";

            //</editor-fold>

            //
            // Rohstoffkauf speichern
            //
            if (isset($_POST['ressource_submit']) && checker_verify()) {
                require("content/market/res_sell.php");
            }

            //
            // Schiffskauf speichern
            //
            elseif (isset($_POST['ship_submit']) && checker_verify()) {
                require("content/market/ship_sell.php");
            }

            //
            // Auktionsgebot speichern
            //
            elseif (isset($_POST['submit_auction_bid'])  && checker_verify()) {
                require("content/market/auction_bid.php");
            }

            //
            // Rohstoffverkauf speichern
            //
            elseif (isset($_POST['ress_last_update']) && intval($_POST['ress_last_update']) == 1 && checker_verify()) {
                require("content/market/res_offer.php");
            }

            //
            // Schiffverkauf speichern
            //
            elseif (isset($_POST['ship_last_update']) && intval($_POST['ship_last_update']) == 1 && checker_verify()) {
                require("content/market/ship_offer.php");
            }

            //
            // Auktion Speichern
            //
            elseif (isset($_POST['auction_last_update']) && intval($_POST['auction_last_update']) == 1 && checker_verify()) {
                require("content/market/auction_new.php");
            }

            //
            // Suchmaske
            //
            elseif ($mode == "search") {
                require("content/market/search.php");
            }

            //
            // Eigene Angebote anzeigen
            //
            elseif ($mode == "user_sell") {
                require("content/market/own.php");
            }

            //
            // Angebote aufgeben
            //
            else {
                require("content/market/new.php");
            }

            if ($cd_enabled) {
                $allianceMarketCooldown = $allianceBuildingRepository->getCooldown($cu->allianceId(), AllianceBuildingId::MARKET);
                countDown("cdcd", $allianceMarketCooldown);
            }
        } else {
            info_msg("Dieses Gebäude ist noch bis " . df($market->deactivated) . " deaktiviert!");
        }
    }

    //
    // Meldung dass noch kein Marktplatz gebaut wurde
    //
    else {
        // Header
        echo '<h1>Marktplatz</h1>';
        echo $resourceBoxDrawer->getHTML($planet);
        info_msg("Der Marktplatz wurde noch nicht gebaut.");
    }
} else {
    echo '<h1>Marktplatz</h1>';
    echo $resourceBoxDrawer->getHTML($planet);
    info_msg("Der Marktplatz ist momentan im Spiel deaktiviert.");
}
