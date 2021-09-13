<?PHP

use EtoA\Ranking\UserBannerService;
use EtoA\User\UserRepository;

echo "<h1>Spieler-Banner</h1>";

echo "<p>Banner werden jeweils beim Aktualisieren der Punkte neu generiert.</p>";

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var UserBannerService $userBannerService */
$userBannerService = $app[UserBannerService::class];

$userNicks = $userRepository->searchUserNicknames();
foreach ($userNicks as $userId => $userNick) {
    $name = $userBannerService->getUserBannerPath($userId);
    if (file_exists($name)) {
        echo '<img src="' . $name . '" alt="Banner" style="width:' . UserBannerService::BANNER_WIDTH . 'px;heigth:' . UserBannerService::BANNER_HEIGHT . 'px;" /> ';
    } else {
        echo '<div  style="display:inline-block; width:' . UserBannerService::BANNER_WIDTH . 'px;heigth:' . UserBannerService::BANNER_HEIGHT . 'px;">Banner für <b>' . $userNick . '</b> existiert nicht!</div> ';
    }
}
