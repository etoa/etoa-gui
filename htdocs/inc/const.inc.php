<?php

define('APP_NAME', 'EtoA');

// Timezone
define('TIMEZONE', 'Europe/Zurich');

/***********
 * Database *
 ***********/

define("WINDOWS_MYSQL_PATH", "c:\\xampp\\mysql\\bin\\mysql.exe");
define("WINDOWS_MYSQLDUMP_PATH", "c:\\xampp\\mysql\\bin\\mysqldump.exe");

// Cache directory
if (!defined('CACHE_ROOT')) {
    define('CACHE_ROOT', RELATIVE_ROOT . 'cache');
}

// Log directory
if (!defined('LOG_DIR')) {
    define('LOG_DIR', RELATIVE_ROOT . 'log');
}

// Class directory
if (!defined('CLASS_ROOT')) {
    define('CLASS_ROOT', RELATIVE_ROOT . 'classes');
}

// Data file directory
if (!defined('DATA_DIR')) {
    define('DATA_DIR', RELATIVE_ROOT . "data");
}

// Image directory
if (!defined('IMAGE_DIR')) {
    define('IMAGE_DIR', RELATIVE_ROOT . "images");
}

// Admin mode?
if (!defined('ADMIN_MODE')) {
    define('ADMIN_MODE', false);
}

define('ERROR_LOGFILE', LOG_DIR . "/errors.log");

// Entwickler Link
define("DEVCENTER_PATH", "http://dev.etoa.ch");

// Entwickler Link (popup)
define("DEVCENTER_ONCLICK", "window.open('" . DEVCENTER_PATH . "','dev','width=1024,height=768,scrollbars=yes');");

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
define('HELPCENTER_ONCLICK', "window.open('" . HELPCENTER_URL . "','helpcenter','width=1024,height=700,scrollbars=yes');");

// Forum Link
define("FORUM_URL", "http://forum.etoa.ch");

// Chat
define('CHAT_URL', "chatframe.php");
define('CHAT_ONCLICK', "parent.top.location='chatframe.php';");

// Teamspeak
define('TEAMSPEAK_URL', "https://discord.gg/7d2ndEU");
define('TEAMSPEAK_ONCLICK', "window.open('" . TEAMSPEAK_URL . "','_blank');");

// Game-Rules
define('RULES_URL', 'http://www.etoa.ch/regeln');
define('RULES_ONCLICK', "window.open('" . RULES_URL . "','rules','width=auto,height=auto,scrollbars=yes');");

// Privacy statement
define('PRIVACY_URL', 'http://www.etoa.ch/privacy');

// URL for user banner HTML snippet
define('USERBANNER_LINK_URL', 'http://www.etoa.ch');

/***********************************/
/* Directory- and file paths       */
/***********************************/

// RSS Dir
define('RSS_DIR', CACHE_ROOT . "/rss");

//
// Pfade
//

// Smilies
define("SMILIE_DIR", IMAGE_DIR . "/smilies");

// Bilder
define("IMAGEPACK_DIRECTORY", IMAGE_DIR . "/imagepacks");
define('IMAGE_PATH', RELATIVE_ROOT . "images/imagepacks/Discovery");
define('IMAGE_EXT', "png");

/*********************/
/* Zufallsereignisse */
/*********************/

define("RANDOM_EVENTS_PER_UPDATE", 1);

/****************************/
/* Allgemeine Einstellungen */
/****************************/

// Homepage
define('DEFAULT_PAGE', "overview");

// Regular expressions
define('REGEXP_NAME', '/^.[^0-9\'\"\?\<\>\$\!\=\;\&]*$/');
define('REGEXP_NICK', '/^.[^\'\"\?\<\>\$\!\=\;\&]*$/');

// Minimale Sperrzeit für Kolonielöschung
define("COLONY_DELETE_THRESHOLD", 24 * 3600 * 5);

// Galaxy map
define('GALAXY_MAP_DOT_RADIUS', 3);
define('GALAXY_MAP_WIDTH', 500);
define('GALAXY_MAP_LEGEND_HEIGHT', 40);

// Tipps beim Start aktivieren
define("ENABLE_TIPS", 1);

// Permissions for uploaded files
define('FILE_UPLOAD_PERMS', 0644);

// Zeitverzögerung zwischen zwei Bauaufträgen in der Warteschlange
define("BUILDING_QUEUE_DELAY", 60);

/****************/
/* Technologien */
/****************/

//
// Ab diesem Level Sieht man von gegnerischen flotten diese infos...
//

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

// Anzahl Flotten die OHNE Flottenkontrolle fliegen können
define("FLEET_NOCONTROL_NUM", 1);

// Anzahl Rohstofftypen im Spiel
define('NUM_RESOURCES', 5);

/****************/
/* Allianzboard */
/****************/

// Verzeichnis der Forenicons
define("BOARD_BULLET_DIR", "images/boardbullets");

// Verzeichnis der Avatare
define("BOARD_AVATAR_DIR", CACHE_ROOT . "/avatars");

// Standard Foren-Icon
define("BOARD_DEFAULT_IMAGE", "default.png");
define("BOARD_ADMIN_RANK", 4);

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
    define("GD_VERSION", 2);
}

//
// Profilbild
//

// Verzeichnis der User-Profilbilder
define("PROFILE_IMG_DIR", CACHE_ROOT . "/userprofiles");

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
define("ALLIANCE_IMG_DIR", CACHE_ROOT . "/allianceprofiles");

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
/* Sonstiges */
/****************/

// Suffix that an administrators mail address must have to be shown in admin contact list (empty string disables this check)
define('CONTACT_REQUIRED_MAIL_SUFFIX', "@etoa.ch");

/***********
 * Updates *
 ***********/

define('LOG_UPDATES', false);
define('LOG_UPDATES_THRESHOLD', 10);
define('USERSTATS_OUTFILE', CACHE_ROOT . "/out/userstats.png");
define('XML_INFO_FILE', CACHE_ROOT . "/xml/info.xml");

/***********
 * Userbanner *
 ***********/

define('USERBANNER_WIDTH', 468);
define('USERBANNER_HEIGTH', 60);
define('USERBANNER_DIR', CACHE_ROOT . '/userbanner');
