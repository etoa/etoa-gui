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

/****************************/
/* Allgemeine Einstellungen */
/****************************/

// Homepage
define('DEFAULT_PAGE', "overview");

// Minimale Sperrzeit für Kolonielöschung
define("COLONY_DELETE_THRESHOLD", 24 * 3600 * 5);

// Galaxy map
define('GALAXY_MAP_DOT_RADIUS', 3);
define('GALAXY_MAP_WIDTH', 500);
define('GALAXY_MAP_LEGEND_HEIGHT', 40);

/****************/
/* Allianzboard */
/****************/

// Verzeichnis der Forenicons
define("BOARD_BULLET_DIR", "images/boardbullets");

// Verzeichnis der Avatare
define("BOARD_AVATAR_DIR", CACHE_ROOT . "/avatars");

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

/***********
 * Updates *
 ***********/

define('USERSTATS_OUTFILE', CACHE_ROOT . "/out/userstats.png");
define('XML_INFO_FILE', CACHE_ROOT . "/xml/info.xml");

/***********
 * Userbanner *
 ***********/

define('USERBANNER_DIR', CACHE_ROOT . '/userbanner');
