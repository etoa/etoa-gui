<?PHP

use EtoA\Core\Configuration\ConfigurationService;

require_once __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/init.inc.php';

// Connect to database
dbconnect();

if (!isset($app)) {
    $app = require __DIR__ . '/../../src/app.php';
    $app->boot();
}

// Load default values
require_once __DIR__ . '/def.inc.php';

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

// Init session
if (ADMIN_MODE) {
    $s = AdminSession::getInstance($config);
} else {
    $s = UserSession::getInstance($config);
}

$twig = $app['twig'];

// Set default page / action variables
$page = (isset($_GET['page']) && $_GET['page'] != "") ? $_GET['page'] : DEFAULT_PAGE;
$mode = (isset($_GET['mode']) && $_GET['mode'] != "") ? $_GET['mode'] : "";
$sub = isset($_GET['sub']) ? $_GET['sub'] : null;
$index = isset($_GET['index']) ? $_GET['index'] : null;
$info = isset($_GET['info']) ? $_GET['info'] : null;
$mode = isset($_GET['mode']) ? $_GET['mode'] : null;

// Initialize XAJAX and load functions
if (!isCLI() && (!defined('SKIP_XAJAX_INIT') || !SKIP_XAJAX_INIT)) {
    if (ADMIN_MODE) {
        require_once dirname(__DIR__) . '/admin/inc/xajax_admin.inc.php';
    } else {
        require_once dirname(__DIR__) . '/inc/xajax.inc.php';
    }
}

// Set popup identifiert to false
$popup = false;
