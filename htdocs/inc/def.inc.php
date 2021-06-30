<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\RuntimeDataStore;

/** @var RuntimeDataStore */
$runtimeDataStore = $app['etoa.runtime.datastore'];

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

/***********************************/
/* Design, Layout, Allgmeine Pfade */
/***********************************/

//
// Layout
//

/****************************/
/* Allgemeine Einstellungen */
/****************************/

//
// Paswort und Nicklänge
//

// Minimale Passwortlänge
define("PASSWORD_MINLENGHT", $config->getInt('password_minlength'));

// Minimale Passwortlänge
define("PASSWORD_MAXLENGHT", $config->getInt('password_minlength'));

// Minimale Nicklänge
define("NICK_MINLENGHT", $config->param1Int('nick_length'));

// Maximale Nicklänge
define("NICK_MAXLENGHT", $config->param2Int('nick_length'));

// Minimale Nicklänge
define("NAME_MAXLENGTH", $config->getInt('name_length'));

//
// Inaktive & Urlaubsmodus
//

// Minimale Umode-Dauer
define("MIN_UMOD_TIME", $config->getInt('hmode_days'));

//MAximale Umode-Dauer
define("MAX_UMOD_TIME", $config->param1Int('hmode_days'));

// Vergangene Zeit bis Löschung eines Users (atm 21 Tage)
define("USER_INACTIVE_DELETE", $config->param1Int('user_inactive_days'));

// Vergangene Zeit bis Löschung falls nie eingeloggt & Zeit bis "Inaktiv" Status Long (atm 14 Tage)
define("USER_NOTLOGIN_DELETE", $config->param2Int('user_inactive_days'));

// Zeit bis "Inaktiv" Status (atm 7 Tage)
define("USER_INACTIVE_SHOW", $config->getInt('user_inactive_days'));

// UNIX-Time (last user action atm -7d)
define("USER_INACTIVE_TIME", time() - (24 * 3600 * USER_INACTIVE_SHOW));

// Zeit bis "Inaktiv" Status Long (atm 14 Tage)
define("USER_INACTIVE_LONG", $config->param2Int('user_inactive_days'));

// UNIX-Time (last user action long -14d)
define("USER_INACTIVE_TIME_LONG", time() - (24 * 3600 * USER_INACTIVE_LONG));

//
// Universum
//

// Anzahl Zellen x
define("CELL_NUM_X", $config->param1Int('num_of_cells'));

// Anzahl Zellen y
define("CELL_NUM_Y", $config->param2Int('num_of_cells'));

// Wurmlöcher
define("WH_UPDATE_AFFECT_TIME", $config->getInt('wh_update'));
define("WH_UPDATE_AFFECT_CNT", $config->param1Int('wh_update'));

// Nachrichten
define("FLOOD_CONTROL", $config->getInt('msg_flood_control'));    // Wartezeit bis zur nächsten Nachricht

//
// Punkteberechnung
//

// 1 Punkt für X (STATS_USER_POINTS) verbaute Rohstoffe
define("STATS_USER_POINTS", $config->param1Int('points_update'));

// 1 Punkt für X (STATS_ALLIANCE_POINTS) User Punkte
define("STATS_ALLIANCE_POINTS", $config->param2Int('points_update'));

//
// Sonstiges
//

// Anzahl Nahrung, welche Arbeiter benötigen
define("PEOPLE_FOOD_USE", $config->getInt('people_food_require'));

// Maximale Anzahl Planeten
define("USER_MAX_PLANETS", $config->getInt('user_max_planets'));

//Zeit bis Löschantrag ausgeführt wird
define("USER_DELETE_DAYS", $config->getInt('user_delete_days'));

//
// Spezialiasten
//

// Minimal Punkte für Spezialist (VERALTET)
define("SPECIALIST_MIN_POINTS_REQ", $config->param2Int('specialistconfig'));

// Maximale Kostensteigerung
define('SPECIALIST_MAX_COSTS_FACTOR', $config->param1Float('specialistconfig'));

// Verfügbare Spezialisten pro Typ basierend auf Faktor * Anzahl User
define('SPECIALIST_AVAILABILITY_FACTOR', $config->getFloat('specialistconfig'));

