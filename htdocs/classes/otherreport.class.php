<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ship\ShipDataRepository;

/**
 * Description of otherreport
 *
 * @author glaubinix
 */
class OtherReport extends Report
{
    static $subTypes = array(
        'return' => 'Flotte angekommen',
        'collectmetal' => 'Asteroiden gesammelt',
        'collectmetalfailed' => 'Asteroidensammeln gescheitert',
        'delivery' => 'Flotte von der Allianzbasis',
        'colonize' => 'Planet kolonialisiert',
        'colonizefailed' => 'Landung nicht möglich',
        'createdebris' => 'Tr&uuml;mmerfeld erstellt',
        'collectfuel' => 'Gas gesaugt',
        'collectfuelfailed' => 'Gassaugen gescheitert',
        'market' => 'Flotte vom Handelsministerium',
        'collectcrystal' => 'Nebelfeld gesammelt',
        'collectcrystalfailed' => 'Nebelfeldensammeln gescheitert',
        'supportreturn' => 'Supportflotte R&uuml;ckflug',
        'support' => 'Supportflotte angekommen',
        'supportfailed' => 'Supportflug fehlgeschlagen"',
        'supportoverflow' => 'Support nicht m&ouml;glich',
        'transport' => 'Transport angekommen',
        'collectdebris' => 'Tr&uuml;mmer gesammelt',
        'collectdebrisfailed' => 'Tr&uuml;mmersammeln gescheitert',
        'fetch' => 'Warenabholung',
        'fetchfailed' => 'Warenabholung gescheitert',
        'actionmain' => 'Flotte umgelenkt',
        'actionshot' => 'Flotte abgeschossen',
        'actionfailed' => 'Aktion gescheitert',
    );

    protected $subType = 'other';
    protected $ships;
    protected $res;
    protected $fleetId;
    protected $status;
    protected $actionCode;

