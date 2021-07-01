<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\RuntimeDataStore;

/** @var RuntimeDataStore */
$runtimeDataStore = $app[RuntimeDataStore::class];

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

// Zeit bis "Inaktiv" Status (atm 7 Tage)
define("USER_INACTIVE_SHOW", $config->getInt('user_inactive_days'));

// Zeit bis "Inaktiv" Status Long (atm 14 Tage)
define("USER_INACTIVE_LONG", $config->param2Int('user_inactive_days'));

// Nachrichten
define("FLOOD_CONTROL", $config->getInt('msg_flood_control'));    // Wartezeit bis zur nächsten Nachricht

// Kriegsdauer
define("WAR_DURATION", 3600 * $config->getInt('alliance_war_time'));
define("PEACE_DURATION", 3600 * $config->param1Int('alliance_war_time'));

// Flugzeit Faktor (wirkt nicht auf Start/Landezeit)
define("FLEET_FACTOR_F", $config->getFloat('flight_flight_time'));

// Startzeit Faktor
define("FLEET_FACTOR_S", $config->getFloat('flight_start_time'));

// Landezeit Faktor
define("FLEET_FACTOR_L", $config->getFloat('flight_land_time'));

// = true/1 um aktive user zu invasieren
define("INVADE_ACTIVE_USER", $config->getBoolean('invade_active_users'));

// Absolute Puntktegrenze
define("USER_ATTACK_MIN_POINTS", $config->getInt('user_attack_min_points'));

// Prozentualer Punkteunterschied
define("USER_ATTACK_PERCENTAGE", $config->getFloat('user_attack_percentage'));

// Aktion beim versenden von Rohstoffen
define("FLEET_ACTION_RESS", $config->get('market_ship_action_ress'));

// Aktion beim versenden von Schiffen oder Schiffe&Rohstoffe
define("FLEET_ACTION_SHIP", $config->get('market_ship_action_ship'));

// Zeit in stunden, wie lange die auktion nach ablauf noch zu sehen ist
define("AUCTION_DELAY_TIME", $config->getInt('market_auction_delay_time'));

// Titan Taxe
define("MARKET_METAL_FACTOR", $runtimeDataStore->get('market_rate_0', (string) 1));

// Silizium Taxe
define("MARKET_CRYSTAL_FACTOR", $runtimeDataStore->get('market_rate_1', (string) 1));

// PVC Taxe
define("MARKET_PLASTIC_FACTOR", $runtimeDataStore->get('market_rate_2', (string) 1));

// Tritium Taxe
define("MARKET_FUEL_FACTOR", $runtimeDataStore->get('market_rate_3', (string) 1));

// Nahrung Taxe
define("MARKET_FOOD_FACTOR", $runtimeDataStore->get('market_rate_4', (string) 1));

// Mindestpreisgrenze der Schiffe 1=100%
define("SHIP_PRICE_FACTOR_MIN", $config->getFloat('ship_price_factor_min'));

// Höchstpreisgrenze der Schiffe
define("SHIP_PRICE_FACTOR_MAX", $config->getFloat('ship_price_factor_max'));

// Mindestpreisgrenze der Rohstoffe
define("RESS_PRICE_FACTOR_MIN", $config->getFloat('res_price_factor_min'));

// Höchstpreisgrenze der Schiffe
define("RESS_PRICE_FACTOR_MAX", $config->getFloat('res_price_factor_max'));

// Mindestpreisgrenze der Autkionen (summiert aus Rohstoffen und Schiffen)
define("AUCTION_PRICE_FACTOR_MIN", $config->getFloat('auction_price_factor_min'));

// Höchstpreisgrenze der Autkionen (summiert aus Rohstoffen und Schiffen)
define("AUCTION_PRICE_FACTOR_MAX", $config->getFloat('auction_price_factor_max'));

// Gebot muss mindestens X% höher sein als jenes des Vorgebotes entsprechen
define("AUCTION_OVERBID", $config->getFloat('auction_overbid'));

// Zuschlagsfaktor auf die Preise
define("MARKET_SELL_TAX", $config->getFloat('market_sell_tax'));

// Mindestdauer einer Autkion (in Tagen)
define("AUCTION_MIN_DURATION", $config->getInt('auction_min_duration'));

// Mindest Marktlevel um Rohstoffe zu kaufen und verkaufen
define("MIN_MARKET_LEVEL_RESS", $config->getInt('min_market_level_res'));

// Mindest Marktlevel um Schiffe zu kaufen und verkaufen
define("MIN_MARKET_LEVEL_SHIP", $config->getInt('min_market_level_ship'));

// Mindest Marktlevel um Auktionen anzubieten und selber zu bieten
define("MIN_MARKET_LEVEL_AUCTION", $config->getInt('min_market_level_auction'));

// Legt fest, wieviele vergangene Werte bei der Marktkursberechnung mit einbezogen werden
define('MARKET_RATES_COUNT', $config->getInt('market_rates_count'));

// Minimaler Marktkurs
define('MARKET_RATE_MIN', $config->getFloat('market_rate_min'));

// Maximaler Marktkurs
define('MARKET_RATE_MAX', $config->getFloat('market_rate_max'));
