<?PHP

use EtoA\User\UserToXml;

chdir(realpath(dirname(__FILE__) . "/../../"));
define('ADMIN_MODE', true);
require_once("inc/bootstrap.inc.php");

/** @var UserToXml $userToXml */
$userToXml = $app[UserToXml::class];

if (isset($_SESSION['adminsession'])) {
    if ($_GET['id'] > 0) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        //header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename="user' . $_GET['id'] . '.xml"');
        echo $userToXml->generate((int) $_GET['id']);
    } else {
        echo "User-ID nicht vorhanden!";
    }
} else {
    echo "Nicht eingeloggt!";
}
