<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\ErrorHandler\ErrorHandler;

define('RELATIVE_ROOT', '../');
define('ADMIN_MODE', true);
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../inc/bootstrap.inc.php';

if (isDebugEnabled()) {
    Debug::enable();
}
ErrorHandler::register();

// Renderzeit-Start festlegen
$render_time = explode(" ", microtime());
$render_starttime = (int) $render_time[1] + (int) $render_time[0];

define('IMAGE_PATH', "../images/imagepacks/Discovery");
define('IMAGE_EXT', "png");

// Feste Konstanten

define('SESSION_NAME', "adminsession");
define('USER_TABLE_NAME', 'admin_users');

define('URL_SEARCH_STRING', "page=$page&amp;sub=$sub&amp;tmp=1");
define('URL_SEARCH_STRING2', "page=$page");
define('URL_SEARCH_STRING3', "page=$page");

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

define('DATE_FORMAT', $config->get('admin_dateformat'));

define('USER_BLOCKED_DEFAULT_TIME', 3600 * 24 * $config->get('user_ban_min_length'));    // Standardsperrzeit
define('USER_HMODE_DEFAULT_TIME', 3600 * 24 * $config->get('user_umod_min_length'));    // Standardurlaubszeit

define('ADMIN_FILESHARING_DIR', CACHE_ROOT . "/admin");

$css_theme = (!isset($themePath) || !is_file("web/css/themes/" . $themePath . "css")) ? "default" : $themePath;
