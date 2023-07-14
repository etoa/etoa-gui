<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\BuddyList\BuddyListRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Design\Design;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Legacy\UserSession;
use EtoA\Legacy\UtilityMethodProvider;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\ExternalUrl;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\Text\TextRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserPropertiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class LegacyIndexController extends AbstractController
{
    public function __construct(
        private readonly ConfigurationService     $config,
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly PlanetRepository         $planetRepository,
        private readonly MessageRepository        $messageRepository,
        private readonly ReportRepository         $reportRepository,
        private readonly FleetRepository          $fleetRepository,
        private readonly TextRepository           $textRepo,
        private readonly BuddyListRepository      $buddyListRepository,
        private readonly RuntimeDataStore         $runtimeDataStore,
        private readonly UserSession              $userSession,
        private readonly UtilityMethodProvider    $utilities,
    )
    {
    }

    /**
     * TODO
     * - Redirect to setup if no DB config given
     */
    #[Route('/', name: 'legacy.index')]
    public function index(Environment $twig, string $projectDir): Response
    {
        ob_start();

        // Fehler ausgabe definiert
        ini_set('arg_separator.output', '&amp;');

        // Set timezone
        define('TIMEZONE', 'Europe/Zurich');
        date_default_timezone_set(TIMEZONE);

        // Enable debug error reporting
        if (isDebugEnabled()) {
            error_reporting(E_ALL);
        } else {
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
        }

        // Load default values
        $this->loadDefaultValues();

        // Set default page / action variables
        global $page, $mode, $sub, $info;
        $page = (isset($_GET['page']) && $_GET['page'] != "") ? $_GET['page'] : 'overview';
        $mode = (isset($_GET['mode']) && $_GET['mode'] != "") ? $_GET['mode'] : "";
        $sub = $_GET['sub'] ?? null;
        $info = $_GET['info'] ?? null;
        $mode = $_GET['mode'] ?? null;

        // Initialize XAJAX and load functions
        if (!isCLI()) {
            $xajax = require_once $projectDir . '/src/xajax/xajax.inc.php';
        }

        // Set no-cache header
        header("Cache-Control: no-cache, must-revalidate");

        //
        // User and session checks
        //
        $s = $this->userSession;

        // Check for modified etoa tool by pain
        if ($_GET['ttool'] ?? false) {
            file_put_contents('cache/log/paintool.log',
                sprintf("[%s] Pain's modified tool used by %s (%s) from %s on %s\n",
                    date('d.m.Y, H:i:s'),
                    $_POST['login_nick'],
                    $s->getUserId(),
                    $_SERVER['REMOTE_ADDR'],
                    $_GET['page'] ?? 'index'
                ), FILE_APPEND);
        }

        // Perform logout if requested
        if ($_GET['logout'] ?? false) {
            $s->logout();
            forward($this->utilities->getLoginUrl(['page' => 'logout']), 'Logout');
        }

        // Validate session
        if (!$s->validate()) {

            if (!$this->config->get('loginurl')) {
                forward($this->utilities->getLoginUrl());
            } else {
                forward($this->utilities->getLoginUrl(['page' => 'err', 'err' => 'nosession']), 'Ungültige Session', $s->getLastError());
            }
        }

        // Load user data
        global $cu;
        $cu = new \EtoA\Legacy\User($s->getUserId());

        // Check if it is valid user
        if (!$cu->isValid) {
            forward($this->utilities->getLoginUrl(['page' => 'err', 'err' => 'usernotfound']), 'Benutzer nicht mehr vorhanden');
        }

        $properties = $this->userPropertiesRepository->getOrCreateProperties($cu->id);

        //
        // Design / layout properties
        //

        if (!defined('CSS_STYLE')) {
            $design = Design::DIRECTORY . "/official/" . $this->config->get('default_css_style');
            if (filled($properties->cssStyle)) {
                if (is_dir(Design::DIRECTORY . "/official/" . $properties->cssStyle)) {
                    $design = Design::DIRECTORY . "/official/" . $properties->cssStyle;
                }
            }
            define('CSS_STYLE', $design);
        }

        //
        // Page content
        //

        // Referrers prüfen
        $referer_allow = false;
        if (isset($_SERVER["HTTP_REFERER"])) {
            // Referrers
            $referrers = explode("\n", $this->config->get('referers'));
            foreach ($referrers as $k => &$v) {
                $referrers[$k] = trim($v);
            }
            unset($v);
            $referrers[] = 'http://' . $_SERVER['HTTP_HOST'];
            foreach ($referrers as &$rfr) {
                if (str_starts_with($_SERVER["HTTP_REFERER"], $rfr)) {
                    $referer_allow = true;
                }
            }
            unset($rfr);
        }

        try {
            ob_start();

            // Spiel ist generell gesperrt (ausser fŸr erlaubte IP's)
            $allowed_ips = explode("\n", $this->config->get('offline_ips_allow'));

            if ($this->config->getBoolean('offline') && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips, true)) {
                iBoxStart('Spiel offline', 750);
                echo "<img src=\"images/maintenance.jpg\" alt=\"maintenance\" /><br/><br/>";
                if (filled($this->config->get('offline_message'))) {
                    echo BBCodeUtils::toHTML($this->config->get('offline_message')) . "<br/><br/>";
                } else {
                    echo "Das Spiel ist aufgrund von Wartungsarbeiten momentan offline! Schaue sp&auml;ter nochmals vorbei!<br/><br/>";
                }
                echo button("Zur Startseite", $this->utilities->getLoginUrl());
                iBoxEnd();
            } // Login ist gesperrt
            elseif (!$this->config->getBoolean('enable_login') && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips, true)) {
                iBoxStart("Login geschlossen", 750);
                echo "<img src=\"images/keychain.png\" alt=\"maintenance\" /><br/><br/>";
                echo "Der Login momentan geschlossen!<br/><br/>";
                echo button("Zur Startseite", $this->utilities->getLoginUrl());
                iBoxEnd();
            } // Login ist erlaubt aber noch zeitlich zu frŸh
            elseif ($this->config->getBoolean('enable_login') && $this->config->param1Int('enable_login') > time() && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips, true)) {
                iBoxStart("Login noch geschlossen", 750);
                echo "<img src=\"images/keychain.png\" alt=\"maintenance\" /><br/><br/>";
                echo "Das Spiel startet am " . date("d.m.Y", $this->config->param1Int('enable_login')) . " ab " . date("H:i", $this->config->param1Int('enable_login')) . "!<br/><br/>";
                echo button("Zur Startseite", $this->utilities->getLoginUrl());
                iBoxEnd();
            } // Zugriff von anderen als eigenem Server bzw Login-Server sperren
            elseif (!$referer_allow && isset($_SERVER["HTTP_REFERER"])) {
                echo "<div style=\"text-align:center;\">
        <h1>Falscher Referer</h1>
        Der Zugriff auf das Spiel ist nur anderen internen Seiten aus m&ouml;glich! Ein externes Verlinken direkt in das Game hinein ist nicht gestattet! Dein Referer: " . $_SERVER["HTTP_REFERER"] . "<br/><br/>
        <a href=\"" . $this->utilities->getLoginUrl() . "\">Hauptseite</a></div>";
            } // Zugriff erlauben und Inhalt anzeigen
            else {
                if ($s->firstView && $properties->startUpChat == 1) {
                    echo "<script type=\"text/javascript\">" . ExternalUrl::CHAT_ON_CLICK . "</script>";
                }

                if ($cu->isSetup()) {
                    $userPlanets = $this->planetRepository->getUserPlanets((int)$cu->id);
                    $planets = [];
                    $mainplanet = 0;
                    foreach ($userPlanets as $planet) {
                        $planets[] = $planet->id;
                        if ($planet->mainPlanet) {
                            $mainplanet = $planet->id;
                        }
                    }
                    // Todo: check if mainplanet is still 0

                    // Wenn eine ID angegeben wurde (Wechsel des Planeten) wird diese ŸberprŸft
                    //if (!isset($s->echng_key))
                    //	$s->echng_key = mt_rand(100,9999999);

                    $eid = isset($_GET['change_entity']) ? (int)$_GET['change_entity'] : 0;
                    if ($eid > 0 && in_array($eid, $planets, true)) {
                        $cpid = $eid;
                        $s->cpid = $cpid;
                    } elseif (isset($s->cpid) && in_array((int)$s->cpid, $planets, true)) {
                        $cpid = $s->cpid;
                    } else {
                        $cpid = $mainplanet;
                        $s->cpid = $cpid;
                    }

                    global $cp;
                    $cp = \Planet::getById($cpid);
                    $pm = new \EtoA\Legacy\PlanetManager($planets);
                } else {
                    $cu->setNotSetup();
                }
            }

            // Count Messages
            $newMessages = $this->messageRepository->countNewForUser($cu->id);

            // Check new reports
            $newReports = $this->reportRepository->countUserUnread($cu->getId());

            // Number of player's own fleets
            $ownFleetCount = $this->fleetRepository->count(FleetSearch::create()->user($cu->getId()));

            if (isset($cp, $pm)) {
                $currentPlanetData = [
                    'currentPlanetName' => $cp,
                    'currentPlanetImage' => $cp->imagePath('m'),
                    'planetList' => $pm->getLinkList($s->cpid, $page, $mode),
                    'nextPlanetId' => $pm->nextId($s->cpid),
                    'prevPlanetId' => $pm->prevId($s->cpid),
                    'selectField' => $pm->getSelectField($s->cpid),
                ];
            } else {
                $currentPlanetData = [
                    'currentPlanetName' => 'Unbekannt',
                    'planetList' => [],
                    'nextPlanetId' => 0,
                    'prevPlanetId' => 0,
                    'selectField' => null,
                ];
            }

            $infoText = $this->textRepo->find('info');

            $globals = array_merge($currentPlanetData, [
                'design' => strtolower(str_replace('designs/official/', '', CSS_STYLE)),
                'gameTitle' => getGameIdentifier(),
                'templateDir' => '/' . CSS_STYLE,
                'xajaxJS' => $xajax->getJavascript(),
                'bodyTopStuff' => getInitTT(),
                'ownFleetCount' => $ownFleetCount,
                'messages' => $newMessages,
                'newreports' => $newReports,
                'blinkMessages' => $properties->msgBlink,
                'buddys' => $this->buddyListRepository->countFriendsOnline($cu->getId()),
                'buddyreq' => $this->buddyListRepository->hasPendingFriendRequest($cu->getId()),
                'fleetAttack' => check_fleet_incomming($cu->id),
                'enableKeybinds' => $properties->enableKeybinds,
                'isAdmin' => $cu->admin,
                'userPoints' => StringUtils::formatNumber($cu->points),
                'userNick' => $cu->nick,
                'page' => $page,
                'mode' => $mode,
                'infoText' => $infoText->isEnabled() ? $infoText->content : null,
                'viewportScale' => $_SESSION['viewportScale'] ?? 0,
                'fontSize' => ($_SESSION['viewportScale'] ?? 1) * 16 . "px"
            ]);
            foreach ($globals as $key => $value) {
                $twig->addGlobal($key, $value);
            }

            // Include content
            require __DIR__ . '/inc/content.inc.php';

            echo $twig->render('layout/game.html.twig', [
                'content' => ob_get_clean(),
            ]);
        } catch (\Throwable $exception) {
            throw $exception;
        } finally {
            $_SESSION['lastpage'] = $page;
        }

        return new Response(ob_get_clean());
    }

    private function loadDefaultValues(): void
    {
        // Zeit bis "Inaktiv" Status (atm 7 Tage)
        define("USER_INACTIVE_SHOW", $this->config->getInt('user_inactive_days'));

        // Zeit bis "Inaktiv" Status Long (atm 14 Tage)
        define("USER_INACTIVE_LONG", $this->config->param2Int('user_inactive_days'));

        // Kriegsdauer
        define("WAR_DURATION", 3600 * $this->config->getInt('alliance_war_time'));
        define("PEACE_DURATION", 3600 * $this->config->param1Int('alliance_war_time'));

        // Flugzeit Faktor (wirkt nicht auf Start/Landezeit)
        define("FLEET_FACTOR_F", $this->config->getFloat('flight_flight_time'));

        // Startzeit Faktor
        define("FLEET_FACTOR_S", $this->config->getFloat('flight_start_time'));

        // Landezeit Faktor
        define("FLEET_FACTOR_L", $this->config->getFloat('flight_land_time'));

        // = true/1 um aktive user zu invasieren
        define("INVADE_ACTIVE_USER", $this->config->getBoolean('invade_active_users'));

        // Absolute Puntktegrenze
        define("USER_ATTACK_MIN_POINTS", $this->config->getInt('user_attack_min_points'));

        // Prozentualer Punkteunterschied
        define("USER_ATTACK_PERCENTAGE", $this->config->getFloat('user_attack_percentage'));

        // Zeit in stunden, wie lange die auktion nach ablauf noch zu sehen ist
        define("AUCTION_DELAY_TIME", $this->config->getInt('market_auction_delay_time'));

        // Titan Taxe
        define("MARKET_METAL_FACTOR", $this->runtimeDataStore->get('market_rate_0', (string)1));

        // Silizium Taxe
        define("MARKET_CRYSTAL_FACTOR", $this->runtimeDataStore->get('market_rate_1', (string)1));

        // PVC Taxe
        define("MARKET_PLASTIC_FACTOR", $this->runtimeDataStore->get('market_rate_2', (string)1));

        // Tritium Taxe
        define("MARKET_FUEL_FACTOR", $this->runtimeDataStore->get('market_rate_3', (string)1));

        // Nahrung Taxe
        define("MARKET_FOOD_FACTOR", $this->runtimeDataStore->get('market_rate_4', (string)1));

        // Mindestpreisgrenze der Schiffe 1=100%
        define("SHIP_PRICE_FACTOR_MIN", $this->config->getFloat('ship_price_factor_min'));

        // Höchstpreisgrenze der Schiffe
        define("SHIP_PRICE_FACTOR_MAX", $this->config->getFloat('ship_price_factor_max'));

        // Mindestpreisgrenze der Rohstoffe
        define("RESS_PRICE_FACTOR_MIN", $this->config->getFloat('res_price_factor_min'));

        // Höchstpreisgrenze der Schiffe
        define("RESS_PRICE_FACTOR_MAX", $this->config->getFloat('res_price_factor_max'));

        // Mindestpreisgrenze der Autkionen (summiert aus Rohstoffen und Schiffen)
        define("AUCTION_PRICE_FACTOR_MIN", $this->config->getFloat('auction_price_factor_min'));

        // Höchstpreisgrenze der Autkionen (summiert aus Rohstoffen und Schiffen)
        define("AUCTION_PRICE_FACTOR_MAX", $this->config->getFloat('auction_price_factor_max'));

        // Gebot muss mindestens X% höher sein als jenes des Vorgebotes entsprechen
        define("AUCTION_OVERBID", $this->config->getFloat('auction_overbid'));

        // Zuschlagsfaktor auf die Preise
        define("MARKET_SELL_TAX", $this->config->getFloat('market_sell_tax'));

        // Mindestdauer einer Autkion (in Tagen)
        define("AUCTION_MIN_DURATION", $this->config->getInt('auction_min_duration'));

        // Mindest Marktlevel um Rohstoffe zu kaufen und verkaufen
        define("MIN_MARKET_LEVEL_RESS", $this->config->getInt('min_market_level_res'));

        // Mindest Marktlevel um Schiffe zu kaufen und verkaufen
        define("MIN_MARKET_LEVEL_SHIP", $this->config->getInt('min_market_level_ship'));

        // Mindest Marktlevel um Auktionen anzubieten und selber zu bieten
        define("MIN_MARKET_LEVEL_AUCTION", $this->config->getInt('min_market_level_auction'));
    }
}
