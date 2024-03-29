<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;

class MissileBattleHandler
{
    private ConfigurationService $config;
    private PlanetRepository $planetRepository;
    private MissileRepository $missileRepository;
    private MissileFlightRepository $missileFlightRepository;
    private MessageRepository $messageRepository;
    private MissileDataRepository $missileDataRepository;
    private DefenseDataRepository $defenseDataRepository;
    private DefenseRepository $defenseRepository;
    private BuildingRepository $buildingRepository;

    public function __construct(ConfigurationService $config, PlanetRepository $planetRepository, MissileRepository $missileRepository, MissileFlightRepository $missileFlightRepository, MessageRepository $messageRepository, MissileDataRepository $missileDataRepository, DefenseDataRepository $defenseDataRepository, DefenseRepository $defenseRepository, BuildingRepository $buildingRepository)
    {
        $this->config = $config;
        $this->planetRepository = $planetRepository;
        $this->missileRepository = $missileRepository;
        $this->missileFlightRepository = $missileFlightRepository;
        $this->messageRepository = $messageRepository;
        $this->missileDataRepository = $missileDataRepository;
        $this->defenseDataRepository = $defenseDataRepository;
        $this->defenseRepository = $defenseRepository;
        $this->buildingRepository = $buildingRepository;
    }

