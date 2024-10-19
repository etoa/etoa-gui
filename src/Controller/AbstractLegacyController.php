<?php

namespace EtoA\Controller;

use EtoA\BuddyList\BuddyListRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Legacy\UserSession;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\Support\GameVersionService;
use EtoA\Support\RuntimeDataStore;
use EtoA\Text\TextRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserPropertiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

abstract class AbstractLegacyController extends AbstractController
{
    public function __construct(
        protected readonly ConfigurationService     $config,
        protected readonly UserPropertiesRepository $userPropertiesRepository,
        protected readonly PlanetRepository         $planetRepository,
        protected readonly MessageRepository        $messageRepository,
        protected readonly ReportRepository         $reportRepository,
        protected readonly FleetRepository          $fleetRepository,
        protected readonly TextRepository           $textRepo,
        protected readonly BuddyListRepository      $buddyListRepository,
        protected readonly RuntimeDataStore         $runtimeDataStore,
        protected readonly UserSession              $userSession,
        protected readonly GameVersionService       $versionService,
        protected readonly Environment              $twig,
        protected readonly string                   $projectDir,
        protected readonly RequestStack             $requestStack,
    )
    {
    }

    protected function bootstrap(): void
    {
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