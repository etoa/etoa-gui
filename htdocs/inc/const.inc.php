<?php

	define('APP_NAME', 'EtoA');

	// Timezone
	define('TIMEZONE', 'Europe/Zurich');

	// OS-Version feststellen
	if (defined('POSIX_F_OK'))
	{
		define('UNIX', true);
		define('WINDOWS', false);
	}
	else
	{
		define('UNIX', false);
		define('WINDOWS', true);
	}

	/***********
	* Database *
	***********/

	define("WINDOWS_MYSQL_PATH", "c:\\xampp\\mysql\\bin\\mysql.exe");
	define("WINDOWS_MYSQLDUMP_PATH", "c:\\xampp\\mysql\\bin\\mysqldump.exe");

	// Cache directory
	if (!defined('CACHE_ROOT')) {
		define('CACHE_ROOT', RELATIVE_ROOT.'cache');
	}

	// Log directory
	if (!defined('LOG_DIR')) {
		define('LOG_DIR', RELATIVE_ROOT.'log');
	}

	// Class directory
	if (!defined('CLASS_ROOT')) {
		define('CLASS_ROOT', RELATIVE_ROOT.'classes');
	}

	// Data file directory
	if (!defined('DATA_DIR')) {
		define('DATA_DIR', RELATIVE_ROOT."data");
	}

	// Image directory
	if (!defined('IMAGE_DIR')) {
		define('IMAGE_DIR', RELATIVE_ROOT."images");
	}

	// xAjax
	define('XAJAX_DIR', RELATIVE_ROOT."libs/xajax");
	define('XAJAX_DEBUG', false);

	// Admin mode?
	if (!defined('ADMIN_MODE')) {
		define('ADMIN_MODE', false);
	}

	define('ERROR_LOGFILE', LOG_DIR."/errors.log");
	define('DBERROR_LOGFILE', LOG_DIR."/dberrors.log");

	// Entwickler Link
	define("DEVCENTER_PATH", "http://dev.etoa.ch");

	// Entwickler Link (popup)
	define("DEVCENTER_ONCLICK", "window.open('".DEVCENTER_PATH."','dev','width=1024,height=768,scrollbars=yes');");

  /***********************************/
  /* Directory names                 */
  /***********************************/

	// CSS Style
	define("DESIGN_DIRECTORY", "designs");

	// Design configuration file name
	define("DESIGN_CONFIG_FILE_NAME", "design.xml");

	// Design main template file name
	define("DESIGN_TEMPLATE_FILE_NAME", "template.html");

	// Design main stylesheet file name
	define("DESIGN_STYLESHEET_FILE_NAME", "style.css");

	// Design main script file name
	define("DESIGN_SCRIPT_FILE_NAME", "scripts.js");

	// Imagepack configuration file name
	define("IMAGEPACK_CONFIG_FILE_NAME", "imagepack.xml");

    // Eventhandler configuration file name
    define('EVENTHANDLER_CONFIG_FILE_NAME', 'eventhandler.conf');

	// Tech Ordner
	define("IMAGE_TECHNOLOGY_DIR", "technologies");

	// Schiffe Ordner
	define("IMAGE_SHIP_DIR", "ships");

	// Planeten Ordner
	define("IMAGE_PLANET_DIR", "planets");

	// Gebäude Ordner
	define("IMAGE_BUILDING_DIR", "buildings");

	// Def Ordner
	define("IMAGE_DEF_DIR", "defense");

	// Allianzgebäude
	define("IMAGE_ALLIANCE_BUILDING_DIR", "abuildings");

	// Allianztech
	define("IMAGE_ALLIANCE_TECHNOLOGY_DIR", "atechnologies");

  /***********************************/
  /* Installer */
    /***********************************/

    // Default login server URL
    define('INSTALLER_DEFAULT_LOGINSERVER_URL', 'https://etoa.ch');

  /***********************************/
  /* Design, Layout, Allgmeine Pfade */
  /***********************************/

	//
	// Externe Pfade
	//

	// Helpcenter Link
	define("HELPCENTER_URL", "http://www.etoa.ch/help/?page=faq");
	define('HELPCENTER_ONCLICK', "window.open('".HELPCENTER_URL."','helpcenter','width=1024,height=700,scrollbars=yes');");

	// Forum Link
	define("FORUM_URL", "http://forum.etoa.ch");

	// Chat
	define('CHAT_URL', "chatframe.php");
	define('CHAT_ONCLICK', "parent.top.location='chatframe.php';");

	// Teamspeak
	define('TEAMSPEAK_URL', "https://discord.gg/7d2ndEU");
	define('TEAMSPEAK_ONCLICK', "window.open('".TEAMSPEAK_URL."','_blank');");

	// Game-Rules
	define('RULES_URL', 'http://www.etoa.ch/regeln');
	define('RULES_ONCLICK', "window.open('".RULES_URL."','rules','width=auto,height=auto,scrollbars=yes');");

	// Privacy statement
	define('PRIVACY_URL', 'http://www.etoa.ch/privacy');

	// URL for user banner HTML snippet
	define('USERBANNER_LINK_URL', 'http://www.etoa.ch');


  /***********************************/
  /* Directory- and file paths       */
  /***********************************/

	// RSS Dir
	define('RSS_DIR', CACHE_ROOT."/rss");

	// Townhall-RSS-File
	define('RSS_TOWNHALL_FILE', RSS_DIR."/townhall.rss");

	//
	// Pfade
	//

	// Smilies
	define("SMILIE_DIR", IMAGE_DIR."/smilies");

	// Bilder
	define("IMAGEPACK_DIRECTORY", IMAGE_DIR."/imagepacks");

	// Bilder
	define("IMAGEPACK_DOWNLOAD_DIRECTORY", CACHE_ROOT."/imagepacks");

  /*********************/
  /* Zufallsereignisse */
  /*********************/

	define("RANDOM_EVENTS_PER_UPDATE", 1);

  /****************************/
  /* Allgemeine Einstellungen */
  /****************************/

	// Homepage
	define('DEFAULT_PAGE', "overview");

	// Rohstoffbenennung
	define("RES_METAL", "Titan");
	define("RES_CRYSTAL", "Silizium");
	define("RES_PLASTIC", "PVC");
	define("RES_FUEL", "Tritium");
	define("RES_FOOD", "Nahrung");
	define("RES_POWER", "Energie");
	define("RES_TIME", "Zeit");
	define("RES_FIELDS", "Felder");

	$resNames = array(RES_METAL,RES_CRYSTAL,RES_PLASTIC,RES_FUEL,RES_FOOD);

	define('RES_ICON_METAL', '<img class="resIcon" src="images/resources/metal_s.png" alt="'.RES_METAL.'" />');
	define('RES_ICON_CRYSTAL', '<img class="resIcon" src="images/resources/crystal_s.png" alt="'.RES_CRYSTAL.'" />');
	define('RES_ICON_PLASTIC', '<img class="resIcon" src="images/resources/plastic_s.png" alt="'.RES_PLASTIC.'" />');
	define('RES_ICON_FUEL', '<img class="resIcon" src="images/resources/fuel_s.png" alt="'.RES_FUEL.'" />');
	define('RES_ICON_FOOD', '<img class="resIcon" src="images/resources/food_s.png" alt="Nahrung" />');
	define('RES_ICON_POWER', '<img class="resIcon" src="images/resources/power_s.png" alt="Energie" />');
	define('RES_ICON_POWER_USE', '<img class="resIcon" src="images/resources/poweru_s.png" alt="Energieverbrauch" />');
	define('RES_ICON_PEOPLE', '<img class="resIcon" src="images/resources/people_s.png" alt="Bevölkerung" />');
	define('RES_ICON_TIME', '<img class="resIcon" src="images/resources/time_s.png" alt="Zeit" />');
	define('RES_ICON_FIELDS', '<img class="resIcon" src="images/resources/field_s.png" alt="Felder" />');

	$resIcons = array(RES_ICON_METAL,RES_ICON_CRYSTAL,RES_ICON_PLASTIC,RES_ICON_FUEL,RES_ICON_FOOD);

	// Regular expressions
	define('REGEXP_NAME', '/^.[^0-9\'\"\?\<\>\$\!\=\;\&]*$/');
	define('REGEXP_NICK', '/^.[^\'\"\?\<\>\$\!\=\;\&]*$/');

	// Minimale Sperrzeit für Kolonielöschung
	define("COLONY_DELETE_THRESHOLD", 24*3600*5);

	// Galaxy map
 	define('GALAXY_MAP_DOT_RADIUS', 3);
	define('GALAXY_MAP_WIDTH', 500);
	define('GALAXY_MAP_LEGEND_HEIGHT', 40);

	//
	// Nachrichten
	//

	// Cat-ID Persönliche Nachrichten
	define("USER_MSG_CAT_ID", 1);

	define('MISC_MSG_CAT_ID', 5);

	// Cat-ID Spionageberichte
	define("SHIP_SPY_MSG_CAT_ID", 2);

	// Cat-ID Kriegsberichte
	define("SHIP_WAR_MSG_CAT_ID", 3);

	// Cat-ID Überwachungsberichte
	define("SHIP_MONITOR_MSG_CAT_ID", 4);

	// Cat-ID Sonstige Nachrichten
	define("SHIP_MISC_MSG_CAT_ID", 5);

	// Cat-ID Allianz
	define("MSG_ALLYMAIL_CAT", 6);

	// Punkteberechnung
	define("ENABLE_USERTITLES", 1);
	define("USERTITLES_MIN_POINTS", 10000);

	define('DIPLOMACY_POINTS_PER_NEWS', 4);
	define('DIPLOMACY_POINTS_PER_PACT', 1);
	define('DIPLOMACY_POINTS_MIN_PACT_DURATION', 3600*24*1);
	define('DIPLOMACY_POINTS_PER_WAR', 1);

	define('TRADE_POINTS_PER_TRADE', 1);
	define('TRADE_POINTS_PER_AUCTION', 1);
	define('TRADE_POINTS_PER_TRADETEXT', 1);
	define('TRADE_POINTS_TRADETEXT_MIN_LENGTH', 15);

	define('BATTLE_POINTS_A_W', 3);
	define('BATTLE_POINTS_A_D', 1);
	define('BATTLE_POINTS_A_L', 1);
	define('BATTLE_POINTS_D_W', 2);
	define('BATTLE_POINTS_D_D', 1);
	define('BATTLE_POINTS_D_L', 0);
	define('BATTLE_POINTS_SPECIAL', 1);

	// Tipps beim Start aktivieren
	define("ENABLE_TIPS", 1);

	// Permissions for uploaded files
	define('FILE_UPLOAD_PERMS', 0644);

	// Zeitverzögerung zwischen zwei Bauaufträgen in der Warteschlange
	define("BUILDING_QUEUE_DELAY", 60);

  /****************/
  /* Technologien */
  /****************/

	// ID der Strukturtechnik
	define("STRUCTURE_TECH_ID", 9);

	// ID der Schildtechnik
	define("SHIELD_TECH_ID", 10);

	// ID der Waffentechnik
	define("WEAPON_TECH_ID", 8);

	// ID der Regenatechnik
	define("REGENA_TECH_ID", 19);

	// ID der Tarntechnik
	define("TARN_TECH_ID", 11);

	// ID der Tarntechnik
  	define("COMPUTER_TECH_ID", 25);

	// ID der Recyclingtechnologie
	define("RECYC_TECH_ID", 12);

	// ID der Bombentechnik
	define("BOMB_TECH_ID", 15);

	// ID der Gentechnologie
	define('GEN_TECH_ID', 23);

	// ID der Energietechnologie
	define('ENERGY_TECH_ID', 3);

	// ID der Wurmlochforschung
	define("TECH_WORMHOLE", 22);

	//
	// Ab diesem Level Sieht man von gegnerischen flotten diese infos...
	//

	// ...Gebäude des Gegners
	define("SPY_TECH_ID", 7);
	// ...Gesinnung des Gegners (friedlich/feindlich)
	define("SPY_TECH_SHOW_ATTITUDE", 1);
	// ...Anzahl der Schiffe
	define("SPY_TECH_SHOW_NUM", 3);
	// ...die verschiedenen Schiffe in der Flotte
	define("SPY_TECH_SHOW_SHIPS", 5);
	// ...die genaue Anzahl von jedem Schiffstyp
	define("SPY_TECH_SHOW_NUMSHIPS", 7);
	// ...Aktion (Angriff/Spionage etc.)
	define("SPY_TECH_SHOW_ACTION", 9);

	//
	// Ab diesem Level Sieht man beim Spionieren...
	//

	// ...die Gebäude des Gegners
	define("SPY_ATTACK_SHOW_BUILDINGS", 1);
	// ...die Forschung des Gegners
	define("SPY_ATTACK_SHOW_RESEARCH", 3);
	// ...die Schiffe des Gegners
	define("SPY_ATTACK_SHOW_SHIPS", 7);
	// ...die Defense des Gegners
	define("SPY_ATTACK_SHOW_DEFENSE", 5);
	// ...die Ressourcen des Gegners
	define("SPY_ATTACK_SHOW_RESSOURCEN", 9);
	// ...die Supportflotten auf dem Planeten
	define("SPY_ATTACK_SHOW_SUPPORT", 11);

	//
	// Spionageabwehr
	//

	// Maximale Spionageabwehr in Prozent
	define('SPY_DEFENSE_MAX', 90);

	// Spionageabwehr: Gewichtung der Technologien
	define('SPY_DEFENSE_FACTOR_TECH', 20);

	// Spionageabwehr: Gewichtung der Sonden
	define('SPY_DEFENSE_FACTOR_SHIPS', 0.5);

	// Spionageabwehr/Tarnabwehr: Gewichtung der Tarntechnik
	define('SPY_DEFENSE_FACTOR_TARN', 10);

  /***********/
  /* Gebäude */
  /***********/

	define("BUILDING_GENERAL_CAT", 1);
	define("BUILDING_RES_CAT", 2);
	define("BUILDING_POWER_CAT", 3);
	define("BUILDING_STORE_CAT", 4);

	define("RES_BUILDING_CAT", 2);

	// Gebäude welches den Status des Bauhofes wiedergibt
	define("BUILD_BUILDING_ID", 6);

	// Gebäude welches den Status das Wohnheim wiedergibt
	define("PEOPLE_BUILDING_ID", 7);

	// Gebäude welches den Status des Forschungslabors wiedergibt
	define("TECH_BUILDING_ID", 8);

	// Gebäude welches den Status der Schiffswerft wiedergibt
	define("SHIP_BUILDING_ID", 9);

	// Gebäude welches den Status der Waffenfabrik wiedergibt
	define("DEF_BUILDING_ID", 10);

	// ID der Schiffswerft
	define("SHIPYARD_ID", 9);

	// ID der Waffenfabrik
	define("FACTORY_ID", 10);

	// ID des Marktplatzes
	define('MARKTPLATZ_ID', 21);

	// ID der Flottenkontrolle
	define("FLEET_CONTROL_ID", 11);

	// ID des Kryptocenters
	define("BUILD_CRYPTO_ID", 24);

	// ID des Raketensilos
	define("BUILD_MISSILE_ID", 25);

	// ID des Rohstoffbunkers
	define("RES_BUNKER_ID", 26);

	// ID des Flottenbunkers
	define("FLEET_BUNKER_ID", 27);

	//
	// Allianzgebäude
	//

	// ID des Allianzmarktplatzes
	define("ALLIANCE_MARKET_ID", 2);

	// ID des Allianzschiffwerftes
	define("ALLIANCE_SHIPYARD_ID", 3);

	define("ALLIANCE_CRYPTO_ID", 6);

	define("ALLIANCE_MAIN_ID", 1);

	define("ALLIANCE_FLEET_CONTROL_ID", 4);

	define("ALLIANCE_RESEARCH_ID", 5);

	define("ALLIANCE_TECH_TARN_ID", 4);

	define("ALLIANCE_TECH_SPY_ID", 8);

	/*************************/
	/* Flotten & Kampfsystem */
	/*************************/

	//
	// Sonstige Flottendefinitionen
	//

	// Flotten Log ID (Kategorie)
	define("FLEET_ACTION_LOG_CAT", 13);

	// Anzahl Flotten die OHNE Flottenkontrolle fliegen können
	define("FLEET_NOCONTROL_NUM", 1);

	// Kategorie der Antriebstechniken
	define("TECH_SPEED_CAT", 1);

	// Standartflug "Transport hinflug" ??? (wieso das?)
	define("DEFAULT_ACTION", "to");

	// Anzahl Runden
	define("BATTLE_ROUNDS", 5);

  /*********/
  /* Markt */
  /*********/

	// Handelsschiff ID
	define("MARKET_SHIP_ID",16);

	// Log-Cat ID
	define("MARKET_LOG_CAT", 7);

	// Anzahl Rohstofftypen im Spiel
	define('NUM_RESOURCES', 5);

  /****************/
  /* Allianzboard */
  /****************/

	// Verzeichnis der Forenicons
	define("BOARD_BULLET_DIR", "images/boardbullets");

	// Verzeichnis der Avatare
	define("BOARD_AVATAR_DIR", CACHE_ROOT."/avatars");

	// Standard Foren-Icon
	define("BOARD_DEFAULT_IMAGE", "default.png");
	define("BOARD_ADMIN_RANK", 4);

	// Tabelle der Forentopics
	define("BOARD_TOPIC_TABLE", "allianceboard_topics");

	// Tabelle der Forenposts
	define("BOARD_POSTS_TABLE", "allianceboard_posts");

	// Tabelle der Kategorien
	define("BOARD_CAT_TABLE", "allianceboard_cat");

	// Avatar-Breite
	define("BOARD_AVATAR_MAX_WIDTH", 1024);

	// Avatar-Höhe
	define("BOARD_AVATAR_MAX_HEIGHT", 1024);

	// Profilbild-Grösse in Byte
	define("BOARD_AVATAR_MAX_SIZE", 2097152);

	// Avatar-Breite
	define("BOARD_AVATAR_WIDTH", 64);
	define("BOARD_AVATAR_HEIGHT", 64);

	if (!defined('GD_VERSION')) {
		define("GD_VERSION",2);
	}

	//
	// Profilbild
	//

	// Verzeichnis der User-Profilbilder
	define("PROFILE_IMG_DIR", CACHE_ROOT."/userprofiles");

	// Profilbild-Breite
	define("PROFILE_IMG_WIDTH", 640);

	// Profilbild-Höhe
	define("PROFILE_IMG_HEIGHT", 480);

	// Max. Profilbild-Breite
	define("PROFILE_MAX_IMG_WIDTH", 1280);

	// Max. Profilbild-Höhe
	define("PROFILE_MAX_IMG_HEIGHT", 1024);

	// Profilbild-Grösse in Byte
	define("PROFILE_IMG_MAX_SIZE", 2097152);

	//
	// Allianzbild
	//

	// Verzeichnis der Allianz-Bilder
	define("ALLIANCE_IMG_DIR", CACHE_ROOT."/allianceprofiles");

	// Allianzbild-Breite
	define("ALLIANCE_IMG_WIDTH", 800);

	// Allianzbild-Höhe
	define("ALLIANCE_IMG_HEIGHT", 600);

	// Max. Allianzbild-Breite
	define("ALLIANCE_IMG_MAX_WIDTH", 1280);

	// Max. Allianzbild-Höhe
	define("ALLIANCE_IMG_MAX_HEIGHT", 1024);

	// Max. Allianzbild-Grösse in Byte
	define("ALLIANCE_IMG_MAX_SIZE", 2000000);

  /****************/
  /* Allianz Flottenkontrolle */
  /****************/
	define("ALLIANCE_FLEET_SHOW", 1);
	define("ALLIANCE_FLEET_SHOW_DETAIL", 2);
	define("ALLIANCE_FLEET_SEND_HOME", 3);
	define("ALLIANCE_FLEET_SHOW_PART", 4);
	define("ALLIANCE_FLEET_SEND_HOME_PART", 5);

  /****************/
  /* Sonstiges */
  /****************/

	// Advertising banner code
	define('ADD_BANNER', '');

	// Banner immer anzeigen
	define('FORCE_ADDS', 0);

	// Suffix that an administrators mail address must have to be shown in admin contact list (empty string disables this check)
	define('CONTACT_REQUIRED_MAIL_SUFFIX', "@etoa.ch");

	/***********
	* Updates *
	***********/

	define('LOG_UPDATES', false);
	define('LOG_UPDATES_THRESHOLD', 10);
	define('GAMESTATS_FILE', CACHE_ROOT."/out/gamestats.html");
	define('GAMESTATS_ROW_LIMIT', 15);
	define('USERSTATS_OUTFILE', CACHE_ROOT."/out/userstats.png");
	define('XML_INFO_FILE', CACHE_ROOT."/xml/info.xml");

	/***********
	* Userbanner *
	***********/

	define('USERBANNER_WIDTH', 468);
	define('USERBANNER_HEIGTH', 60);
	define('USERBANNER_BACKGROUND_IMAGE', RELATIVE_ROOT."images/userbanner/userbanner1.png");
	define('USERBANNER_FONT', RELATIVE_ROOT."images/userbanner/calibri.ttf");
	define('USERBANNER_DIR', CACHE_ROOT.'/userbanner');
