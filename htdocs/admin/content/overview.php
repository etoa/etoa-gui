<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ranking\GameStatsGenerator;
use Symfony\Component\HttpFoundation\Request;

/** @var Request $request */

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var GameStatsGenerator $gameStatsGenerator */
$gameStatsGenerator = $app[GameStatsGenerator::class];

if ($sub == "stats") {
    require("home/stats.inc.php");
} else {
    indexView();
}

function indexView() {
    forward('/admin/overview');
}
