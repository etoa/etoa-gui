<?PHP

$loginUrl = Config::getInstance()->loginurl->v;
if ($loginUrl) {
    forward($loginUrl);
    return;
}

function getErrMsg($err) {
    switch ($err) {
        case "name":
            return "Du hast vergessen einen Namen oder ein Passwort einzugeben!";
        case "pass":
            return "Falsches Passwort oder falscher Benutzername!";
        case "ip":
            return "IP-Adresse-&Uuml;berpr&uuml;fungsfehler! Kein Login von diesem Computer m&ouml;glich, da schon eine andere IP mit diesem Account verbunden ist!";
        case "timeout":
            return "Das Timeout wurde erreicht und du wurdest automatisch ausgeloggt!";
        case "session":
            return "Session-Cookie-Fehler. &Uuml;berpr&uuml;fe ob dein Browser wirklich Sitzungscookies akzeptiert!";
        case "tomanywindows":
            return "Es wurden zu viele Fenster ge&ouml;ffnet oder aktualisiert, dies ist leider nicht erlaubt!";
        case "session2":
            return "Deine Session ist nicht mehr vorhanden! Sie wurde entweder gel&ouml;scht oder sie ist fehlerhaft. Dies kann passieren wenn du dich an einem anderen PC einloggst obwohl du noch mit diesem online warst!";
        case "nosession":
            return "Deine Session ist nicht mehr vorhanden! Sie wurde entweder gel&ouml;scht oder sie ist fehlerhaft. Dies kann passieren wenn du dich an einem anderen PC einloggst obwohl du noch mit diesem online warst!";
        case "verification":
            return "Falscher Grafikcode! Bitte gib den linksstehenden Code in der Grafik korrekt in das Feld darunter ein!
            Diese Massnahme ist leider n&ouml;tig um das Benutzen von automatisierten Programmen (Bots) zu erschweren.";
        case "logintimeout":
            return "Der Login-Schlüssel ist abgelaufen! Bitte logge dich neu ein!";
        case "sameloginkey":
            return "Der Login-Schlüssel wurde bereits verwendet! Bitte logge dich neu ein!";
        case "wrongloginkey":
            return "Falscher Login-Schlüssel! Ein Login ist nur von der offiziellen EtoA-Startseite aus möglich!";
        case "nologinkey":
            return "Kein Login-Schlüssel! Ein Login ist nur von der offiziellen EtoA-Startseite aus möglich!";
        case "general":
            return "Ein allgemeiner Fehler ist aufgetreten. Bitte den Entwickler kontaktieren!";
        default:
            return "Unbekannter Fehler (<b>".$err."</b>). Bitte den Entwickler kontaktieren!";
    }
}

$time = time();
$loginToken = sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $time).dechex($time);
$nickField = sha1('nick' . $loginToken . $time);
$passwordField = sha1('password' . $loginToken . $time);

$errorMessage = null;
if (isset($_GET['err'])) {
    $errorMessage = getErrMsg($_GET['err']);
}

echo $twig->render('external/login.html.twig', [
    'errorMessage' => $errorMessage,
    'loginToken' => $loginToken,
    'loginUrl' => $loginUrl,
    'roundName' => Config::getInstance()->roundname->v,
    'nickField' => $nickField,
    'passwordField' => $passwordField,
]);
