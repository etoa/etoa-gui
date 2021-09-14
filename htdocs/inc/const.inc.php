<?php

define('APP_NAME', 'EtoA');

// Timezone
define('TIMEZONE', 'Europe/Zurich');

// Cache directory
if (!defined('CACHE_ROOT')) {
    define('CACHE_ROOT', RELATIVE_ROOT . 'cache');
}

// Admin mode?
if (!defined('ADMIN_MODE')) {
    define('ADMIN_MODE', false);
}

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

// Planeten Ordner
define("IMAGE_PLANET_DIR", "planets");

// Gebäude Ordner
define("IMAGE_BUILDING_DIR", "buildings");


/***********************************/
/* Installer */
/***********************************/

// Default login server URL
define('INSTALLER_DEFAULT_LOGINSERVER_URL', 'https://etoa.ch');

/***********************************/
/* Directory- and file paths       */
/***********************************/

// RSS Dir
define('RSS_DIR', CACHE_ROOT . "/rss");

//
// Pfade
//

// Bilder
define('IMAGE_PATH', RELATIVE_ROOT . "images/imagepacks/Discovery");


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

// Anzahl Rohstofftypen im Spiel
define('NUM_RESOURCES', 5);

/****************/
/* Allianzboard */
/****************/

// Verzeichnis der Forenicons
define("BOARD_BULLET_DIR", "images/boardbullets");

// Verzeichnis der Avatare
define("BOARD_AVATAR_DIR", CACHE_ROOT . "/avatars");

if (!defined('GD_VERSION')) {
    define("GD_VERSION", 2);
}

//
// Profilbild
//

// Verzeichnis der User-Profilbilder
define("PROFILE_IMG_DIR", CACHE_ROOT . "/userprofiles");

//
// Allianzbild
//

// Verzeichnis der Allianz-Bilder
define("ALLIANCE_IMG_DIR", CACHE_ROOT . "/allianceprofiles");

/****************/
/* Sonstiges */
/****************/

// Suffix that an administrators mail address must have to be shown in admin contact list (empty string disables this check)
define('CONTACT_REQUIRED_MAIL_SUFFIX', "@etoa.ch");

/***********
 * Updates *
 ***********/

define('USERSTATS_OUTFILE', CACHE_ROOT . "/out/userstats.png");
define('XML_INFO_FILE', CACHE_ROOT . "/xml/info.xml");

/***********
 * Userbanner *
 ***********/

define('USERBANNER_DIR', CACHE_ROOT . '/userbanner');
