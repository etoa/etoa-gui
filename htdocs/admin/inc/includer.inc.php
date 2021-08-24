<?PHP

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

define('SESSION_NAME', "adminsession");

define('URL_SEARCH_STRING', "page=$page&amp;sub=$sub&amp;tmp=1");

define('ADMIN_FILESHARING_DIR', CACHE_ROOT . "/admin");
