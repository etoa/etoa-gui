<?PHP

use EtoA\Core\Configuration\ConfigurationService;

require_once __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/init.inc.php';

if (!isset($app)) {
    global $app;
    $app = require __DIR__ . '/../../src/app.php';
    $app->boot();
}

// Load default values
require_once __DIR__ . '/def.inc.php';

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

// Init session
if (!ADMIN_MODE) {
    $s = UserSession::getInstance($config);
}

$twig = $app['twig'];

// Set default page / action variables
global $page, $mode, $sub, $index, $info;
$page = (isset($_GET['page']) && $_GET['page'] != "") ? $_GET['page'] : 'overview';
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
