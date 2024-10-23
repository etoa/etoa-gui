<?php declare(strict_types=1);

namespace EtoA\Fleet\Attack;

use EtoA\Entity\BattleLog;
use EtoA\Support\StringUtils;

class BanFinder
{
    private const MAX_ATTACKS_PER_WAVE = [3, 4];             // Max. 3er/4er Wellen...
    private const WAVE_TIME = 15 * 60;                       // ...innerhalb 15mins
    private const MAX_ATTACKS_PER_ENTITY = [2, 4];           // Max. 2/4 mal den gleichen Planeten angreiffen
    private const MAX_ATTACKED_ENTITIES = [5, 10];           // Max. Anzahl Planeten die angegriffen werden können...
    private const TIME_BETWEEN_ATTACKS_ON_ENTITY = 6 * 3600; // ...innerhalb 6h
    private const BAN_RANGE = 24 * 3600;                     // alle Regeln gelten innerhalb von 24h

    /**
     * @param BattleLog[] $logs
     * @return Ban[]
     */
    public function find(array $logs): array
    {
        if (count($logs) === 0) {
            return [];
        }

        $bans = [];

        //Alle Daten werden in einem Array gespeichert, da mehr als 1 Angriffer möglich ist funktioniert das alte Tool nicht mehr
        $data = [];
        foreach ($logs as $log) {
            $attackingUserIds = explode(",", $log->getFleetUserIds());
            $defendingUserIds = explode(",", $log->getEntityUserIds());
            $entityUserId = $defendingUserIds[1];
            foreach ($attackingUserIds as $atackingUserId) {
                if ($atackingUserId != "") {
                    $data[$atackingUserId][$entityUserId][$log->getEntityId()][] = [$log->getLandTime(), $log->isWar(), $log->getAction()];
                }
            }
        }

        foreach ($data as $fUser => $eUserArr) {
            foreach ($eUserArr as $eUser => $eArr) {
                $firstTime = 0;
                $attackedEntities = count($eArr);

                foreach ($eArr as $entityId => $eDataArr) {
                    $firstPlanetTime = 0;
                    $lastPlanetTime = 0;
                    $attackCntEntity = 0;
                    $waveCnt = 0;
                    $waveStart = 0;
                    $waveEnd = 0;
                    $lastWave = 0;

                    foreach ($eDataArr as $eData) {
                        $ban = false;
                        $banReason = "";
                        if ($firstTime === 0) {
                            $firstTime = $eData[0];

                            // Wenn mehr als 5 Planeten angegrifen wurden
                            if ($attackedEntities > self::MAX_ATTACKED_ENTITIES[$eData[1]]) {
                                $ban = true;
                                $banReason .= "Mehr als " . self::MAX_ATTACKED_ENTITIES[$eData[1]] . " innerhalb von " . (self::BAN_RANGE / 3600) . " Stunden.<br/>Anzahl: " . $attackedEntities . "<br/><br/>";
                            }
                        }

                        if ($firstPlanetTime === 0) {
                            $firstPlanetTime = $eData[0];
                        }

                        if ($lastPlanetTime === 0) {
                            $lastPlanetTime = $eData[0];
                        }

                        //Wellenreset
                        if ($waveStart === 0 || $waveEnd <= $eData[0] - self::WAVE_TIME) {
                            $lastWave = $waveEnd;
                            $waveStart = $eData[0];
                            $waveEnd = $eData[0];
                            $waveCnt = 1;
                            ++$attackCntEntity;
                        } else {
                            ++$waveCnt;
                            $waveEnd = $eData[0];
                        }

                        //
                        // Überprüfungen
                        //

                        //Zu viele Angriffe in einer Welle
                        if ($waveCnt > self::MAX_ATTACKS_PER_WAVE[$eData[1]]) {
                            $ban = true;
                            $banReason .= "Mehr als " . self::MAX_ATTACKS_PER_WAVE[$eData[1]] . " Angriffe in einer Welle auf dem selben Ziel.<br />Anzahl Angriffe : " . $waveCnt . "<br />Dauer der Welle: " . StringUtils::formatTimespan($waveEnd - $waveStart) . "<br /><br />";
                        }

                        // Sperre keine 6h gewartet zwischen Angriffen auf einen Planeten
                        if ($waveCnt === 1 && $eData[0] > $lastWave && $eData[0] < $lastWave + self::TIME_BETWEEN_ATTACKS_ON_ENTITY) {
                            $ban = true;
                            $banReason .= "Der Abstand zwischen 2 Angriffen/Wellen auf ein Ziel ist kleiner als " . (self::TIME_BETWEEN_ATTACKS_ON_ENTITY / 3600) . " Stunden.<br />Dauer zwischen den beiden Angriffen: " . StringUtils::formatTimespan($eData[0] - $lastWave) . "<br /><br />";
                        }

                        // Sperre wenn mehr als 2/4 Angriffe pro Planet
                        if ($waveCnt === 1 && $attackCntEntity > self::MAX_ATTACKS_PER_ENTITY[$eData[1]]) {
                            $ban = true;
                            $banReason .= "Mehr als " . self::MAX_ATTACKS_PER_ENTITY[$eData[1]] . " Angriffe/Wellen auf ein Ziel.<br />Anzahl:" . $attackCntEntity . "<br /><br />";
                        }

                        // Es liegt eine Angriffsverletzung vor
                        if ($ban) {
                            $bans[] = new Ban($eData[2], $eData[0], (int) $fUser, (int) $eUser, $entityId, $banReason);
                        }
                    }
                }
            }
        }

        return $bans;
    }
}