    public function battle(MissileFlight $flight): void
    {
        // Kampf abbrechen und Raketen zum Startplanet schicken wenn Kampfsperre aktiv ist
        if ($this->config->getBoolean('battleban') && $this->config->param1Int('battleban_time') <= time() && $this->config->param2Int('battleban_time') > time()) {
            $planetUserId = $this->planetRepository->getPlanetUserId($flight->entityFromId);
            // Transferiert Raketen zum Startplanet
            foreach ($flight->missiles as $missileId => $count) {
                $this->missileRepository->addMissile($missileId, $count, $planetUserId, $flight->entityFromId);
            }

            // Löscht Flug
            $this->missileFlightRepository->deleteFlight($flight->id, $flight->entityFromId);

            // Schickt Nachricht an den Angreifer
            $msg = $this->config->param2('battleban_arrival_text');
            $this->messageRepository->createSystemMessage($planetUserId, MessageCategoryId::SHIP_WAR, 'Ergebnis des Raketenangriffs', $msg);

            return;
        }

        if ($flight->entityFromId > 0) {
            $targetUserId = $this->planetRepository->getPlanetUserId($flight->targetPlanetId);

            $missiles = $this->missileDataRepository->getMissiles();
            $this->missileFlightRepository->deleteFlight($flight->id, $flight->entityFromId);
            if (count($flight->missiles) > 0) {
                // Select all attacking missiles
                /** @var array<int, Missile> $attackingMissiles */
                $attackingMissiles = [];
                $attackingMissilesCount = 0;
                foreach ($flight->missiles as $missileId => $count) {
                    $missile = $missiles[$missileId];
                    for ($x = 0; $x < $count; $x++) {
                        $attackingMissiles[$attackingMissilesCount] = $missile->damage;
                        $attackingMissiles[$attackingMissilesCount] = $missile->deactivate;
                        $attackingMissilesCount++;
                    }
                }
                // Shuffle their order
                shuffle($attackingMissiles);

                // Select anti-missiles from target
                $missileList = $this->missileRepository->findForUser($targetUserId, $flight->targetPlanetId);
                $defendingMissiles = [];
                $defendingMissilesCounts = [];
                $defendingMissileCount = 0;
                foreach ($missileList as $item) {
                    $missile = $missiles[$item->missileId];
                    if ($missile->def > 0) {
                        $defendingMissilesCounts[$item->id] = $item->count;
                        for ($x = 0; $x < $item->count; $x++) {
                            $defendingMissiles[$defendingMissileCount]['id'] = $item->id;
                            $defendingMissiles[$defendingMissileCount]['d'] = $missile->def;
                            $defendingMissiles[$defendingMissileCount]['n'] = $missile->name;
                            $defendingMissileCount++;
                        }
                    }
                }
                $dmcnt_start = $defendingMissileCount;

                shuffle($defendingMissiles);

                $dm_copy = $defendingMissiles;
                $dmcnt_copy = $defendingMissileCount;
                $def_report = "";
                for ($x = 0; $x < $defendingMissileCount; $x++) {
                    $def_report .= "Feuere " . $dm_copy[$x]['n'] . " ab...\n";
                    for ($y = 0; $y < $dm_copy[$x]['d']; $y++) {
                        $def_report .= "Angreifende Rakete wird getroffen!\n";
                        array_pop($attackingMissiles);
                        $attackingMissilesCount--;
                    }
                    $missileList = array_pop($defendingMissiles);
                    $defendingMissilesCounts[$missileList['id']]--;
                    $dmcnt_copy--;
                    if ($attackingMissilesCount <= 0) {
                        break;
                    }
                }
                $defendingMissileCount = $dmcnt_copy;

                if ($def_report != '') {
                    $def_report = "[b]Verteidigungsbericht:[/b]\n\n" . $def_report;
                    if ($defendingMissileCount > 0) {
                        $def_report .= "\n[b]Verbleibende Raketen:[/b]\n\n";
                        foreach ($defendingMissiles as $tc => $tm) {
                            $def_report .= $tm['n'] . "\n";
                        }
                    } else {
                        $def_report .= "\nAlle Defensivraketen wurden verbraucht!\n";
                    }
                }

                // Check if missiles are left
                if ($attackingMissilesCount > 0) {
                    $msg_a = "Eure Raketen haben den Planeten [b]" . $flight->targetPlanetName . "[/b] angegriffen! ";
                    $msg_d = "Euer Planet [b]" . $flight->targetPlanetName . "[/b] wurde von einem Raketenangriff getroffen!\n";
                    if ($dmcnt_start > 0) {
                        $msg_d .= "Eure Abfangraketen schossen zwar einige angreifende Raketen ab, jedoch kamen die restlichen Raketen trotzdem durch.\n ";
                        $msg_d .= "\n" . $def_report . "\n";
                    }

                    // Bomb the defense
                    $defenses = $this->defenseDataRepository->getAllDefenses();
                    $defenseList = $this->defenseRepository->findForUser($targetUserId, $flight->targetPlanetId);
                    if (count($defenseList) > 0) {
                        // Def values
                        $defendingStructure = 0;
                        $defendingShield = 0;
                        $defenseItemsById = [];
                        $defenseItemCounts = [];
                        $msg_d .= "Anlagen vor dem Angriff:\n\n";
                        foreach ($defenseList as $item) {
                            $defense = $defenses[$item->defenseId];
                            $defendingStructure += $defense->structure * $item->count;
                            $defendingShield += $defense->shield * $item->count * $this->config->getFloat('missile_battle_shield_factor');
                            $defenseItemsById[$item->id] = $item;
                            $defenseItemCounts[$item->id] = $item->count;
                            $msg_d .= "" . $item->count . " " . $defense->name . "\n";
                        }
                        shuffle($defenseItemCounts);

                        // Missile damage
                        $attackingDamage = 0;
                        foreach ($attackingMissiles as $attackingMissile) {
                            $attackingDamage += $attackingMissile->damage;
                        }

                        $msg_d .= "\nDie Raketen verursachen $attackingDamage Schaden.\n";

                        $remainingShiled = $defendingShield - $attackingDamage;
                        if ($remainingShiled < 0) {
                            $msg_d .= "Die Schilde halten $defendingShield Schaden auf.\n";

                            $remainingStructure = $defendingStructure + $remainingShiled;
                            if ($remainingStructure > 0) {
                                $stillAvailableStructure = $defendingStructure - $remainingStructure;
                                foreach ($defenseItemCounts as $itemId => $count) {
                                    $defense = $defenses[$defenseItemsById[$itemId]->defenseId];
                                    $defenseStructure = $defense->structure * $count;
                                    if ($defenseStructure - $stillAvailableStructure > 0) {
                                        $defenseItemCounts[$itemId] = (int) ceil($count * ($defenseStructure - $stillAvailableStructure) / $defenseStructure);

                                        break;
                                    }

                                    $defenseItemCounts[$itemId] = 0;
                                    $stillAvailableStructure -= $defenseStructure;
                                }

                                $msg_d .= "\nAnlagen nach dem Angriff:\n\n";
                                foreach ($defenseItemCounts as $itemId => $count) {
                                    $msg_d .= $count . " " . $defenses[$defenseItemsById[$itemId]->defenseId]->name . "\n";
                                    $this->defenseRepository->setDefenseCount($itemId, $count);
                                }
                            } else {
                                $msg_d .= 'Sämtliche Verteidigungsanlagen wurden zerstört!' . "\n";
                                foreach (array_keys($defenseItemCounts) as $itemId) {
                                    $this->defenseRepository->setDefenseCount($itemId, 0);
                                }
                            }
                        } else {
                            $msg_d .= 'Es wurden aber keine Schäden festgestellt da eure Schilde allen Schaden abgefangen haben.' . "\n";
                        }
                    } else {
                        $msg_d .= 'Es wurden aber keine Schäden festgestellt da Ihr keine Verteidigungsanlagen habt.' . "\n";
                    }

                    // EMP
                    $time = time();
                    foreach ($attackingMissiles as $attackingMissile) {
                        if ($attackingMissile->deactivate > 0) {
                            $toBeDeactivated = $this->buildingRepository->getDeactivatableBuilding($flight->targetPlanetId);
                            if ($toBeDeactivated !== null) {
                                $msg_a .= "Das Gebäude " . $toBeDeactivated['building_name'] . " wurde für " . StringUtils::formatTimespan($attackingMissile->deactivate) . " deaktiviert!\n";
                                $msg_d .= "Euer Gebäude " . $toBeDeactivated['building_name'] . " wurde für " . StringUtils::formatTimespan($attackingMissile->deactivate) . " deaktiviert!\n";
                                $this->buildingRepository->deactivateBuilding((int) $toBeDeactivated['buildlist_id'], $time + $attackingMissile->deactivate);
                            }
                        }
                    }
                } else {
                    $msg_a = "Der Kontakt zu den Raketen die den Planeten [b]" . $flight->targetPlanetName . "[/b] angreifen sollten ist verlorengegangen!";
                    $msg_d = "Eure Defensivraketen auf [b]" . $flight->targetPlanetName . "[/b] haben erfolgreich einen feindlichen Raketenangriff abgewehrt!";
                    $msg_d .= "\n\n" . $def_report;
                }

                // Set remaining defense missiles
                foreach ($defendingMissilesCounts as $itemId => $count) {
                    $this->missileRepository->setMissileCount($itemId, $count);
                }

                $this->messageRepository->createSystemMessage($flight->entityFromId, MessageCategoryId::SHIP_WAR, 'Ergebnis des Raketenangriffs', $msg_a);
                $this->messageRepository->createSystemMessage($targetUserId, MessageCategoryId::SHIP_WAR, 'Raketenangriff', $msg_d);
            }
        }
    }
}
