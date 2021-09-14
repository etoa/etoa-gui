<?php

// Admin mode?
if (!defined('ADMIN_MODE')) {
    define('ADMIN_MODE', false);
}

/****************/
/* Allianzboard */
/****************/

// Verzeichnis der Avatare
define("BOARD_AVATAR_DIR", RELATIVE_ROOT . 'cache' . "/avatars");

//
// Profilbild
//

// Verzeichnis der User-Profilbilder
define("PROFILE_IMG_DIR", RELATIVE_ROOT . 'cache' . "/userprofiles");

