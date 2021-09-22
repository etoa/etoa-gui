<?PHP

use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\Support\StringUtils;

$twig->addGlobal('title', 'Datenbank');

//
// Backups anzeigen
//
if ($sub === 'cleanup') {
    require("db/cleanup.inc.php");
}
