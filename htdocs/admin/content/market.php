<?PHP

use EtoA\Market\MarketAuctionRepository;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResourceNames;

/** @var RuntimeDataStore $runtimeDataStore */
$runtimeDataStore = $app[RuntimeDataStore::class];
/** @var MarketAuctionRepository $marketAuctionRepository */
$marketAuctionRepository = $app[MarketAuctionRepository::class];
/** @var MarketResourceRepository $marketResourceRepository */
$marketResourceRepository = $app[MarketResourceRepository::class];
/** @var MarketShipRepository $marketShipRepository */
$marketShipRepository = $app[MarketShipRepository::class];

define("USER_MESSAGE_CAT_ID", 1);
define("SYS_MESSAGE_CAT_ID", 5);

echo "<h1>Marktplatz</h1>";

if ($sub == "ress") {
    echo "<h2>Rohstoffe</h2>";
    if (isset($_GET['ressource_delete']) && $_GET['ressource_delete'] > 0) {
        $marketResourceRepository->delete((int) $_GET['ressource_delete']);
        echo MessageBox::ok("", "Angebot gel&ouml;scht!");
    }

    $offers = $marketResourceRepository->getAll();
    if (count($offers) > 0) {
        foreach ($offers as $offer) {
            $username = get_user_nick($offer->userId);
            echo "<table class=\"tb\">";
            echo "<tr>
                        <th width=\"100\">
                            Datum:
                        </th>
                        <td colspan=\"2\" width=\"200\">
                            " . date("d.m.Y - H:i:s", $offer->date) . "
                        </td>
                        <th width=\"100\">
                            Spieler:
                        </th>
                        <td width=\"100\">
                            <a href=\"?page=user&amp;sub=edit&amp;user_id=" . $offer->userId . "\">" . $username . "</a></td><td class=\"tbltitle\"><input type=\"button\" onclick=\"if (confirm('Soll dieses Angebot wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&ressource_delete=" . $offer->id . "'\" value=\"L&ouml;schen\"/>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan=\"6\">
                            Angebot:
                        </th>";
            $first = true;
            $sellResources = $offer->getSellResources();
            $buyResources = $offer->getBuyResources();
            foreach (ResourceNames::NAMES as $k => $v) {
                if (!$first) echo "<tr>";
                echo "	<td width=\"110\">" . $v . "</td>
                                    <td width=\"100\">
                                        " . StringUtils::formatNumber($sellResources->get($k)) . "
                                    </td>";
                if ($first) {
                    echo "<th rowspan=\"5\">Preis:</th>";
                    $first = false;
                }
                echo    "<td width=\"110\">" . $v . "</td>
                                    <td width=\"100\">
                                        " . StringUtils::formatNumber($buyResources->get($k)) . "
                                    </td>
                                </tr>";
            }
            echo "</table><br/>";
        }
    } else {
        error_msg("Keine Angebote vorhanden", 1);
    }
} elseif ($sub == "ships") {
    echo "<h2>Schiffe</h2>";
    if (isset($_GET['ship_delete']) && $_GET['ship_delete'] != "") {
        $marketShipRepository->delete((int) $_GET['ship_delete']);
        echo MessageBox::ok("", "Angebot gel&ouml;scht");
    }

    $offers = $marketShipRepository->getAll();
    if (count($offers) > 0) {
        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $app[ShipDataRepository::class];
        $shipNames = $shipRepository->getShipNames(true);

        foreach ($offers as $offer) {
            $username = get_user_nick($offer->userId);
            echo "<form action=\"?page=$page&sub=$sub\" method=\"POST\">\n";
            echo "<input type=\"hidden\" name=\"ship_market_id\" value=\"" . $offer->id . "\">";
            echo "<table class=\"tb\">
                        <tr>
                            <th width=\"100\">
                                Datum:
                            </th>
                            <td colspan=\"2\" width=\"200\">
                                " . date("d.m.Y - H:i:s", $offer->date) . "
                            </td>
                            <th width=\"100\">
                                Spieler:
                            </th>
                            <td width=\"100\">
                                <a href=\"?page=user&amp;sub=edit&amp;user_id=" . $offer->userId . "\">" . $username . "</a>
                            </td>
                            <td rowspan=\"4\">
                                <input type=\"button\" onclick=\"if (confirm('Soll dieses Angebot wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&ship_delete=" . $offer->id . "'\" value=\"L&ouml;schen\"/>
                            </td>
                        </tr>
                        <tr>
                            <th width=\"100\">
                                Schiffname:
                            </th>
                            <td colspan=\"2\" width=\"200\">
                                " . $shipNames[$offer->shipId] . "
                            </td>
                            <th width=\"100\">
                                Anzahl:
                            </td>
                            <td width=\"100\">
                                " . $offer->count . "
                            </td>
                        </tr>
                        <tr>";
            foreach (ResourceNames::NAMES as $k => $v) {
                echo "<th width=\"100\">
                            " . $v . "
                        </th>";
            }
            echo "</tr>
                        <tr>";
            foreach ([$offer->costs0, $offer->costs1, $offer->costs2, $offer->costs3, $offer->costs4] as $cost) {
                echo "<td width=\"100\">
                            " . StringUtils::formatNumber($cost) . "
                        </td>";
            }

            echo "</tr>
                    </table><br/>";
        }
    } else {
        error_msg("Keine Angebote vorhanden", 1);
    }
} elseif ($sub == "auction") {
    echo "<h2>Auktionen</h2>";
    if (isset($_GET['auction_delete']) && $_GET['auction_delete'] != "") {
        $marketAuctionRepository->deleteAuction((int) $_GET['auction_delete']);
        echo MessageBox::ok("", "Auktion gel&ouml;scht");
    }

    $auctions = $marketAuctionRepository->getAll();
    if (count($auctions) > 0) {
        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $app[ShipDataRepository::class];
        $shipNames = $shipRepository->getShipNames(true);

        foreach ($auctions as $auction) {
            tableStart();
            echo "<tr>
                        <th>Anbieter</td>
                        <th>Auktion Start/Ende</td>
                        <th colspan=\"3\">Angebot</td>
                        <th>Status</td></tr>";

            //restliche zeit bis zum ende
            $rest_time = $auction->dateEnd - time();

            $t = floor($rest_time / 3600 / 24);
            $h = floor(($rest_time - ($t * 24 * 3600)) / 3600);
            $m = floor(($rest_time - ($t * 24 * 3600) - ($h * 3600)) / 60);
            $s = floor(($rest_time - ($t * 24 * 3600) - ($h * 3600) - ($m * 60)));

            $rest_time = "Noch $t t $h h $m m $s s";


            echo "<tr>
                                <td rowspan=\"5\">
                                    <a href=\"?page=user&amp;sub=edit&amp;user_id=" . $auction->userId . "\">" . get_user_nick($auction->userId) . "</a>
                                </td>
                                <td>
                                    Start " . date("d.m.Y  G:i:s", $auction->dateStart) . "
                                </td>";


            // Sind Schiffe angeboten
            if ($auction->shipId > 0) {
                echo "<td rowspan=\"5\">
                                    " . $auction->shipCount . " <a href=\"?page=help&site=shipyard&id=" . $auction->shipId . "\">" . $shipNames[$auction->shipId] . "</a>
                                </td>";
            } else {
                echo "<td rowspan=\"5\">Keine Schiffe</td>";
            }

            echo "<td>
                                <b>" . ResourceNames::METAL . "</b>:
                            </td>
                            <td>
                                " . StringUtils::formatNumber($auction->sell0) . "
                            </td>";

            // Zurückzieh button wenn noch niemand geboten hat
            if ($auction->currentBuyerId == 0) {
                echo "<td class=\"tbldata\" rowspan=\"5\"><input type=\"button\" onclick=\"if (confirm('Soll diese Auktion wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&auction_delete=" . $auction->id . "'\" value=\"L&ouml;schen\"/></td></tr>";
            } elseif ($auction->buyable == 0) {
                echo "<td class=\"tbldata\" rowspan=\"5\">Verkauft!<br><br><input type=\"button\" onclick=\"if (confirm('Soll diese Auktion wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&auction_delete=" . $auction->id . "'\" value=\"L&ouml;schen\"/></td></tr>";
            } else {
                echo "<td class=\"tbldata\" rowspan=\"5\">Es wurde bereits geboten<br><br><input type=\"button\" onclick=\"if (confirm('Soll diese Auktion wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&auction_delete=" . $auction->id . "'\" value=\"L&ouml;schen\"/></td></tr>";
            }

            // Start/Ende Anzeigen sofern die auktion nicht schon beendet ist
            if ($auction->dateEnd > time()) {
                echo "<tr>
                                    <td>
                                        Ende " . date("d.m.Y  G:i:s", $auction->dateEnd) . "
                                    </td>";
            }
            // sonst das löschdatum anzeigen
            else {
                $delete_rest_time = $auction->deleted - time();

                $t = floor($delete_rest_time / 3600 / 24);
                $h = floor(($delete_rest_time) / 3600);
                $m = floor(($delete_rest_time - ($h * 3600)) / 60);
                $s = floor(($delete_rest_time - ($h * 3600) - ($m * 60)));

                echo "<tr>
                                    <td>
                                        Auktion beendet
                                    </td>";
            }

            echo "		<td>
                                        <b>" . ResourceNames::CRYSTAL . "</b>:
                                    </td>
                                    <td>
                                        " . StringUtils::formatNumber($auction->sell1) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan=\"3\">
                                        $rest_time
                                    </td>
                                    <td>
                                        <b>" . ResourceNames::PLASTIC . "</b>:
                                    </td>
                                    <td>
                                        " . StringUtils::formatNumber($auction->sell2) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . ResourceNames::FUEL . "</b>:
                                    </td>
                                    <td>
                                        " . StringUtils::formatNumber($auction->sell3) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . ResourceNames::FOOD . "</b>:
                                    </td>
                                    <td>
                                        " . StringUtils::formatNumber($auction->sell4) . "
                                    </td>
                                </tr>";

            //Hochstgebot Anzeigen wenn schon geboten worden ist
            if ($auction->currentBuyerId != 0) {
                echo "<tr>
                                    <th colspan=\"6\">
                                        H&ouml;chstgebot
                                    </th>
                                </tr>";
                //Höchstbietender User anzeigen wenn vorhanden
                echo "<tr>
                                    <td rowspan=\"5\">
                                        <a href=\"?page=user&amp;sub=edit&amp;user_id=" . $auction->currentBuyerId . "\">" . get_user_nick($auction->currentBuyerId) . "</a>
                                    </td>
                                    <td rowspan=\"5\">
                                        Geboten " . date("d.m.Y  G:i:s", $auction->currentBuyerDate) . "
                                    </td>
                                    <td>
                                        <b>" . ResourceNames::METAL . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . StringUtils::formatNumber($auction->buy0) . "
                                    </td>
                                    <td rowspan=\"5\">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . ResourceNames::CRYSTAL . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . StringUtils::formatNumber($auction->buy1) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . ResourceNames::PLASTIC . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . StringUtils::formatNumber($auction->buy2) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . ResourceNames::FUEL . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . StringUtils::formatNumber($auction->buy3) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . ResourceNames::FOOD . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . StringUtils::formatNumber($auction->buy4) . "
                                    </td>
                                </tr>";
            }
            tableEnd();
        }
    } else {
        error_msg("Keine Angebote vorhanden", 1);
    }
} else {
    echo '<div style="float:left;">';
    echo "Willkommen bei der Marktplatzverwaltung. <br/><br/>";

    echo '<input type="button" value="Schiffe" onclick="document.location=\'?page=' . $page . '&amp;sub=ships\'" /><br/><br/>';
    echo '<input type="button" value="Rohstoffe" onclick="document.location=\'?page=' . $page . '&amp;sub=ress\'" /><br/><br/>';
    echo '<input type="button" value="Auktionen" onclick="document.location=\'?page=' . $page . '&amp;sub=auction\'" /><br/><br/>';

    echo "<h2>Rohstoffkurse</h2>";
    if (isset($_GET['action']) && $_GET['action'] == "updaterates") {
        $tr = new PeriodicTaskRunner($app);
        success_msg($tr->runTask(MarketrateUpdateTask::class));
    }

    echo "<table class=\"tb\" style=\"width:200px;\">";
    for ($i = 0; $i < NUM_RESOURCES; $i++) {
        echo "<tr><th>" . ResourceNames::NAMES[$i] . "</th><td>" . $runtimeDataStore->get('market_rate_' . $i, (string) 1) . "</td></tr>";
    }
    echo "</table>";

    echo '<p>Die Marktkurse werden periodisch neu berechnet.</p>';
    echo '<input type="button" value="Kurse manuell aktualisieren" onclick="document.location=\'?page=' . $page . '&amp;action=updaterates\'" /><br/><br/>';

    echo '</div>';

    echo '<img src="../misc/market.image.php" alt="Kursverlauf" style="float:right;" />';
}