// Kriegsdauer
define("WAR_DURATION", 3600 * $config->getInt('alliance_war_time'));
define("PEACE_DURATION", 3600 * $config->param1Int('alliance_war_time'));

/****************************************************/
/* Startwerte (bei erstellung eines neuen Accounts) */
/****************************************************/

// Anzahl Titan
define("USR_START_METAL", $config->getInt('user_start_metal'));

// Anzahl Silizium
define("USR_START_CRYSTAL", $config->getInt('user_start_crystal'));

// Anzahl PVC
define("USR_START_PLASTIC", $config->getInt('user_start_plastic'));

// Anzahl Tritium
define("USR_START_FUEL", $config->getInt('user_start_fuel'));

// Anzahl Nahrung
define("USR_START_FOOD", $config->getInt('user_start_food'));

// Anzahl Bewohner
define("USR_START_PEOPLE", $config->getInt('user_start_people'));

// "Startplanet" Name
define("USR_PLANET_NAME", $config->getInt('user_planet_name'));

/*********/
/* Zeit  */
/*********/

// Allgegenwertiger Faktor in allen build_times
define("GLOBAL_TIME", $config->getInt('global_time'));

// Gebäudebau Faktor
define("BUILD_BUILD_TIME", $config->getFloat('build_build_time'));

// Forschungsbau Faktor
define("RES_BUILD_TIME", $config->getFloat('res_build_time'));

// Schiffsbau Faktor
define("SHIP_BUILD_TIME", $config->getFloat('ship_build_time'));

// Verteidigungsbau Faktor
define("DEF_BUILD_TIME", $config->getFloat('def_build_time'));

// Flugzeit Faktor (wirkt nicht auf Start/Landezeit)
define("FLEET_FACTOR_F", $config->getFloat('flight_flight_time'));

// Startzeit Faktor
define("FLEET_FACTOR_S", $config->getFloat('flight_start_time'));

// Landezeit Faktor
define("FLEET_FACTOR_L", $config->getFloat('flight_land_time'));

/*************************/
/* Flotten & Kampfsystem */
/*************************/

//
// Invasion
//

// Grundinvasionschance
define("INVADE_POSSIBILITY", $config->getFloat('invade_possibility'));

// MAX. Invasionschance
define("INVADE_MAX_POSSIBILITY", $config->param1Float('invade_possibility'));

// Min. Invasionschance
define("INVADE_MIN_POSSIBILITY", $config->param2Float('invade_possibility'));

// wird nicht benötigt!
define("INVADE_SHIP_DESTROY", $config->getFloat('invade_ship_destroy'));

// = true/1 um aktive user zu invasieren
define("INVADE_ACTIVE_USER", $config->getBoolean('invade_active_users'));

//
// Kampfsystem
//

// Prozentualer Wiederaufbau der Def
define("DEF_RESTORE_PERCENT", $config->getFloat('def_restore_percent'));

// Def ins Trümmerfeld
define("DEF_WF_PERCENT", $config->getFloat('def_wf_percent'));

// Ship ins Trümmerfeld
define("SHIP_WF_PERCENT", $config->getFloat('ship_wf_percent'));

// Chance-Faktor beim Bombardieren + Deaktivieren
define("SHIP_BOMB_FACTOR", $config->getInt('ship_bomb_factor'));

//
// Anfängerschutz
//

// Absolute Puntktegrenze
define("USER_ATTACK_MIN_POINTS", $config->getInt('user_attack_min_points'));

// Prozentualer Punkteunterschied
define("USER_ATTACK_PERCENTAGE", $config->getFloat('user_attack_percentage'));

/*********/
/* Markt */
/*********/

// Aktion beim versenden von Rohstoffen
define("FLEET_ACTION_RESS", $config->get('market_ship_action_ress'));

// Aktion beim versenden von Schiffen oder Schiffe&Rohstoffe
define("FLEET_ACTION_SHIP", $config->get('market_ship_action_ship'));

// Minimal Flugzeit
define("FLIGHT_TIME_MIN", $config->param1Int('market_ship_flight_time'));

// Maximal Flugzeit
define("FLIGHT_TIME_MAX", $config->param2Int('market_ship_flight_time'));

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
