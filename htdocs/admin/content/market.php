<?PHP

use EtoA\Market\MarketAuctionRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\RuntimeDataStore;

/** @var RuntimeDataStore */
$runtimeDataStore = $app[RuntimeDataStore::class];
/** @var MarketAuctionRepository $marketAuctionRepository */
$marketAuctionRepository = $app[MarketAuctionRepository::class];

define("USER_MESSAGE_CAT_ID", 1);
define("SYS_MESSAGE_CAT_ID", 5);

echo "<h1>Marktplatz</h1>";

if ($sub == "ress") {
    echo "<h2>Rohstoffe</h2>";
    if (isset($_GET['ressource_delete']) && $_GET['ressource_delete'] > 0) {
        dbquery("DELETE FROM market_ressource WHERE id=" . $_GET['ressource_delete'] . "");
        echo MessageBox::ok("", "Angebot gel&ouml;scht!");
    }
    $res = dbquery("SELECT * FROM market_ressource ORDER BY datum ASC");
    if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_array($res)) {
            $username = get_user_nick($arr['user_id']);
            echo "<table class=\"tb\">";
            echo "<tr>
                        <th width=\"100\">
                            Datum:
                        </th>
                        <td colspan=\"2\" width=\"200\">
                            " . date("d.m.Y - H:i:s", $arr['datum']) . "
                        </td>
                        <th width=\"100\">
                            Spieler:
                        </th>
                        <td width=\"100\">
                            <a href=\"?page=user&amp;sub=edit&amp;user_id=" . $arr['user_id'] . "\">" . $username . "</a></td><td class=\"tbltitle\"><input type=\"button\" onclick=\"if (confirm('Soll dieses Angebot wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&ressource_delete=" . $arr['id'] . "'\" value=\"L&ouml;schen\"/>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan=\"6\">
                            Angebot:
                        </th>";
            $first = true;
            foreach ($resNames as $k => $v) {
                if (!$first) echo "<tr>";
                echo "	<td width=\"110\">" . $v . "</td>
                                    <td width=\"100\">
                                        " . nf($arr['sell_' . $k . '']) . "
                                    </td>";
                if ($first) {
                    echo "<th rowspan=\"5\">Preis:</th>";
                    $first = false;
                }
                echo    "<td width=\"110\">" . $v . "</td>
                                    <td width=\"100\">
                                        " . nf($arr['buy_' . $k . '']) . "
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
        dbquery("DELETE FROM market_ship WHERE id=" . $_GET['ship_delete'] . "");
        echo MessageBox::ok("", "Angebot gel&ouml;scht");
    }
    $res = dbquery("SELECT * FROM market_ship ORDER BY datum DESC;");
    if (mysql_num_rows($res) > 0) {
        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $app[ShipDataRepository::class];
        $shipNames = $shipRepository->getShipNames(true);
        while ($arr = mysql_fetch_array($res)) {
            $username = get_user_nick($arr['user_id']);
            echo "<form action=\"?page=$page&sub=$sub\" method=\"POST\">\n";
            echo "<input type=\"hidden\" name=\"ship_market_id\" value=\"" . $arr['id'] . "\">";
            echo "<table class=\"tb\">
                        <tr>
                            <th width=\"100\">
                                Datum:
                            </th>
                            <td colspan=\"2\" width=\"200\">
                                " . date("d.m.Y - H:i:s", $arr['datum']) . "
                            </td>
                            <th width=\"100\">
                                Spieler:
                            </th>
                            <td width=\"100\">
                                <a href=\"?page=user&amp;sub=edit&amp;user_id=" . $arr['user_id'] . "\">" . $username . "</a>
                            </td>
                            <td rowspan=\"4\">
                                <input type=\"button\" onclick=\"if (confirm('Soll dieses Angebot wirklich gel&ouml;scht werden?')) document.location='?page=$page&sub=$sub&ship_delete=" . $arr['id'] . "'\" value=\"L&ouml;schen\"/>
                            </td>
                        </tr>
                        <tr>
                            <th width=\"100\">
                                Schiffname:
                            </th>
                            <td colspan=\"2\" width=\"200\">
                                " . $shipNames[$arr['ship_id']] . "
                            </td>
                            <th width=\"100\">
                                Anzahl:
                            </td>
                            <td width=\"100\">
                                " . $arr['count'] . "
                            </td>
                        </tr>
                        <tr>";
            foreach ($resNames as $k => $v) {
                echo "<th width=\"100\">
                            " . $v . "
                        </th>";
            }
            echo "</tr>
                        <tr>";
            foreach ($resNames as $k => $v) {
                echo "<td width=\"100\">
                            " . nf($arr['costs_' . $k . '']) . "
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
                                <b>" . RES_METAL . "</b>:
                            </td>
                            <td>
                                " . nf($auction->sell0) . "
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
                                        <b>" . RES_CRYSTAL . "</b>:
                                    </td>
                                    <td>
                                        " . nf($auction->sell1) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan=\"3\">
                                        $rest_time
                                    </td>
                                    <td>
                                        <b>" . RES_PLASTIC . "</b>:
                                    </td>
                                    <td>
                                        " . nf($auction->sell2) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . RES_FUEL . "</b>:
                                    </td>
                                    <td>
                                        " . nf($auction->sell3) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . RES_FOOD . "</b>:
                                    </td>
                                    <td>
                                        " . nf($auction->sell4) . "
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
                                        <b>" . RES_METAL . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . nf($auction->buy0) . "
                                    </td>
                                    <td rowspan=\"5\">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . RES_CRYSTAL . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . nf($auction->buy1) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . RES_PLASTIC . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . nf($auction->buy2) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . RES_FUEL . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . nf($auction->buy3) . "
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>" . RES_FOOD . "</b>:
                                    </td>
                                    <td colspan=\"2\">
                                        " . nf($auction->buy4) . "
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
        echo "<tr><th>" . $resNames[$i] . "</th><td>" . $runtimeDataStore->get('market_rate_' . $i, (string) 1) . "</td></tr>";
    }
    echo "</table>";

    echo '<p>Die Marktkurse werden periodisch neu berechnet.</p>';
    echo '<input type="button" value="Kurse manuell aktualisieren" onclick="document.location=\'?page=' . $page . '&amp;action=updaterates\'" /><br/><br/>';

    echo '</div>';

    echo '<img src="../misc/market.image.php" alt="Kursverlauf" style="float:right;" />';
}
