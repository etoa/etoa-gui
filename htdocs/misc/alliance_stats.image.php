<?PHP

use EtoA\Support\StringUtils;

include("image.inc.php");

define('DETAIL_LIMIT', 48);    // Maximale Anzahl Datensätze
define('STEP', 6);
define('IM_W', 600);    // Breite des Bildes
define('IM_H', IM_W / 3 * 2);    // Höhe des Bildes
define('B_B', 25);        // Randabstand
define('SHADOW_L', 5);    // Grösse des Schattens
define('FONT_SIZE', 1);    // Schriftgrösse
define('BG_FAC_W', 5 / 6);    // Schriftgrösse
define('BG_FAC_H', 0.41);    // Schriftgrösse

define('B_H', IM_H - (2 * B_B));

$im = imagecreate(IM_W, IM_H);

$bg = imagecolorallocate($im, 34, 34, 51);
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 187, 187, 187);
$black = imagecolorallocate($im, 0, 0, 0);
$grey = imagecolorallocate($im, 150, 150, 150);
$green = imagecolorallocate($im, 0, 200, 0);
$blue = imagecolorallocate($im, 34, 34, 85);
$red = imagecolorallocate($im, 255, 0, 0);
$yellow = imagecolorallocate($im, 255, 255, 0);
$lblue = imagecolorallocate($im, 34, 34, 200);

imagefill($im, 0, 0, $white);
$imh = imagecreatefromjpeg("images/logo_trans.jpg");
imagecopyresized($im, $imh, (int) (IM_W - (IM_W * BG_FAC_W)) / 2, (int) (IM_H - (IM_H * BG_FAC_H)) / 2, 0, 0, (int) (IM_W * BG_FAC_W), (int) (IM_H * BG_FAC_H), imagesx($imh), imagesy($imh));
imagerectangle($im, 0, 0, IM_W - 1, IM_H - 1, $black);

$aid = isset($_GET['alliance']) ? (int) $_GET['alliance'] : 0;
if ($aid > 0 && count($_SESSION) > 0) {
    /** @var \EtoA\Alliance\AllianceRepository $allianceRepository */
    $allianceRepository = $app[\EtoA\Alliance\AllianceRepository::class];
    $alliance = $allianceRepository->getAlliance($aid);
    if ($alliance !== null) {
        /** @var \EtoA\Alliance\AlliancePointsRepository $alliancePointsRepository */
        $alliancePointsRepository = $app[\EtoA\Alliance\AlliancePointsRepository::class];
        $start = (int) ($_GET['start'] ?? 0) > 0 ? (int) $_GET['start'] : null;
        $end = (int) ($_GET['end'] ?? 0) > 0 ? (int) $_GET['end'] : null;

        $pointEntries = $alliancePointsRepository->getPoints($alliance->id, DETAIL_LIMIT * 6, $start, $end);
        if (count($pointEntries) > 0) {
            if (floor(count($pointEntries) / STEP) > 0) {
                define('B_W', (IM_W - B_B) / floor(count($pointEntries) / STEP) / 2);
            } else {
                define('B_W', 0);
            }
            // Bar colors
            $b_col = [];
            for ($x = 0; $x < B_W; $x++) {
                $b_col[$x] = imagecolorallocate($im, 34 / B_W * $x, 34 / B_W * $x, 85 / B_W * $x);
            }
            // Shadow colors
            $s_col = [];
            for ($i = SHADOW_L; $i > 0; $i--) {
                $s_col[$i] = imagecolorallocate($im, 5 + ($i * 250 / SHADOW_L), 5 + ($i * 250 / SHADOW_L), 5 + ($i * 250 / SHADOW_L));
            }

            $pmax = 0;
            $last_update = 0;
            $cnt = 0;
            $points = [];
            foreach ($pointEntries as $entry) {
                if ($last_update === 0) $last_update = $entry->timestamp;
                if ($cnt === 0) {
                    $points[$entry->timestamp] = $entry->points;
                    $pmax = max($pmax, $entry->points);
                }
                $cnt++;
                if ($cnt == STEP) $cnt = 0;
            }
            ksort($points);

            imagestring($im, FONT_SIZE, (int) (B_B / 3), (int) (B_B / 3), "Statistiken von " . $alliance->nameWithTag . ", Rang " . $alliance->currentRank . ", letzes Update: " . date("d.m.Y H:i", $last_update) . "", $black);
            imagestring($im, FONT_SIZE, (int) (B_B / 3), (int) (B_B / 3 + 9), "Schrittweite: " . STEP . " Stunden, Zeitraum: " . (DETAIL_LIMIT * STEP / 24) . " Tage", $black);
            $cnt = 0;

            $last_x = -1;
            $last_y = -1;
            foreach ($points as $t => $p) {
                $left =  B_B - 15 + ($cnt * 2 * B_W);

                $x0 = $left + ($x / 2);
                $y0 = B_B + B_H - (B_H * $p / $pmax);

                if ($last_x == -1) {
                    $last_x = $x0;
                }
                if ($last_y == -1) {
                    $last_y = $y0;
                }

                imageline($im, $x0 + 1, $y0 + 2, $last_x + 1, $last_y + 2, $grey);
                imageline($im, $x0, $y0 + 2, $last_x, $last_y + 2, $grey);

                imageline($im, $x0, $y0, $last_x, $last_y, $lblue);
                imageline($im, $x0, $y0 + 1, $last_x, $last_y + 1, $lblue);

                imageline($im, $left + (B_W / 2), B_B + B_H - (B_H * $p / $pmax), $left + (B_W / 2), B_B + B_H, $grey);

                $last_x = $x0;
                $last_y = $y0;

                // Zeit
                if ($cnt % 3 == 1) {
                    imagestring($im, FONT_SIZE, $left + (B_W / 2) - imagefontwidth(1) * 5 / 2, B_B + B_H + 5, date("H:i", $t), $black);
                    imagestring($im, FONT_SIZE, $left - 8 + (B_W / 2) - imagefontwidth(1) * 5 / 2, B_B + B_H + 13, date("d.m.y", $t), $black);
                }

                // Punkte
                imagestringup($im, FONT_SIZE, $left + (B_W / 2) - imagefontheight(1), B_H + B_B - 10, StringUtils::formatNumber($p), $black);

                $cnt++;
            }
        } else
            imagestring($im, 3, 10, 10, "Keine Punktdaten (Punkte > 0) vorhanden!", $black);
    } else
        imagestring($im, 3, 10, 10, "Fehler! Allianz nicht vorhanden!", $black);
} else
    imagestring($im, 3, 10, 10, "Fehler! Keine ID angegeben oder du bist nicht eingeloggt!", $black);
imagepng($im);

DBManager::getInstance()->close();
