<?PHP

use EtoA\Alliance\AllianceNewsRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSpendRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

$xajax->register(XAJAX_FUNCTION, "allianceNewsSave");
$xajax->register(XAJAX_FUNCTION, "allianceNewsLoad");
$xajax->register(XAJAX_FUNCTION, "allianceNewsEdit");
$xajax->register(XAJAX_FUNCTION, "allianceNewsLoadUserList");
$xajax->register(XAJAX_FUNCTION, "allianceNewsDel");
$xajax->register(XAJAX_FUNCTION, "allianceNewsRemoveOld");
$xajax->register(XAJAX_FUNCTION, "allianceNewsSetBanTime");

$xajax->register(XAJAX_FUNCTION, "showSpend");

function allianceNewsLoad()
{
    global $app;

    /** @var AllianceNewsRepository $allianceNewsRepository */
    $allianceNewsRepository = $app[AllianceNewsRepository::class];
    ob_start();

    $allNews = $allianceNewsRepository->getNewsEntries(null);
    if (count($allNews) > 0) {
        echo '<table class="tb">';
        echo '<tr>
            <th>Datum</th>
            <th>Absender</th>
            <th>Empfänger</th>
            <th>Titel / Text</th>
            </tr>';
        foreach ($allNews as $news) {
            echo '<tr>
                <td rowspan="2">' . StringUtils::formatDate($news->date) . '</td>';
            echo '<td id="news_' . $news->id . '_alliance" style="border-bottom:1px dotted #999;"><b>';
            if ($news->authorAllianceTag != '') {
                echo '[' . $news->authorAllianceTag . '] ' . $news->authorAllianceName;
            } else {
                echo '<span style="color:#999;">Allianz existiert nicht!</span>';
            }
            echo '</b></td>';
            echo '<td id="news_' . $news->id . '_alliance_to" style="border-bottom:none;"><b>';
            if ($news->toAllianceTag != '') {
                echo '[' . $news->toAllianceTag . '] ' . $news->toAllianceName;
            } else {
                echo '<span style="color:#999;">Allianz existiert nicht!</span>';
            }
            echo '</b></td>';
            echo '<td id="news_' . $news->id . '_title" style="border-bottom:1px dotted #999;';
            echo '"><b>' . stripslashes($news->title) . '</b></td>';
            echo '<td rowspan="2" id="news_' . $news->id . '_actions">
                <a href="javascript:;" onclick="xajax_allianceNewsEdit(' . $news->id . ');"><img src="../images/edit.gif" alt="Edit" style="border:none;" /></a>
                <a href="javascript:;" onclick="if (confirm(\'Beitrag löschen?\')) xajax_allianceNewsDel(' . $news->id . ');"><img src="../images/delete.gif" alt="Delete" style="border:none;" /></a>';
            if ($news->authorUserId > 0) {
                echo '<a href="javascript:;" onclick="if (confirm(\'Benutzer sperren?\')) xajax_lockUser(' . $news->authorUserId . ',document.getElementById(\'ban_timespan\').options[document.getElementById(\'ban_timespan\').selectedIndex].value,document.getElementById(\'ban_text\').value);"><img src="../images/lock.png" alt="Lock" style="border:none;" /></a>';
            }
            echo '</td>';
            echo '</tr><tr>';
            echo '<td style="border-top:none;" id="news_' . $news->id . '_user">';
            if ($news->authorUserNick != '') {
                echo $news->authorUserNick;
            } else {
                echo '<span style="color:#999;">Spieler existiert nicht!</span>';
            }
            echo '</td>';
            echo '<td style="border-top:none;" id="news_' . $news->id . '_public"></td>';
            echo '<td style="border-top:none;" id="news_' . $news->id . '_text">' . stripslashes($news->text) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<i>Keine News vorhanden!</i>';
    }

    $objResponse = new xajaxResponse();
    $objResponse->assign("newsBox", "innerHTML", ob_get_contents());
    ob_end_clean();
    return $objResponse;
}

function allianceNewsDel($id)
{
    global $app;

    /** @var AllianceNewsRepository $allianceNewsRepository */
    $allianceNewsRepository = $app[AllianceNewsRepository::class];
    $allianceNewsRepository->deleteEntry($id);

    $objResponse = new xajaxResponse();
    $objResponse->script("xajax_allianceNewsLoad()");
    return $objResponse;
}

function allianceNewsRemoveOld($ts)
{
    global $app;

    $t = time() - $ts;

    /** @var AllianceNewsRepository $allianceNewsRepository */
    $allianceNewsRepository = $app[AllianceNewsRepository::class];
    $allianceNewsRepository->deleteOlderThan($t);

    $objResponse = new xajaxResponse();
    $objResponse->alert(mysql_affected_rows() . " Beiträge wurden gelöscht!");
    $objResponse->script("xajax_allianceNewsLoad()");
    return $objResponse;
}

function allianceNewsEdit($id)
{
    global $app;

    /** @var AllianceNewsRepository $allianceNewsRepository */
    $allianceNewsRepository = $app[AllianceNewsRepository::class];

    $objResponse = new xajaxResponse();

    $newsIds = $allianceNewsRepository->getNewsIds();
    foreach ($newsIds as $newsId) {
        if ($newsId != $id) {
            $objResponse->assign("news_" . $newsId . "_actions", "innerHTML", '');
        }
    }

    $news = $allianceNewsRepository->getEntry($id);
    if ($news !== null) {
        /** @var AllianceRepository $allianceRepository */
        $allianceRepository = $app[AllianceRepository::class];
        $alliances = $allianceRepository->getAllianceNamesWithTags();

        $out = '<select name="alliance_id" onchange="xajax_allianceNewsLoadUserList(' . $id . ',this.options[this.selectedIndex].value,0);"><option value="0">(keine)</option>';
        $ca = 0;
        foreach ($alliances as $k => $v) {
            $out .= '<option value="' . $k . '"';
            if ($k === $news->authorAllianceId) {
                $ca = $k;
                $out .= ' selected="selected"';
            }
            $out .= '>' . $v . '</option>';
        }
        $out .= '</select>';
        $objResponse->assign("news_" . $id . "_alliance", "innerHTML", $out);

        $out = '<select name="alliance_to_id"><option value="0">(keine)</option>';
        foreach ($alliances as $k => $v) {
            $out .= '<option value="' . $k . '"';
            if ($k === $news->toAllianceId) {
                $out .= ' selected="selected"';
            }
            $out .= '>' . $v . '</option>';
        }
        $out .= '</select>';
        $objResponse->assign("news_" . $id . "_alliance_to", "innerHTML", $out);

        $objResponse->assign("news_" . $id . "_public", "innerHTML", $out);

        $objResponse->assign("news_" . $id . "_user", "innerHTML", 'Lade Spieler...');
        $objResponse->script("xajax_allianceNewsLoadUserList(" . $id . "," . $ca . "," . $news->authorUserId . ");");

        $out = '<textarea name="text" rows="6" cols="45" >' . stripslashes($news->text) . '</textarea>';
        $objResponse->assign("news_" . $id . "_text", "innerHTML", $out);

        $out = '<input type="text" name="title" size="45" value="' . stripslashes($news->title) . '" />';
        $objResponse->assign("news_" . $id . "_title", "innerHTML", $out);

        $out = '<input type="button" onclick="xajax_allianceNewsSave(' . $id . ',xajax.getFormValues(\'newsForm\'))" value="Speichern" /><br/>
        <input type="button" onclick="xajax_allianceNewsLoad()" value="Abbrechen" />';
        $objResponse->assign("news_" . $id . "_actions", "innerHTML", $out);
    }
    return $objResponse;
}



function allianceNewsLoadUserList($nid, $aid, $uid)
{
    global $app;

    $objResponse = new xajaxResponse();
    $out = '';
    if ($aid > 0) {
        $out = '<select name="user_id"><option value="0">(keiner)</option>';

        /** @var UserRepository $userRepository */
        $userRepository = $app[UserRepository::class];
        $members = $userRepository->searchUserNicknames(UserSearch::create()->allianceId($aid));
        foreach ($members as $memberId => $memberNick) {
            $out .= '<option value="' . $memberId . '"';
            if ($uid == $memberId) {
                $out .= ' selected="selected"';
            }
            $out .= '>' . $memberNick . '</option>';
        }
        $out .= '</select>';
    } else {
        $out .= '<option value="0">(Keine Allianz gewählt)</option>';
    }
    $out .= '</select>';

    $objResponse->assign("news_" . $nid . "_user", "innerHTML", $out);

    return $objResponse;
}

function allianceNewsSave($id, $form)
{
    global $app;

    /** @var AllianceNewsRepository $allianceNewsRepository */
    $allianceNewsRepository = $app[AllianceNewsRepository::class];
    $allianceNewsRepository->update($id, $form['user_id'], $form['alliance_id'], $form['title'], $form['text'], $form['alliance_to_id']);

    $objResponse = new xajaxResponse();
    $objResponse->script("xajax_allianceNewsLoad()");
    return $objResponse;
}

function allianceNewsSetBanTime($time, $text)
{
    // TODO
    global $app;

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];

    $config->set('townhall_ban', $time, $text);

    $objResponse = new xajaxResponse();
    $objResponse->alert("Einstellungen gespeichert!");
    return $objResponse;
}

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
