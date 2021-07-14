<?PHP

/**
 * Checks current wars / peace between alliances
 * if they're still valid
 */
class WarPeaceUpdateTask implements IPeriodicTask
{
    function run()
    {
        global $app;
        $time = time();

        // Assign diplomacy points for pacts
        $res = dbquery("
			SELECT
				alliance_bnd_id,
				alliance_bnd_diplomat_id,
				alliance_bnd_alliance_id1,
				alliance_bnd_alliance_id2,
				alliance_bnd_points
			FROM
				alliance_bnd
			WHERE
				alliance_bnd_date<" . ($time - DIPLOMACY_POINTS_MIN_PACT_DURATION) . "
				AND alliance_bnd_points>0
				AND alliance_bnd_level=2
			");
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                $user = new User($arr['alliance_bnd_diplomat_id']);
                $user->rating->addDiplomacyRating($arr['alliance_bnd_points'], "Bündnis " . $arr['alliance_bnd_alliance_id1'] . " mit " . $arr['alliance_bnd_alliance_id1']);
                dbquery("
					UPDATE
						alliance_bnd
					SET
						alliance_bnd_points=0
					WHERE
						alliance_bnd_id=" . $arr['alliance_bnd_id'] . "
					");
            }
        }

        $cnt = 0;

        // Wars
        $res = dbquery("
			SELECT
				alliance_bnd_id,
				a1.alliance_id as a1id,
				a2.alliance_id as a2id,
				a1.alliance_name as a1name,
				a2.alliance_name as a2name,
				a1.alliance_tag as a1tag,
				a2.alliance_tag as a2tag,
				a1.alliance_founder_id as a1f,
				a2.alliance_founder_id as a2f,
				alliance_bnd_points,
				alliance_bnd_diplomat_id
			FROM
				alliance_bnd
			INNER JOIN
				alliances as a1
				ON a1.alliance_id=alliance_bnd_alliance_id1
			INNER JOIN
				alliances as a2
				ON a2.alliance_id=alliance_bnd_alliance_id2
			WHERE
				alliance_bnd_date<" . ($time - WAR_DURATION) . "
				AND alliance_bnd_level=3
			");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                // Add log
                $text = "Der Krieg zwischen [b][" . $arr['a1tag'] . "] " . $arr['a1name'] . "[/b] und [b][" . $arr['a2tag'] . "] " . $arr['a2name'] . "[/b] ist zu Ende! Es folgt eine Friedenszeit von " . round(PEACE_DURATION / 3600) . " Stunden.";
                /** @var \EtoA\Alliance\AllianceHistoryRepository $allianceHistoryRepository */
                $allianceHistoryRepository = $app[\EtoA\Alliance\AllianceHistoryRepository::class];
                $allianceHistoryRepository->addEntry((int) $arr['a1id'], $text);
                $allianceHistoryRepository->addEntry((int) $arr['a2id'], $text);

                // Send message to leader
                /** @var \EtoA\Message\MessageRepository $messageRepository */
                $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                $messageRepository->createSystemMessage((int) $arr['a1f'], MSG_ALLYMAIL_CAT, "Krieg beendet", $text . " Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");
                $messageRepository->createSystemMessage((int) $arr['a2f'], MSG_ALLYMAIL_CAT, "Krieg beendet", $text . " Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");

                // Assing diplomacy points
                $user = new User($arr['alliance_bnd_diplomat_id']);
                $user->rating->addDiplomacyRating($arr['alliance_bnd_points'], "Krieg " . $arr['a1id'] . " gegen " . $arr['a2id']);

                dbquery("
					UPDATE
						alliance_bnd
					SET
						alliance_bnd_level=4,
						alliance_bnd_date=" . $time . ",
						alliance_bnd_points=0
					WHERE
						alliance_bnd_id=" . $arr['alliance_bnd_id'] . "
					");
            }
            $cnt += $nr;
        }

        // Peaces
        $res = dbquery("
			SELECT
				alliance_bnd_id,
				a1.alliance_id as a1id,
				a2.alliance_id as a2id,
				a1.alliance_name as a1name,
				a2.alliance_name as a2name,
				a1.alliance_tag as a1tag,
				a2.alliance_tag as a2tag,
				a1.alliance_founder_id as a1f,
				a2.alliance_founder_id as a2f
			FROM
				alliance_bnd
			INNER JOIN
				alliances as a1
				ON a1.alliance_id=alliance_bnd_alliance_id1
			INNER JOIN
				alliances as a2
				ON a2.alliance_id=alliance_bnd_alliance_id2
			WHERE
				alliance_bnd_date<" . ($time - PEACE_DURATION) . "
				AND alliance_bnd_level=4
			");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                // Add log
                $text = "Der Friedensvertrag zwischen [b][" . $arr['a1tag'] . "] " . $arr['a1name'] . "[/b] und [b][" . $arr['a2tag'] . "] " . $arr['a2name'] . "[/b] ist abgelaufen. Ihr könnt einander nun wieder Krieg erklären.";
                /** @var \EtoA\Alliance\AllianceHistoryRepository $allianceHistoryRepository */
                $allianceHistoryRepository = $app[\EtoA\Alliance\AllianceHistoryRepository::class];
                $allianceHistoryRepository->addEntry((int) $arr['a1id'], $text);
                $allianceHistoryRepository->addEntry((int) $arr['a2id'], $text);

                // Send message to leader
                /** @var \EtoA\Message\MessageRepository $messageRepository */
                $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                $messageRepository->createSystemMessage((int) $arr['a1f'], MSG_ALLYMAIL_CAT, "Friedensvertrag abgelaufen", $text);
                $messageRepository->createSystemMessage((int) $arr['a2f'], MSG_ALLYMAIL_CAT, "Friedensvertrag abgelaufen", $text);

                dbquery("
					DELETE FROM
						alliance_bnd
					WHERE
						alliance_bnd_id=" . $arr['alliance_bnd_id'] . "
					");
            }
            $cnt += $nr;
        }

        return "$cnt diplomatische Beziehungen (Krieg / Frieden) aktualisiert";
    }

    function getDescription()
    {
        return "Krieg/Frieden Status aktualisieren";
    }
}