    public function __construct($args)
    {
        global $resNames;
        parent::__construct($args);
        if ($this->valid) {
            $res = dbquery("SELECT * FROM reports_other WHERE id=" . $this->id . ";");
            if (mysql_num_rows($res) > 0) {
                $arr = mysql_fetch_assoc($res);
                $this->subType = $arr['subtype'];
                $this->ships = $arr['ships'];
                $this->res = array();
                foreach ($resNames as $rk => $rn)
                    $this->res[$rk] = $arr['res_' . $rk];
                $this->res[5] = $arr['res_5'];
                $this->fleetId = $arr['fleet_id'];
                $this->status = $arr['status'];
                $this->actionCode = $arr['action'];
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
        return self::$subTypes[$this->subType];
    }

    function __toString()
    {
        // TODO
        global $resNames;

        global $app;

        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];
        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $app[ShipDataRepository::class];
        $shipNames = $shipRepository->getShipNames(true);

        ob_start();
        $ent1 = Entity::createFactoryById($this->entity1Id);
        $ent2 = Entity::createFactoryById($this->entity2Id);

        switch ($this->subType) {
            case 'return':
                $action = FleetAction::createFactory($this->actionCode);
                echo '<strong>FLOTTE GELANDET</strong><br /><br />
                        Eine eurer Flotten hat ihr Ziel erreicht!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Start: </strong>' . $ent2->detailLink() . '<br />';
                echo '<strong>Zeit: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . ' [' . FleetAction::$statusCode[$this->status] . ']<br /><br />';

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
                                    <td style="text-align:right;"> ' . nf($data[1]) . '</td>
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
                        <td style="text-align:right;"> ' . nf($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . nf($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                break;
            case 'collectmetal':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat das Ziel ' . $ent1->detailLink() . ' um ' . df($this->timestamp) . ' erreicht und Rohstoffe gesammelt.<br />';
                echo '<br /><strong>ROHSTOFFE:</strong><br />';
                echo '<table>';
                foreach ($resNames as $k => $v) {
                    echo '<tr>
                        <td>' . $v . ' </td>
                        <td style="text-align:right;"> ' . nf($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . nf($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                if ($this->ships != '') {
                    echo '<br />Aufgrund einer Kolision mit einem Asteroiden sind einige deiner Schiffe zerst&ouml;rt worden:<br />';
                    echo '<table>';
                    $shipArr = explode(',', $this->ships);

                    foreach ($shipArr as $ship) {
                        if ($ship != '') {
                            $data = explode(':', $ship);
                            echo '<tr>
                                    <td>' . $shipNames[(int) $data[0]] . ' </td>
                                    <td style="text-align:right;"> ' . nf($data[1]) . '</td>
                                </tr>';
                        }
                    }
                    echo '</table><br />';
                }
                break;
            case 'collectmetalfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' versuchte, Asteroiden zu sammeln, doch war kein Asteroidenfeld mehr vorhanden und so machte sich die Crew auf den Weg nach Hause.';
                break;
            case 'delivery':
                $action = FleetAction::createFactory($this->actionCode);
                echo 'Eine Flotte von der Allianzbasis hat folgendes Ziel erreicht!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Zeit: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . '<br /><br />';
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
                                    <td style="text-align:right;"> ' . nf($data[1]) . '</td>
                                </tr>';
                        }
                    }
                    echo '</table>';
                }
                break;
            case 'colonize':
                $action = FleetAction::createFactory($this->actionCode);
                echo 'Die Flotte hat folgendes Ziel erreicht!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Start: </strong>' . $ent2->detailLink() . '<br />';
                echo '<strong>Zeit: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . ' [' . FleetAction::$statusCode[$this->status] . ']<br /><br />Die Flotte hat eine neue Kolonie errichtet! Dabei wurde ein Besiedlungsschiff verbraucht.<br />';
                echo '<br /><strong>SCHIFFE</strong><br />';
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
                                    <td style="text-align:right;"> ' . nf($data[1]) . '</td>
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
                        <td style="text-align:right;"> ' . nf($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . nf($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                break;
            case 'colonizefailed':
                $action = FleetAction::createFactory($this->actionCode);
                echo 'Die Flotte hat folgendes Ziel erreicht!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Start: </strong>' . $ent2->detailLink() . '<br />';
                echo '<strong>Zeit: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . ' [' . FleetAction::$statusCode[$this->status] . ']<br /><br />Die Flotte kann den Planeten nicht kolonialisieren';
                if ($this->content == 1)
                    echo ',  da er bereits von einem anderen Volk kolonialisiert wurde';
                elseif ($this->content == 2)
                    echo ',  da die maximale Zahl an Planeten auf denen du regieren darfst, bereits erreicht worden ist';
                echo '!<br />';
                break;
            case 'createdebris':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat auf dem Planeten ' . $ent1->detailLink() . ' ein Trümmerfeld erstellt.<br />';
                break;
            case 'collectfuel':
                echo '<strong>GASSAUGER-RAPPORT</strong><br /><br />';
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat den Planeten ' . $ent1->detailLink() . ' erkundet und Gas gesaugt.<br />';
                echo '<br /><strong>ROHSTOFFE</strong><br />';
                echo '<table>';
                foreach ($resNames as $k => $v) {
                    echo '<tr>
                        <td>' . $v . ' </td>
                        <td style="text-align:right;"> ' . nf($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . nf($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                if ($this->ships != '') {
                    echo '<br />Aufgrund starker Wasserstoffexplosionen sind einige deiner Schiffe zerst&ouml;rt worden:<br />';
                    echo '<table>';
                    $shipArr = explode(',', $this->ships);
                    foreach ($shipArr as $ship) {
                        if ($ship != '') {
                            $data = explode(':', $ship);
                            echo '<tr>
                                    <td>' . $shipNames[(int) $data[0]] . ' </td>
                                    <td style="text-align:right;"> ' . nf($data[1]) . '</td>
                                </tr>';
                        }
                    }
                    echo '</table><br />';
                }
                break;
            case 'collectfuelfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hatte sich auf dem Weg zu einem Gasplaneten, wohl gr&uuml;ndlich verflogen und kehrte auf direktem Weg zur&uuml;ck nach Hause.';
                break;
            case 'market':
                $action = FleetAction::createFactory($this->actionCode);
                echo 'Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Start: </strong>Marktplatz<br />';
                echo '<strong>Zeit: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . ' [' . FleetAction::$statusCode[$this->status] . ']<br /><br />Die gekauften Waren sind angekommen.<br />';
                if (!($this->ships == '' || $this->ships == '0')) {
                    echo '<br /><strong>SCHIFFE</strong><br />';
                    echo '<table>';
                    $shipArr = explode(',', $this->ships);
                    foreach ($shipArr as $ship) {
                        if ($ship != '') {
                            $data = explode(':', $ship);
                            echo '<tr>
                                    <td>' . $shipNames[(int) $data[0]] . ' </td>
                                    <td style="text-align:right;"> ' . nf($data[1]) . '</td>
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
                        <td style="text-align:right;"> ' . nf($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . nf($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                break;
            case 'collectcrystal':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat das Ziel ' . $ent1->detailLink() . ' um ' . df($this->timestamp) . ' erreicht und Rohstoffe gesammelt.<br />';
                echo '<br /><strong>ROHSTOFFE</strong><br />';
                echo '<table>';
                foreach ($resNames as $k => $v) {
                    echo '<tr>
                        <td>' . $v . ' </td>
                        <td style="text-align:right;"> ' . nf($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . nf($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                if ($this->ships != '') {
                    echo '<br />Einige Schiffe deiner Flotte verirrten sich in einem Interstellarer Gasnebel und konnten nicht mehr gefunden werden:<br />';
                    echo '<table>';
                    $shipArr = explode(',', $this->ships);
                    foreach ($shipArr as $ship) {
                        if ($ship != '') {
                            $data = explode(':', $ship);
                            echo '<tr>
                                    <td>' . $shipNames[(int) $data[0]] . ' </td>
                                    <td style="text-align:right;"> ' . nf($data[1]) . '</td>
                                </tr>';
                        }
                    }
                    echo '</table><br />';
                }
                break;
            case 'collectcrystalfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' konnte kein Intergalaktisches Nebelfeld orten und so machte sich die Crew auf den Weg nach Hause.';
                break;
            case 'supportreturn':
                $action = FleetAction::createFactory($this->actionCode);
                echo '<strong>SUPPORT BEENDET</strong><br /><br />
                    Eine eurer Flotten hat hat ihr Ziel verlassen und macht sich nun auf den R&uuml;ckweg!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Start: </strong>' . $ent2->detailLink() . '<br />';
                echo '<strong>Zeit: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . ' [' . FleetAction::$statusCode[$this->status] . ']<br />';
                break;
            case 'support':
                $action = FleetAction::createFactory($this->actionCode);
                $user = new User($this->opponent1Id);
                echo '<strong>SUPPORTFLOTTE ANGEKOMMEN</strong><br /><br />
                    Eine Flotte hat ihr Ziel erreicht!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Start: </strong>' . $ent2->detailLink() . '<br />';
                echo '<strong>Ankunft: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . ' [' . FleetAction::$statusCode[$this->status] . ']<br />';
                echo '<strong>Ende des Auftrages: </strong>' . df($this->content) . '<br />';
                echo '<strong>Flottenbesitzer: </strong>' . $user . '<br />';
                break;
            case  'supportfailed':
                $action = FleetAction::createFactory($this->actionCode);
                echo '<strong>FLOTTE LANDEN FEHLGESCHLAGEN</strong><br /><br />
                    Eine eurer Flotten konnte nicht auf ihrem Ziel landen!';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Start: </strong>' . $ent2->detailLink() . '<br />';
                echo '<strong>Ankunft: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . ' [' . FleetAction::$statusCode[$this->status] . ']<br />';
                break;
            case 'supportoverflow':
                $action = FleetAction::createFactory($this->actionCode);
                echo '<strong>Kein Supportplatz auf dem Zielplaneten vorhanden</strong><br /><br />
                    Eine Supportflotte konnte keinen Platz mehr auf dem Zielplaneten finden.<br />
                    Der Planet wurde bereits von ' . $config->param1Int('alliance_fleets_max_players') .
                    ' Imperatoren verteidigt, so dass Eure Flotte wieder umkehren musste. <br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Start: </strong>' . $ent2->detailLink() . '<br />';
                echo '<strong>Ankunft: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . ' [' . FleetAction::$statusCode[$this->status] . ']<br />';
                break;
            case 'transport':
                echo '<strong>TRANSPORT GELANDET</strong><br /><br />
                        Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat ihr Ziel erreicht!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Zeit: </strong>' . df($this->timestamp) . '<br /><br />';
                echo '<br /><strong>WAREN</strong><br />';
                echo '<table>';
                foreach ($resNames as $k => $v) {
                    echo '<tr>
                        <td>' . $v . ' </td>
                        <td style="text-align:right;"> ' . nf($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . nf($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                break;
            case 'collectdebris':
                echo '<strong>TR&Uuml;MMERSAMMLER-RAPPORT</strong><br /><br />
                    Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat das Tr&uuml;mmerfeld bei ' . $ent1->detailLink() . ' um ' . df($this->timestamp) . ' erreicht und Trümmer gesammelt.<br /><br />';
                echo '<br /><strong>ROHSTOFFE</strong><br />';
                echo '<table>';
                foreach ($resNames as $k => $v) {
                    echo '<tr>
                        <td>' . $v . ' </td>
                        <td style="text-align:right;"> ' . nf($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . nf($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                break;
            case 'collectdebrisfailed':
                echo '<strong>TR&Uuml;MMERSAMMLER-RAPPORT</strong><br /><br />
                    Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat das Tr&uuml;mmerfeld bei ' . $ent1->detailLink() . ' um ' . df($this->timestamp) . ' erreicht.<br />
                    Es wurden aber leider keine brauchbaren Tr&uuml;mmerteile mehr gefunden so dass die Flotte unverrichteter Dinge zur&uuml;ckkehren musste.';
                break;
            case 'fetch':
                echo '<strong>WAREN ABGEHOLT</strong><br /><br />
                    Eine Flotte vom Planeten ' . $ent2->detailLink() . ' hat das Ziel erreicht!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Zeit: </strong>' . df($this->timestamp) . '<br />';
                echo '<br />Folgende Waren wurden abgeholt:<br />';
                echo '<table>';
                foreach ($resNames as $k => $v) {
                    echo '<tr>
                        <td>' . $v . ' </td>
                        <td style="text-align:right;"> ' . nf($this->res[$k]) . '</td>
                        </tr>';
                }
                echo '<tr>
                        <td>Bewohner </td>
                        <td style="text-align:right;"> ' . nf($this->res[5]) . '</td>
                    </tr>';
                echo '</table><br/>';
                break;
            case 'fetchfailed':
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' versuchte Waren abzuholen. Leider fand die Flotte keinen deiner Planeten mehr vor und so machte sich die Crew auf den Weg nach Hause!<br />';
                break;
            case 'actionmain':
                $action = FleetAction::createFactory($this->actionCode);
                echo '<strong>FLOTTE LANDEN GESCHEITERT</strong><br /><br />
                        Eine eurer Flotten hat versucht auf ihrem Ziel zu laden Der Versuch scheiterte jedoch und die Flotte macht sich auf den Weg zu eurem Hauptplaneten!<br /><br />';
                echo '<strong>Ziel: </strong>' . $ent1->detailLink() . '<br />';
                echo '<strong>Start: </strong>' . $ent2->detailLink() . '<br />';
                echo '<strong>Zeit: </strong>' . df($this->timestamp) . '<br />';
                echo '<strong>Auftrag: </strong>' . $action->name() . ' [' . FleetAction::$statusCode[$this->status] . ']<br /><br />';
                break;
            case 'actionshot':
                $action = FleetAction::createFactory($this->actionCode);
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' wurde beim Ziel ' . $ent1->detailLink() . ' beim durchführen der Aktion ' . $action->name() . ' abgeschossen.';
                break;
            case 'actionfailed':
                $action = FleetAction::createFactory($this->actionCode);
                echo 'Eine Flotte vom Planeten ' . $ent2->detailLink() . ' versuchte beim Ziel ' . $ent1->detailLink() . ' die Aktion ' . $action->name() . ' durchzuführen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!<br />';
                break;
            default:
                etoa_dump($this);
        }

        return ob_get_clean();
    }
}
