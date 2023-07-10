<?PHP

use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\ErrorHandler\ErrorHandler;

define('ADMIN_MODE', true);
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../inc/bootstrap.inc.php';

if (isDebugEnabled()) {
    Debug::enable();
}
ErrorHandler::register();

