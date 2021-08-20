<?PHP

use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

$dir = PROFILE_IMG_DIR . "/";

echo "<h1>User-Bilder pr&uuml;fen</h1>";

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

//
// Check submit
//
if (isset($_POST['validate_submit'])) {
    foreach ($_POST['validate'] as $id => $v) {
        if ($v == 0) {
            $user = $userRepository->getUser($id);
            if ($user !== null) {
                if (file_exists(PROFILE_IMG_DIR . "/" . $user->profileImage)) {
                    unlink(PROFILE_IMG_DIR . "/" . $user->profileImage);
                }
                if ($userRepository->updateImgCheck($id, false, '')) {
                    echo "Bild entfernt!<br/><br/>";
                }
            }
        } else {
            $userRepository->updateImgCheck($id, false);
        }
    }
}

//
// Check new images
//
echo "<h2>Noch nicht verifizierte Bilder</h2>";
echo "Diese Bilder gehören zu aktiven Spielern. Bitte prüfe regelmässig, ob sie nicht gegen unsere Regeln verstossen!<br/>";
$users = $userRepository->searchUsers(UserSearch::create()->confirmedImageCheck());
if (count($users) > 0) {
    echo "Es sind " . count($users) . " Bilder gespeichert!<br/><br/>";
    echo "<form action=\"\" method=\"post\">
        <table class=\"tb\"><tr><th>User</th><th>Fehler</th><th>Aktionen</th></tr>";
    foreach ($users as $user) {
        echo "<tr><td>" . $user->nick . "</td><td>";
        if (file_exists($dir . $user->profileImage)) {
            echo '<img src="' . $dir . $user->profileImage . '" alt="Profil" />';
        } else {
            echo '<span style=\"color:red\">Bild existiert nicht!</span>';
        }
        echo "</td><td>
            <input type=\"radio\" name=\"validate[" . $user->id . "]\" value=\"1\" checked=\"checked\"> Bild ist in Ordnung<br/>
            <input type=\"radio\" name=\"validate[" . $user->id . "]\" value=\"0\" > Bild verstösst gegen die Regeln. Lösche es!<br/>
            </td></tr>";
    }
    echo "</table><br/>
        <input type=\"submit\" name=\"validate_submit\" value=\"Speichern\" /></form>";
} else {
    echo "<br/><i>Keine Bilder vorhanden!</i>";
}

//
// Orphans
//
$users = $userRepository->searchUsers(UserSearch::create()->withProfileImage());
$paths = [];
foreach ($users as $user) {
    $paths[$user->id] = $user->profileImage;

}
$files = array();
if (is_dir($dir)) {
    $d = opendir($dir);
    while ($f = readdir($d)) {
        if (is_file($dir . $f)) {
            array_push($files, $f);
        }
    }
    closedir($d);
}

$overhead = array();
while (count($files) > 0) {
    $k = array_pop($files);
    if (!in_array($k, $paths, true))
        array_push($overhead, $k);
}

if (isset($_GET['action']) && $_GET['action'] == "clearoverhead") {
    while (count($overhead) > 0) {
        unlink($dir . array_pop($overhead));
    }
    echo "Verwaiste Bilder gelöscht!<br/><bt/>";
}
$co = count($overhead);

echo "<h2>Verwaiste Bilder</h2>";
if ($co > 0) {
    echo "Diese Bilder gehören zu Spielern, die nicht mehr in unserer Datenbank vorhanden sind.<br/>
            Es sind $co Bilder vorhanden. <a href=\"?page=$page&amp;sub=$sub&amp;action=clearoverhead\">L&ouml;sche alle verwaisten Bilder</a><br/><br/>";
    echo "<table class=\"tb\">
            <tr><th>Datei</th><th>Bild</th></tr>";
    foreach ($overhead as $v) {
        echo "<tr><td>" . $v . "</td>";
        echo '<td><img src="' . $dir . $v . '" alt="Profil" /></td></tr>';
    }
    echo "</table><br/>";
} else {
    echo "<i>Keine vorhanden!</i>";
}
