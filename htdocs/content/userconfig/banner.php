<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ranking\UserBannerService;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var UserBannerService $userBannerService */
$userBannerService = $app[UserBannerService::class];

$id = $cu->id;

iBoxStart("Banner");
echo 'Hilf mit, EtoA bekannter zu machen und binde unser Banner auf deiner Website ein!
Hier findest du den Quellcode um das Banner einzubinden:<br><br>';

$name = $userBannerService->getUserBannerPath($id);
if (file_exists($name)) {
    echo '<div style="text-align: center;">
    <img src="' . $name . '" alt="Banner"><br><br>
    HTML:<br/><textarea readonly="readonly" rows="2" cols="65">&lt;a href="' . USERBANNER_LINK_URL . '"&gt;&lt;img src="' . $config->get('roundurl') . '/' . $name . '" width="' . UserBannerService::BANNER_WIDTH . '" height="' . UserBannerService::BANNER_HEIGHT . '" alt="EtoA Online-Game" border="0" /&gt;&lt;/a&gt;</textarea><br/>
    BBCode:<br/><textarea readonly="readonly" rows="1" cols="65">[url=' . USERBANNER_LINK_URL . '][img]' . $config->get('roundurl') . '/' . $name . '[/img][/url]</textarea>';
} else {
    echo "Momentan ist kein Banner verfügbar!";
}
iBoxEnd();
