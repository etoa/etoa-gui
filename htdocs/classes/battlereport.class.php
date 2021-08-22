<?php

use EtoA\Building\BuildingDataRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;

/**
 * Description of battlereport
 *
 * @author glaubinix
 */
class BattleReport extends Report
{
    static $subTypes = array(
        'antrax' => 'Antraxangriff',
        'antraxfailed' => 'Antraxangriff erfolglos',
        'bombard' => 'Gebäude bombardiert',
        'bombardfailed' => 'Bombardierung erfolglos',
        'emp' => 'Deaktivierung',
        'empfailed' => 'Deaktivierung erfolglos',
        'gasattack' => 'Giftgasangriff',
        'gasattackfailed' => 'Giftgasangriff erfolglos',
        'invasion' => 'Planet erfolgreich invasiert',
        'invasionfailed' => 'Invasionsversuch gescheitert',
        'invaded' => 'Kolonie wurde invasiert',
        'invadedfailed' => 'Invasionsversuch abgewehrt',
        'spyattack' => 'Spionageangriff',
        'spyattackfailed' => 'Spionageangriff erfolglos',
        'battle' => 'Kampfbericht',
        'battlefailed' => 'Kampfbericht (Abgebrochen)',
        'battleban' => 'Kampfbericht (Abgebrochen)',
        'alliancefailed' => 'Allianzteilflotte abgebrochen'
    );

    protected $subType = 'other';
    protected $user, $entityUser;
    protected $ships, $entityShips, $entityDef;
    protected $weaponTech, $shieldTech, $structureTech;
    protected $weapon, $shield, $structure, $heal, $count, $exp;
    protected $entityWeaponTech, $entityShieldTech, $entityStructureTech;
    protected $entityWeapon, $entityShield, $entityStructure, $entityHeal, $entityCount, $entityExp;
    protected $shipsEnd, $entityShipsEnd, $entityDefEnd;
    protected $restore, $restoreCivilShips, $result;
    protected $res;
    protected $wf;
    protected $fleetId;

    private ConfigurationService $config;

    public function __construct($args)
    {
        // TODO
        global $app;

        $this->config = $app[ConfigurationService::class];

        global $resNames;
        parent::__construct($args);
        if ($this->valid) {
            $res = dbquery("SELECT * FROM reports_battle WHERE id=" . $this->id . ";");
            if (mysql_num_rows($res) > 0) {
                $arr = mysql_fetch_assoc($res);
                $this->subType = $arr['subtype'];
                $this->user = $arr['user'];
                $this->entityUser = $arr['entity_user'];
                $this->ships = $arr['ships'];
                $this->entityShips = $arr['entity_ships'];
                $this->entityDef = $arr['entity_def'];
                $this->weaponTech = $arr['weapon_tech'];
                $this->shieldTech = $arr['shield_tech'];
                $this->structureTech = $arr['structure_tech'];
                $this->weapon = array(0, $arr['weapon_1'], $arr['weapon_2'], $arr['weapon_3'], $arr['weapon_4'], $arr['weapon_5']);
                $this->shield = $arr['shield'];
                $this->structure = $arr['structure'];
                $this->heal = array(0, $arr['heal_1'], $arr['heal_2'], $arr['heal_3'], $arr['heal_4'], $arr['heal_5']);
                $this->count = array(0, $arr['count_1'], $arr['count_2'], $arr['count_3'], $arr['count_4'], $arr['count_5']);
                $this->exp = $arr['exp'];
                $this->entityWeaponTech = $arr['entity_weapon_tech'];
                $this->entityShieldTech = $arr['entity_shield_tech'];
                $this->entityStructureTech = $arr['entity_structure_tech'];
                $this->entityWeapon = array(0, $arr['entity_weapon_1'], $arr['entity_weapon_2'], $arr['entity_weapon_3'], $arr['entity_weapon_4'], $arr['entity_weapon_5']);
                $this->entityShield = $arr['entity_shield'];
                $this->entityStructure = $arr['entity_structure'];
                $this->entityHeal = array(0, $arr['entity_heal_1'], $arr['entity_heal_2'], $arr['entity_heal_3'], $arr['entity_heal_4'], $arr['entity_heal_5']);
                $this->entityCount = array(0, $arr['entity_count_1'], $arr['entity_count_2'], $arr['entity_count_3'], $arr['entity_count_4'], $arr['entity_count_5']);
                $this->entityExp = $arr['entity_exp'];
                $this->shipsEnd = $arr['ships_end'];
                $this->entityShipsEnd = $arr['entity_ships_end'];
                $this->entityDefEnd = $arr['entity_def_end'];
                $this->restore = $arr['restore'];
                $this->restoreCivilShips = $arr['restore_civil_ships'];
                $this->result = $arr['result'];
                $this->res = array();
                foreach ($resNames as $rk => $rn)
                    $this->res[$rk] = $arr['res_' . $rk];
                $this->res[5] = $arr['res_5'];
                $this->wf = array($arr['wf_0'], $arr['wf_1'], $arr['wf_2']);
                $this->fleetId = $arr['fleet_id'];
            } else {
                $this->valid = false;
                return;
            }
        }
    }

