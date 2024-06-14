#! /usr/bin/php -q
<?PHP

use EtoA\Support\Balancing\Exporter;

require_once __DIR__ . '/../vendor/autoload.php';

include_once __DIR__ . '/../htdocs/inc/init.inc.php';

/** @var \Silex\Application $app */
$app = require_once __DIR__ . '/../src/app.php';
$app->boot();

/** @var Exporter $exporter */
$exporter = $app[Exporter::class];
$exporter->export('local');
