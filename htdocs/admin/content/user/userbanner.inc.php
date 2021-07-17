<?PHP

use EtoA\User\UserRepository;

echo "<h1>Spieler-Banner</h1>";

echo "<p>Banner werden jeweils beim Aktualisieren der Punkte neu generiert.</p>";

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
$userNicks = $userRepository->getUserNicknames();
foreach ($userNicks as $userId => $userNick) {
    $name = Ranking::getUserBannerPath($userId);
    if (file_exists($name)) {
        echo '<img src="' . $name . '" alt="Banner" style="width:' . USERBANNER_WIDTH . 'px;heigth:' . USERBANNER_HEIGTH . 'px;" /> ';
    } else {
        echo '<div  style="display:inline-block; width:' . USERBANNER_WIDTH . 'px;heigth:' . USERBANNER_HEIGTH . 'px;">Banner für <b>' . $userNick . '</b> existiert nicht!</div> ';
    }
}