    static function add($data)
    {
        return false;
    }

    function createSubject()
    {
        $ent1 = Entity::createFactoryById($this->entity1Id);
        switch ($this->subType) {
            case 'battle':
                $users = array_map(function (string $userId): int {
                    return (int) $userId;
                }, explode(',', $this->user));
                $subject = "Kampfbericht (";
                switch ($this->result) {
                    case '1':
                        if (array_search($this->userId, $users, true))
                            $subject .= 'Gewonnen';
                        else
                            $subject .= 'Verloren';
                        break;
                    case '2':
                        if (array_search($this->userId, $users, true))
                            $subject .= 'Verloren';
                        else
                            $subject .= 'Gewonnen';
                        break;
                    default:
                        $subject .= 'Unentschieden';
                }
                $subject .= ') ' . $ent1;
                return $subject;
            default:
                return self::$subTypes[$this->subType];
        }
    }

    function __toString()
    {
        global $resNames, $app;

        ob_start();
        $ent1 = Entity::createFactoryById($this->entity1Id);
        $ent2 = Entity::createFactoryById($this->entity2Id);
        $user = new User($this->opponent1Id);

        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $app[ShipDataRepository::class];
        $shipNames = $shipRepository->getShipNames(true);

        switch ($this->subType) {
            case 'antrax':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat einen Antraxangriff auf den Planeten ' . $ent1->detailLink() . ' verübt. Es starben dabei ' . StringUtils::formatNumber($this->res[5]) . ' Bewohner und ' . StringUtils::formatNumber($this->res[4]) . ' t Nahrung wurden verbrannt.';
                break;
            case 'antraxfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat erfolglos einen Antraxangriff auf den Planeten ' . $ent1->detailLink() . ' verübt.';
                break;
            case 'bombard':
                $data = explode(':', $this->content);
                /** @var BuildingDataRepository $buildingRepository */
                $buildingRepository = $app[BuildingDataRepository::class];
                $buildingNames = $buildingRepository->getBuildingNames(true);
                $building = $buildingNames[(int) $data[0]];
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat das Gebäude ' . $building . ' des Planeten ' . $ent1->detailLink() . ' von Stufe ' . $data[2] . ' auf Stufe ' . $data[1] . ' zur&uuml;ck gesetzt.';
                break;
            case 'bombardfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat erfolglos versucht ein Gebäude des Planeten ' . $ent1->detailLink() . ' zu bombardieren.';
                break;
            case 'emp':
                $data = explode(':', $this->content);
                /** @var BuildingDataRepository $buildingRepository */
                $buildingRepository = $app[BuildingDataRepository::class];
                $buildingNames = $buildingRepository->getBuildingNames(true);
                $building = $buildingNames[(int) $data[0]];
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat das Gebäude ' . $building . ' des Planeten ' . $ent1->detailLink() . ' für ' . $data[1] . ' h deaktiviert.';
                break;
            case 'empfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat erfolglos versucht ein Gebäude des Planeten ' . $ent1->detailLink() . ' zu deaktivieren.<br />';
                if ($this->content == 1)
                    echo 'Hinweis: Der Spieler hat keine Gebäudeeinrichtungen, welche deaktiviert werden können!';
                break;
            case 'gasattack':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat einen Giftgasangriff auf den Planeten ' . $ent1->detailLink() . ' verübt. Es starben dabei ' . StringUtils::formatNumber($this->res[5]) . ' Bewohner.';
                break;
            case 'gasattackfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat erfolglos einen Giftgasangriff auf den Planeten ' . $ent1->detailLink() . ' verübt.';
                break;
            case 'invasion':
                echo '<strong>Planet:</strong> ' . $ent1->detailLink() . '<br />';
                echo '<strong>Besitzer:</strong> ' . $user . '<br /><br />';
                echo 'Dieser Planet wurde von einer Flotte, welche vom Planeten ' . $ent2->detailLink() . ' stammt, übernommen!<br />Ein Invasionsschiff wurde bei der Übernahme aufgebraucht!<br /><br />';
                echo '<strong>SCHIFFE</strong><br />';
                if ($this->ships == '' || $this->ships == '0')
                    echo '<i>Nichts vorhanden!</i><br />';
                else {
                    echo '<table>';
                    $shipArr = explode(',', $this->ships);
                    foreach ($shipArr as $ship) {
                        if ($ship != '') {
                            $data = explode(':', $ship);
                            echo '<tr>
                                    <td>' . $shipNames[(int) $data[0]] . ' </td>
                                    <td style="text-align:right;"> ' . StringUtils::formatNumber((int) $data[1]) . '</td>
                                </tr>';
                        }
                    }
                    echo '</table>';
                }

                echo '<br /><strong>WAREN</strong><br />';
                echo '<table>';
                foreach ($resNames as $k => $v) {
                    echo '<tr>
                        <td>' . $v . ' </td>
                        <td style="text-align:right;"> ' . StringUtils::formatNumber($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . StringUtils::formatNumber($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                break;
            case 'invasionfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat versucht den Planeten ' . $ent1->detailLink() . ' zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!<br />';
                if ($this->content == 1)
                    echo 'Hinweis: Du hast bereits die maximale Anzahl Planeten erreicht!<br />';
                elseif ($this->content == 2)
                    echo 'Hinweis: Dies ist ein Hauptplanet!<br />';
                break;
            case 'invaded':
                echo '<strong>Planet:</strong> ' . $ent1->detailLink() . '<br />';
                echo '<strong>Besitzer:</strong> ' . $user . '<br /><br />';
                echo 'Dieser Planet wurde von einer Flotte, welche vom Planeten ' . $ent2->detailLink() . ' stammt, übernommen!<br />';
                break;
            case 'invadedfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat versucht den Planeten ' . $ent1->detailLink() . ' zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!';
                break;
            case 'spyattack':
                /** @var TechnologyDataRepository $technologyRepository */
                $technologyRepository = $app[TechnologyDataRepository::class];
                $techNames = $technologyRepository->getTechnologyNames(true);
                $data = explode(':', $this->content);
                $tech = $techNames[(int) $data[0]];
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat erfolgreich einen Spionageangriff durchgeführt und erfuhr so die Geheimnisse der Forschung ' . $tech . ' bis zum Level ' . $data[1] . '.';
                break;
            case 'spyattackfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat erfolglos einen Spionageangriff auf den Planeten ' . $ent1->detailLink() . ' verübt.';
                break;
            case 'battle':
                echo '<strong>KAMPFBERICHT</strong><br />
                    vom Planeten ' . $ent1->detailLink() . '<br />
                    <strong>Zeit:</strong> ' . StringUtils::formatDate($this->timestamp) . '<br /><br />
                    <table class="battleTable" width="100%">
                        <tr>
                            <td>
                                <strong>Angreifer:</strong> ';
                if ($this->user != '') {
                    $userArr = explode(',', $this->user);
                    $cnt = 0;
                    foreach ($userArr as $uId) {
                        if ($uId != '') {
                            $user = new User($uId);
                            if ($cnt > 0) echo ', ';
                            echo $user;
                            ++$cnt;
                        }
                    }
                }
                echo        '</td>
                            <td>
                                <strong>Verteidiger:</strong> ';
                if ($this->entityUser != '') {
                    $userArr = explode(',', $this->entityUser);
                    $cnt = 0;
                    foreach ($userArr as $uId) {
                        if ($uId != '') {
                            $user = new User($uId);
                            if ($cnt > 0) echo ', ';
                            echo $user;
                            ++$cnt;
                        }
                    }
                }
                echo         '<br /><br /></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>ANGREIFENDE FLOTTE</strong><br />';
                if (!($this->ships == '' || $this->ships == '0')) {
                    echo '<table>';
                    $shipArr = explode(',', $this->ships);
                    foreach ($shipArr as $ship) {
                        if ($ship != '') {
                            $data = explode(':', $ship);
                            echo '<tr>
                                                    <td>' . $shipNames[(int) $data[0]] . ' </td>
                                                    <td style="text-align:right;"> ' . StringUtils::formatNumber((int) $data[1]) . '</td>
                                                </tr>';
                        }
                    }
                    echo '</table>';
                } else
                    echo '<i>Nichts vorhanden!</i>';
                echo        '</td>
                            <td>
                                <strong>VERTEIDIGENDE FLOTTE</strong><br />';
                if (!($this->entityShips == '' || $this->entityShips == '0')) {
                    echo '<table>';
                    $shipArr = explode(',', $this->entityShips);
                    foreach ($shipArr as $ship) {
                        if ($ship != '') {
                            $data = explode(':', $ship);
                            echo '<tr>
                                                    <td>' . $shipNames[(int) $data[0]] . ' </td>
                                                    <td style="text-align:right;"> ' . StringUtils::formatNumber((int) $data[1]) . '</td>
                                                </tr>';
                        }
                    }
                    echo '</table>';
                } else
                    echo '<i>Nichts vorhanden!</i>';
                echo        '<br /></td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <strong>PLANETARE VERTEIDIGUNG</strong><br />';
                if (!($this->entityDef == '' || $this->entityDef == '0')) {
                    echo '<table>';
                    $defArr = explode(',', $this->entityDef);
                    /** @var DefenseDataRepository $defenseRepository */
                    $defenseRepository = $app[DefenseDataRepository::class];
                    $defenseNames = $defenseRepository->getDefenseNames(true);
                    foreach ($defArr as $defense) {
                        if ($defense != '') {
                            $data = explode(':', $defense);
                            echo '<tr>
                                                    <td>' . $defenseNames[(int) $data[0]] . ' </td>
                                                    <td style="text-align:right;"> ' . StringUtils::formatNumber((int) $data[1]) . '</td>
                                                </tr>';
                        }
                    }
                    echo '</table>';
                } else
                    echo '<i>Nichts vorhanden!</i>';
                echo        '<br /></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>DATEN DES ANGREIFERS</strong><br />
                                <table>
                                    <tr>
                                        <td>Schild (' . $this->shieldTech . '%):</td><td style="text-align:right;"> ' . StringUtils::formatNumber($this->shield) . '</td>
                                    </tr><tr>
                                        <td>Struktur (' . $this->structureTech . '%):</td><td style="text-align:right;"> ' . StringUtils::formatNumber($this->structure) . '</td>
                                    </tr><tr>
                                        <td>Waffen (' . $this->weaponTech . '%):</td><td style="text-align:right;"> ' . StringUtils::formatNumber($this->weapon[1]) . '</td>
                                    </tr><tr>
                                        <td>Einheiten:</td><td style="text-align:right;"> ' . StringUtils::formatNumber($this->count[1]) . '</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <strong>DATEN DES VERTEIDIGERS</strong><br />
                                <table>
                                    <tr>
                                        <td>Schild (' . $this->entityShieldTech . '%):</td><td style="text-align:right;"> ' . StringUtils::formatNumber($this->entityShield) . '</td>
                                    </tr><tr>
                                        <td>Struktur (' . $this->entityStructureTech . '%):</td><td style="text-align:right;"> ' . StringUtils::formatNumber($this->entityStructure) . '</td>
                                    </tr><tr>
                                        <td>Waffen (' . $this->entityWeaponTech . '%):</td><td style="text-align:right;"> ' . StringUtils::formatNumber($this->entityWeapon[1]) . '</td>
                                    </tr><tr>
                                        <td>Einheiten:</td><td style="text-align:right;"> ' . StringUtils::formatNumber($this->entityCount[1]) . '</td>
                                    </tr>
                                </table>
                            <br /></td>
                        </tr>
                        <tr>
                            <td colspan="2">';
                $rnd = 1;
                $shieldStructure = $initShieldStructure = (int) $this->shield + (int) $this->structure;
                $entityShieldStructure = $entityInitShieldStructure = (int) $this->entityShield + (int) $this->entityStructure;
                for ($rnd = 1; $rnd <= 5; $rnd++) {
                    $shieldStructure = max(0, $shieldStructure - $this->entityWeapon[$rnd]);
                    $entityShieldStructure = max(0, $entityShieldStructure - $this->weapon[$rnd]);

                    echo '<br />' . StringUtils::formatNumber($this->count[$rnd]) . ' Einheiten des Angreifers schiessen mit einer Stärke von ' . StringUtils::formatNumber($this->weapon[$rnd]) . ' auf den Verteidiger. Der Verteidiger hat danach noch ' . StringUtils::formatNumber($entityShieldStructure) . ' Struktur- und Schildpunkte.<br /><br />';
                    echo StringUtils::formatNumber($this->entityCount[$rnd]) . ' Einheiten des Verteidigers schiessen mit einer Stärke von ' . StringUtils::formatNumber($this->entityWeapon[$rnd]) . ' auf den Angreifer. Der Angreifer hat danach noch ' . StringUtils::formatNumber($shieldStructure) . ' Struktur- und Schildpunkte.<br /><br />';

                    if ($this->heal[$rnd] > 0 && $shieldStructure < $initShieldStructure) {
                        $shieldStructure = min($initShieldStructure, ($shieldStructure + $this->heal[$rnd]));
                        echo 'Die Einheiten des Angreifers heilen ' . StringUtils::formatNumber($this->heal[$rnd]) . ' Struktur- und Schildpunkte. Der Angreifer hat danach wieder ' . StringUtils::formatNumber($shieldStructure) . ' Struktur- und Schildpunkte<br /><br />';
                    }
                    if ($this->entityHeal[$rnd] > 0 && $entityShieldStructure < $entityInitShieldStructure) {
                        $entityShieldStructure = min($entityInitShieldStructure, ($entityShieldStructure + $this->entityHeal[$rnd]));
                        echo 'Die Einheiten des Verteidiger heilen ' . StringUtils::formatNumber($this->entityHeal[$rnd]) . ' Struktur- und Schildpunkte. Der Verteidiger hat danach wieder ' . StringUtils::formatNumber($entityShieldStructure) . ' Struktur- und Schildpunkte<br /><br />';
                    }

                    if ($rnd == 5 || $this->count[$rnd + 1] == 0 || $this->entityCount[$rnd + 1] == 0) break;
                }

                echo 'Der Kampf dauerte ' . min(5, $rnd) . ' Runden!<br /><br />';
                switch ($this->result) {
                    case '1':
                        echo 'Der Angreifer hat den Kampf gewonnen!';
                        break;
                    case '2':
                        echo 'Der Verteidiger hat den Kampf gewonnen!';
                        break;
                    case '3':
                        echo 'Der Kampf endete unentschieden, da sowohl die Einheiten des Angreifers als auch die Einheiten des Verteidigers alle zerst&ouml;rt wurden!';
                        break;
                    default:
                        echo 'Der Kampf endete unentschieden und die Flotten zogen sich zur&uuml;ck!';
                }
                echo '<br /><br /></td>
                        </tr>
                        <tr>
                            <td>';
                echo '<strong>BEUTE</strong><br />';
                if ($this->result == 1) {
                    echo '<table>';
                    foreach ($resNames as $k => $v) {
                        echo '<tr>
                                            <td>' . $v . ' </td>
                                            <td style="text-align:right;"> ' . StringUtils::formatNumber($this->res[$k]) . '</td>
                                            </tr>';
                    }
                    echo '<tr>
                                            <td>Bewohner </td>
                                            <td style="text-align:right;"> ' . StringUtils::formatNumber($this->res[5]) . '</td>
                                        </tr>';
                    echo '</table>';
                }
                echo '<br /><br /></td>
                            <td>
                                <strong>TR&Uuml;MMERFELD</strong><br />';
                echo '<table>';
                foreach ($this->wf as $k => $wf) {
                    echo '<tr>
                                            <td>' . $resNames[$k] . ' </td>
                                            <td style="text-align:right;"> ' . StringUtils::formatNumber($wf) . '</td>
                                        </tr>';
                }
                echo '</table><br/>
                        <br /><br /></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                Zustand nach dem Kampf:<br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>ANGREIFENDE FLOTTE</strong><br />';
                if (!($this->shipsEnd == '' || $this->shipsEnd == '0')) {
                    echo '<table>';
                    $shipArr = explode(',', $this->shipsEnd);
                    foreach ($shipArr as $ship) {
                        if ($ship != '') {
                            $data = explode(':', $ship);
                            echo '<tr>
                                                    <td>' . $shipNames[(int) $data[0]] . ' </td>
                                                    <td style="text-align:right;"> ' . StringUtils::formatNumber((int) $data[1]) . '</td>
                                                </tr>';
                        }
                    }
                    echo '</table>';
                } else
                    echo '<i>Nichts vorhanden!</i>';
                echo        '</td>
                            <td>
                                <strong>VERTEIDIGENDE FLOTTE</strong><br />';
                if (!($this->entityShipsEnd == '' || $this->entityShipsEnd == '0')) {
                    echo '<table>';
                    $shipArr = explode(',', $this->entityShipsEnd);
                    foreach ($shipArr as $ship) {
                        if ($ship != '') {
                            $data = explode(':', $ship);
                            echo '<tr>
                                                    <td>' . $shipNames[(int) $data[0]] . ' </td>';
                            if ($data[2] > 0) {
                                echo '<td style="text-align:right;"> ' . StringUtils::formatNumber((int) $data[1]) . '</td>';
                                echo '<td>(+' . StringUtils::formatNumber((int) $data[2]) . ')</td>';
                            } else {
                                echo '<td style="text-align:right;"> ' . StringUtils::formatNumber((int) $data[1]) . '</td>';
                            }
                            echo    '</tr>';
                        }
                    }
                    echo '<tr><td colspan="3">' . $this->restoreCivilShips . '% der zivilen Schiffe werden wiederhergestellt!</td></tr>';
                    echo '</table>';
                } else
                    echo '<i>Nichts vorhanden!</i>';
                echo        '<br /></td>
                        </tr>';
                if ($this->exp >= 0 || $this->entityExp >= 0) {
                    echo '<tr>
                            <td>';
                    if ($this->exp >= 0)
                        echo 'Gewonnene EXP: ' . StringUtils::formatNumber($this->exp);
                    echo '	</td><td>';
                    if ($this->entityExp >= 0)
                        echo 'Gewonnene EXP: ' . StringUtils::formatNumber($this->entityExp);
                    echo '<br /><br /></td>
                        </tr>';
                }
                echo '	<tr>
                            <td>
                            </td>
                            <td>
                                <strong>PLANETARE VERTEIDIGUNG:</strong><br />';
                if (!($this->entityDefEnd == '' || $this->entityDefEnd == '0')) {
                    echo '<table>';
                    $defArr = explode(',', $this->entityDefEnd);
                    /** @var DefenseDataRepository $defenseRepository */
                    $defenseRepository = $app[DefenseDataRepository::class];
                    $defenseNames = $defenseRepository->getDefenseNames(true);
                    foreach ($defArr as $defense) {
                        if ($defense != '') {
                            $data = explode(':', $defense);
                            echo '<tr>
                                                    <td>' . $defenseNames[(int) $data[0]] . ' </td>';
                            if ($data[2] > 0) {
                                echo '<td style="text-align:right;"> ' . StringUtils::formatNumber((int) $data[1]) . '</td>';
                                echo '<td>(+' . StringUtils::formatNumber((int) $data[2]) . ')</td>';
                            } else {
                                echo '<td style="text-align:right;"> ' . StringUtils::formatNumber((int) $data[1]) . '</td>';
                            }
                            echo    '</tr>';
                        }
                    }
                    echo '<tr><td colspan="3">' . $this->restore . '% der Verteidigungsanlagen werden repariert!</td></tr>';
                    echo '</table>';
                } else
                    echo '<i>Nichts vorhanden!</i>';
                echo        '<br /></td>
                        </tr>
                    </table>';
                break;
            case 'battlefailed':
                echo '<strong>KAMPFBERICHT</strong><br />
                    vom Planeten ' . $ent1->detailLink() . '<br />
                    <strong>Zeit:</strong> ' . StringUtils::formatDate($this->timestamp) . '<br /><br />
                    <table class="battleTable" width="100%">
                        <tr>
                            <td>
                            <strong>Angreifer:</strong> ';
                if ($this->user != '') {
                    $userArr = explode(',', $this->user);
                    $cnt = 0;
                    foreach ($userArr as $uId) {
                        if ($uId != '') {
                            $user = new User($uId);
                            if ($cnt > 0) echo ', ';
                            echo $user;
                            ++$cnt;
                        }
                    }
                }
                echo        '</td>
                        <td>
                            <strong>Verteidiger:</strong> ';
                if ($this->entityUser != '') {
                    $userArr = explode(',', $this->entityUser);
                    $cnt = 0;
                    foreach ($userArr as $uId) {
                        if ($uId != '') {
                            $user = new User($uId);
                            if ($cnt > 0) echo ', ';
                            echo $user;
                            ++$cnt;
                        }
                    }
                }
                echo         '<br /><br /></td>
                    </tr>
                </table>';
                echo 'Der Kampf wurde abgebrochen da Angreifer und Verteidiger demselben Imperium angeh&ouml;ren oder der Verteidiger nicht mehr existiert!';
                break;
            case 'battleban':
                echo '<strong>KAMPFBERICHT</strong><br />
                    vom Planeten ' . $ent1->detailLink() . '<br />
                    <strong>Zeit:</strong> ' . StringUtils::formatDate($this->timestamp) . '<br /><br />
                    <table class="battleTable" width="100%">
                        <tr>
                            <td>
                            <strong>Angreifer:</strong> ';
                if ($this->user != '') {
                    $userArr = explode(',', $this->user);
                    $cnt = 0;
                    foreach ($userArr as $uId) {
                        if ($uId != '') {
                            $user = new User($uId);
                            if ($cnt > 0) echo ', ';
                            echo $user;
                            ++$cnt;
                        }
                    }
                }
                echo        '</td>
                        <td>
                            <strong>Verteidiger:</strong> ';
                if ($this->entityUser != '') {
                    $userArr = explode(',', $this->entityUser);
                    $cnt = 0;
                    foreach ($userArr as $uId) {
                        if ($uId != '') {
                            $user = new User($uId);
                            if ($cnt > 0) echo ', ';
                            echo $user;
                            ++$cnt;
                        }
                    }
                }
                echo         '<br /><br /></td>
                    </tr>
                </table>';
                echo 'Der Kampf wurde abgebrochen, da momentan gerade eine Kampfsperre aktiv ist!';
                break;
            case 'alliancefailed':
                echo 'Deine Flotte vom Planeten ' . $ent2->detailLink() .
                    ' wollte sich einem Allianzangriff auf den Planeten ' . $ent1->detailLink() .
                    ' anschliessen. Die Flotte &uuml;berstieg aber die Limitierung durch das' .
                    ' intergalaktische Kriegsrecht auf ' . $this->config->param1Int('alliance_fleets_max_players') .
                    ' Angreifer, weswegen die Piloten dem Kampf nur zuschauen konnten.';
                break;
            case 'alliancenowar':
                echo 'Deine Flotte vom Planeten ' . $ent2->detailLink() .
                    ' wollte sich einem Allianzangriff auf den Planeten ' . $ent1->detailLink() .
                    ' anschliessen. Da sich die Allianzen von Angreifer und Verteidiger aber' .
                    ' nicht im Krieg befinden, war die Unterst&uuml;tzung gem&auml;ss' .
                    ' intergalaktischem Kriegsrecht nicht möglich,' .
                    ' weswegen die Piloten dem Kampf nur zuschauen konnten.';
                break;
            case 'supportnowar': //TODO: Implement in Backend
                echo 'Deine Flotte vom Planeten ' . $ent2->detailLink() .
                    ' ist zum Support auf den Planeten ' . $ent1->detailLink() .
                    ' stationiert, wo gerade ein Kampf stattgefunden hat.' .
                    ' Da sich die Allianzen von Angreifer und Verteidiger aber' .
                    ' nicht im Krieg befinden, war die Unterst&uuml;tzung gem&auml;ss' .
                    ' intergalaktischem Kriegsrecht nicht möglich,' .
                    ' weswegen deine Piloten dem Kampf nur zuschauen konnten.';
                break;
            case 'absdisabled':
                echo 'Deine Flotte vom Planeten ' . $ent2->detailLink() .
                    ' wollte einen Allianzangriff auf den Planeten ' . $ent1->detailLink() .
                    ' durchführen. Da das Allianzkampfsystem momentan nicht aktiv ist,' .
                    ' mussten deine Piloten leider umkehren.';
            default:
                etoa_dump($this);
        }

        return ob_get_clean();
    }
}
