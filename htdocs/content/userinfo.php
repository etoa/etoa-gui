<?PHP

use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserLogRepository;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRatingSearch;
use EtoA\User\UserRepository;

/** @var UserLogRepository $userLogRepository */
$userLogRepository = $app[UserLogRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var AllianceRankRepository $allianceRankRepository */
$allianceRankRepository = $app[AllianceRankRepository::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var RaceDataRepository $raceRepository */
$raceRepository = $app[RaceDataRepository::class];

echo "<h1>Benutzerprofil</h1>";

if (!isset($_GET['id']))
    $uid = $cu->id;
elseif (intval($_GET['id']) > 0)
    $uid = intval($_GET['id']);
else
    $uid = 0;

if ($uid > 0) {
    $user = new User($uid);

    // Besuchercounter
    if ($user->id != $cu->id) {
        $userRepository->addVisit($user->id);
    }

    if ($user->isValid) {
        tableStart("Profil von " . $user->nick);
        if ($user->profileImage != "") {
            $imagePath = \EtoA\User\ProfileImage::IMAGE_PATH . $user->profileImage;
            $im = $app['app.webroot_dir'] . $imagePath;
            if (is_file($im)) {
                $ims = getimagesize($im);
                echo "<tr><td class=\"tblblack\" colspan=\"2\" style=\"text-align:center;background:#000;\">
                    <img src=\"" . $imagePath . "\" style=\"width:" . $ims[0] . "px;height:" . $ims[1] . "px;\" alt=\"Profil\" /></td></tr>";
            }
        }
        if ($user->profileText != "") {
            echo "<tr><td colspan=\"2\" style=\"text-align:center\">" . BBCodeUtils::toHTML($user->profileText) . "</td></tr>";
        }
        echo "<tr><th style=\"width:120px;\">Punkte:</th><td>" . StringUtils::formatNumber($user->points) . "</td></tr>";

        $race = $raceRepository->getRace($user->raceId);
        echo "<tr>
          <th>Rasse:</th>
          <td>" . $race->name . "</td>
      </tr>";
        if ($user->allianceId != 0) {
            echo "<tr><th style=\"width:120px;\">Allianz:</th><td>";
            $alliance = $allianceRepository->getAlliance($user->allianceId());
            if ($user->getId() === $alliance->founderId) {
                echo 'Gründer von ';
            } else {
                $rank = $allianceRankRepository->getRank($user->allianceRankId, $user->allianceId());
                if ($rank !== null) {
                    echo $rank->name . " von ";
                }
            }

            echo "<a href=\"?page=alliance&amp;id=" . $user->allianceId . "\">" . $alliance->nameWithTag     . "</a></td></tr>";
        }
        if ($user->visits > 0) {
            echo "<tr><th style=\"width:120px;\">Besucherz&auml;hler:</th><td>" . StringUtils::formatNumber($user->visits) . " Besucher</td></tr>";
        }
        if ($user->rank > 0) {
            echo "<tr><th style=\"width:120px;\">Aktueller Rang:</th><td>" . StringUtils::formatNumber($user->rank) . "</td></tr>";
        }
        if ($user->rankHighest > 0) {
            echo "<tr><th style=\"width:120px;\">Bester Rang:</th><td>" . StringUtils::formatNumber($user->rankHighest) . "</td></tr>";
        }

        /** @var UserRatingRepository $userRatingRepository */
        $userRatingRepository = $app[UserRatingRepository::class];

        $ratingSearch = UserRatingSearch::create()->id($user->id);

        $battleRating = $userRatingRepository->getBattleRating($ratingSearch)[0] ?? null;
        if ($battleRating !== null && $battleRating->rating > 0) {
            echo "<tr><th style=\"width:120px;\">Kampfpunkte:</th><td>" . StringUtils::formatNumber($battleRating->rating) . " (Gewonnen/Verloren/Total: " . StringUtils::formatNumber($battleRating->battlesWon) . "/" . StringUtils::formatNumber($battleRating->battlesLost) . "/" . StringUtils::formatNumber($battleRating->battlesFought) . ")</td></tr>";
        }

        $tradeRating = $userRatingRepository->getTradeRating($ratingSearch)[0] ?? null;
        if ($tradeRating !== null && $tradeRating->rating > 0) {
            echo "<tr><th style=\"width:120px;\">Handelspunkte:</th><td>" . StringUtils::formatNumber($tradeRating->rating) . " (Einkäufe/Verkäufe: " . StringUtils::formatNumber($tradeRating->tradesBuy) . "/" . StringUtils::formatNumber($tradeRating->tradesSell) . ")</td></tr>";
        }

        $diplomacyRating = $userRatingRepository->getDiplomacyRating($ratingSearch)[0] ?? null;
        if ($diplomacyRating !== null && $diplomacyRating->rating > 0) {
            echo "<tr><th style=\"width:120px;\">Diplomatiepunkte:</th><td>" . StringUtils::formatNumber($diplomacyRating->rating) . "</td></tr>";
        }

        if ($user->profileBoardUrl != "") {
            echo "<tr><th style=\"width:120px;\">Foren-Profil:</th><td><a href=\"" . $user->profileBoardUrl . "\">" . $user->profileBoardUrl . "</a></td></tr>";
        }
        if ($user->registered > 0) {
            echo "<tr><th style=\"width:120px;\">Registriert:</th><td>" . StringUtils::formatDate($user->registered) . " (dabei seit " . StringUtils::formatTimespan(time() - $user->registered) . ")</td></tr>";
        }
        if ($user->admin) {
            echo "<tr><th style=\"width:120px;\">Game-Admin:</th><td class=\"adminColor\">Dies ist ein Account eines Game-Admins. Er darf gemäss Regeln nicht angegriffen werden.</td></tr>";
        }
        if ($user->chatadmin) {
            echo "<tr><th style=\"width:120px;\">Chat-Admin:</th><td>Dies ist ein Account eines Chat-Admins. Er hat das Recht, Spieler im Chat zu kicken oder zu bannen.</td></tr>";
        }
        if ($user->ghost) {
            echo "<tr><th style=\"width:120px;\">Geist:</th><td>Dies ist ein Geist-Account. Er wird nicht in der Statistik angezeigt.</td></tr>";
        }

        tableEnd();

        //
        // User-Log
        //

        iBoxStart("&Ouml;ffentliches Benutzer-Log");
        $logs = $userLogRepository->getUserLogs($user->getId(), 10, true);
        if (count($logs) > 0) {
            foreach ($logs as $log) {
                echo "<div class=\"infoLog\">" . BBCodeUtils::toHTML($log->message);
                echo "<span>" . StringUtils::formatDate($log->timestamp, false) . "";
                echo "</span>
                    </div>";
            }
            echo "<div style=\"font-size:7pt;padding-top:6px;\">Nur die 10 neusten Nachrichten werden angezeigt.</div>";
        } else {
            echo "Keine Nachrichten!";
        }
        iBoxEnd();


        if ($user->id == $cu->id) {
            iBoxStart("Privates Benutzer-Log");
            $logs = $userLogRepository->getUserLogs($user->getId(), 30, false);
            if (count($logs) > 0) {
                foreach ($logs as $log) {
                    echo "<div class=\"infoLog\">" . BBCodeUtils::toHTML($log->message);
                    echo "<span>" . StringUtils::formatDate($log->timestamp) . "";
                    echo "</span>
                        </div>";
                }
                echo "<div style=\"font-size:7pt;padding-top:6px;\">Nur die 30 neusten Nachrichten werden angezeigt.</div>";
            } else {
                echo "Keine Nachrichten!";
            }
            iBoxEnd();


            $resourcesUser = $userRepository->getUser($cu->getId());
            if ($resourcesUser !== null) {
                iBoxStart("Rohstoffe von...");
                echo "Raids: " . StringUtils::formatNumber($resourcesUser->resFromRaid) . " t</br>";
                echo "Asteroiden: " . StringUtils::formatNumber($resourcesUser->resFromAsteroid) . " t</br>";
                echo "Nebelfelder: " . StringUtils::formatNumber($resourcesUser->resFromNebula) . " t</br>";
                echo "Trümmerfelder: " . StringUtils::formatNumber($resourcesUser->resFromTf) . " t";
                iBoxEnd();
            }
        }

        echo "<input type=\"button\" value=\"Nachricht senden\" onclick=\"document.location='?page=messages&amp;mode=new&amp;message_user_to=" . intval($user->id) . "'\" /> &nbsp; ";
        echo "<input type=\"button\" value=\"Statistiken anzeigen\" onclick=\"document.location='?page=stats&amp;mode=user&amp;userdetail=" . intval($user->id) . "'\" /> &nbsp; ";
    } else
        echo "<b>Fehler:</b> Dieser Spieler existiert nicht!<br/><br/>";
} else {
    echo "<b>Fehler:</b> Keine ID angegeben!<br/><br/>";
}

if (isset($_GET['id'])) {
    echo "<input type=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";
}
