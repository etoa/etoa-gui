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

/****************************/
/* Allgemeine Einstellungen */
/****************************/

// Homepage
define('DEFAULT_PAGE', "overview");

/****************/
/* Allianzboard */
/****************/

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
