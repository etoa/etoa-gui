<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ranking\RankingService;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var RankingService $rankingService */
$rankingService = $app[RankingService::class];

$id = $cu->id;

iBoxStart("Banner");
echo 'Hilf mit, EtoA bekannter zu machen und binde unser Banner auf deiner Website ein!
Hier findest du den Quellcode um das Banner einzubinden:<br><br>';

$name = $rankingService->getUserBannerPath($id);
if (file_exists($name)) {
    echo '<div style="text-align: center;">
    <img src="' . $name . '" alt="Banner"><br><br>
    HTML:<br/><textarea readonly="readonly" rows="2" cols="65">&lt;a href="' . USERBANNER_LINK_URL . '"&gt;&lt;img src="' . $config->get('roundurl') . '/' . $name . '" width="' . USERBANNER_WIDTH . '" height="' . USERBANNER_HEIGTH . '" alt="EtoA Online-Game" border="0" /&gt;&lt;/a&gt;</textarea><br/>
    BBCode:<br/><textarea readonly="readonly" rows="1" cols="65">[url=' . USERBANNER_LINK_URL . '][img]' . $config->get('roundurl') . '/' . $name . '[/img][/url]</textarea>';
} else {
    echo "Momentan ist kein Banner verfügbar!";
}
iBoxEnd();
