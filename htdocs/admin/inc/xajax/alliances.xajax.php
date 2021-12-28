<?PHP

use EtoA\Alliance\AllianceSpendRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

$xajax->register(XAJAX_FUNCTION, "showSpend");

function showSpend($allianceId, $form)
{
    global $app;

    ob_start();

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $memberNicks = $userRepository->searchUserNicknames(UserSearch::create()->allianceId($allianceId));

    $sum = false;
    $user = 0;
    $limit = 0;

    // Summierung der Einzahlungen
    if ($form['output'] == 1) {
        $sum = true;
    }

    // Limit
    if ($form['limit'] > 0) {
        $limit = $form['limit'];
    }

    // User
    if ($form['user_spends'] > 0) {
        $user = $form['user_spends'];
    }

    /** @var AllianceSpendRepository $allianceSpendRepository */
    $allianceSpendRepository = $app[AllianceSpendRepository::class];
    if ($sum) {
        if ($user > 0) {
            $user_message = "von " . $memberNicks[$user] . " ";
        } else {
            $user_message = "";
        }

        echo "Es werden die bisher eingezahlten Rohstoffe " . $user_message . " angezeigt.<br><br>";

        // Läd Einzahlungen
        $resources = $allianceSpendRepository->getTotalSpent($allianceId, $user);
        if ($resources->getSum() > 0) {
            tableStart("Total eingezahlte Rohstoffe " . $user_message . "");
            echo "<tr>
                            <th class=\"resmetalcolor\" style=\"width:20%\">" . ResourceNames::METAL . "</th>
                            <th class=\"rescrystalcolor\" style=\"width:20%\">" . ResourceNames::CRYSTAL . "</th>
                            <th class=\"resplasticcolor\" style=\"width:20%\">" . ResourceNames::PLASTIC . "</th>
                            <th class=\"resfuelcolor\" style=\"width:20%\">" . ResourceNames::FUEL . "</th>
                            <th class=\"resfoodcolor\" style=\"width:20%\">" . ResourceNames::FOOD . "</th>
                        </tr>";
            echo "<tr>
                            <td>" . StringUtils::formatNumber($resources->metal) . "</td>
                            <td>" . StringUtils::formatNumber($resources->crystal) . "</td>
                            <td>" . StringUtils::formatNumber($resources->plastic) . "</td>
                            <td>" . StringUtils::formatNumber($resources->fuel) . "</td>
                            <td>" . StringUtils::formatNumber($resources->food) . "</td>
                        </tr>";
            tableEnd();
        } else {
            iBoxStart("Einzahlungen");
            echo "Es wurden noch keine Rohstoffe eingezahlt!";
            iBoxEnd();
        }
    }
    // Einzahlungen werden einzelen ausgegeben
    else {
        if ($user > 0) {
            $user_message = "von " . $memberNicks[$user] . " ";
        } else {
            $user_message = "";
        }

        if ($limit > 0) {
            if ($limit == 1) {
                echo "Es wird die letzte Einzahlung " . $user_message . "gezeigt.<br><br>";
            } else {
                echo "Es werden die letzten " . $limit . " Einzahlungen " . $user_message . "gezeigt.<br><br>";
            }
        } else {
            echo "Es werden alle bisherigen Einzahlungen " . $user_message . "gezeigt.<br><br>";
        }

        // Läd Einzahlungen
        $spendEntries = $allianceSpendRepository->getSpent($allianceId, $user, (int) $limit);
        if (count($spendEntries) > 0) {
            foreach ($spendEntries as $entry) {
                tableStart("" . $memberNicks[$entry->userId] . " - " . StringUtils::formatDate($entry->time) . "");
                echo "<tr>
                                <th class=\"resmetalcolor\" style=\"width:20%\">" . ResourceNames::METAL . "</th>
                                <th class=\"rescrystalcolor\" style=\"width:20%\">" . ResourceNames::CRYSTAL . "</th>
                                <th class=\"resplasticcolor\" style=\"width:20%\">" . ResourceNames::PLASTIC . "</th>
                                <th class=\"resfuelcolor\" style=\"width:20%\">" . ResourceNames::FUEL . "</th>
                                <th class=\"resfoodcolor\" style=\"width:20%\">" . ResourceNames::FOOD . "</th>
                            </tr>";
                echo "<tr>
                                <td>" . StringUtils::formatNumber($entry->metal) . "</td>
                                <td>" . StringUtils::formatNumber($entry->crystal) . "</td>
                                <td>" . StringUtils::formatNumber($entry->plastic) . "</td>
                                <td>" . StringUtils::formatNumber($entry->fuel) . "</td>
                                <td>" . StringUtils::formatNumber($entry->food) . "</td>
                            </tr>";
                tableEnd();
            }
        } else {
            iBoxStart("Einzahlungen");
            echo "Es wurden noch keine Rohstoffe eingezahlt!";
            iBoxEnd();
        }
    }

    $objResponse = new xajaxResponse();
    $objResponse->assign("spends", "innerHTML", ob_get_contents());
    ob_end_clean();
    return $objResponse;
}
