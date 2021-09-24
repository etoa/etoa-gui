<?PHP

use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\Support\StringUtils;

\EtoA\Admin\LegacyTemplateTitleHelper::$title = 'Datenbank';

//
// Backups anzeigen
//
if ($sub === 'cleanup') {
    require("db/cleanup.inc.php");
}
