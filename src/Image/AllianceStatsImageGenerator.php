<?php

namespace EtoA\Image;

use EtoA\Alliance\AlliancePointsRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Support\StringUtils;

class AllianceStatsImageGenerator
{
    const DETAIL_LIMIT = 48;
    const STEP = 6;
    const PADDING = 25;
    const SHADOW_L = 5;
    const FONT_SIZE = 1;
    const BG_FAC_W = 5 / 6;
    const BG_FAC_H = 0.41;

    public function __construct(
        private readonly AllianceRepository       $allianceRepository,
        private readonly AlliancePointsRepository $alliancePointsRepository,
        private readonly string                   $projectDir,
    )
    {
    }

    public function create(int $allianceId, int $width = 600): void
    {
        $height = $width / 3 * 2;

        $usableHeight = $height - (2 * self::PADDING);

        $im = imagecreate($width, $height);

        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $grey = imagecolorallocate($im, 150, 150, 150);
        $lightBlue = imagecolorallocate($im, 34, 34, 200);

        imagefill($im, 0, 0, $white);
        $imh = imagecreatefromjpeg($this->projectDir . "/assets/images/logo_trans.jpg");
        imagecopyresized($im, $imh, (int)($width - ($width * self::BG_FAC_W)) / 2, (int)($height - ($height * self::BG_FAC_H)) / 2, 0, 0, (int)($width * self::BG_FAC_W), (int)($height * self::BG_FAC_H), imagesx($imh), imagesy($imh));
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $black);

        if ($allianceId > 0) {
            $alliance = $this->allianceRepository->getAlliance($allianceId);
            if ($alliance !== null) {
                $start = (int)($_GET['start'] ?? 0) > 0 ? (int)$_GET['start'] : null;
                $end = (int)($_GET['end'] ?? 0) > 0 ? (int)$_GET['end'] : null;

                $pointEntries = $this->alliancePointsRepository->getPoints($alliance->id, self::DETAIL_LIMIT * 6, $start, $end);
                if (count($pointEntries) > 0) {
                    if (floor(count($pointEntries) / self::STEP) > 0) {
                        define('B_W', ($width - self::PADDING) / floor(count($pointEntries) / self::STEP) / 2);
                    } else {
                        define('B_W', 0);
                    }
                    // Bar colors
                    $b_col = [];
                    for ($x = 0; $x < B_W; $x++) {
                        $b_col[$x] = imagecolorallocate($im, (int)(34 / B_W * $x), (int)(34 / B_W * $x), (int)(85 / B_W * $x));
                    }
                    // Shadow colors
                    $s_col = [];
                    for ($i = self::SHADOW_L; $i > 0; $i--) {
                        $s_col[$i] = imagecolorallocate($im, 5 + ($i * 250 / self::SHADOW_L), 5 + ($i * 250 / self::SHADOW_L), 5 + ($i * 250 / self::SHADOW_L));
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
                        if ($cnt == self::STEP) $cnt = 0;
                    }
                    ksort($points);

                    imagestring($im, self::FONT_SIZE, (int)(self::PADDING / 3), (int)(self::PADDING / 3), "Statistiken von " . $alliance->nameWithTag . ", Rang " . $alliance->currentRank . ", letztes Update: " . date("d.m.Y H:i", $last_update), $black);
                    imagestring($im, self::FONT_SIZE, (int)(self::PADDING / 3), (int)(self::PADDING / 3 + 9), "Schrittweite: " . self::STEP . " Stunden, Zeitraum: " . (self::DETAIL_LIMIT * self::STEP / 24) . " Tage", $black);
                    $cnt = 0;

                    $last_x = -1;
                    $last_y = -1;
                    foreach ($points as $t => $p) {
                        $left = self::PADDING - 15 + ($cnt * 2 * B_W);

                        $x0 = $left + ($x / 2);
                        $y0 = self::PADDING + $usableHeight - ($usableHeight * $p / $pmax);

                        if ($last_x == -1) {
                            $last_x = $x0;
                        }
                        if ($last_y == -1) {
                            $last_y = $y0;
                        }

                        imageline($im, $x0 + 1, $y0 + 2, $last_x + 1, $last_y + 2, $grey);
                        imageline($im, $x0, $y0 + 2, $last_x, $last_y + 2, $grey);

                        imageline($im, $x0, $y0, $last_x, $last_y, $lightBlue);
                        imageline($im, $x0, $y0 + 1, $last_x, $last_y + 1, $lightBlue);

                        imageline($im, $left + (B_W / 2), self::PADDING + $usableHeight - ($usableHeight * $p / $pmax), $left + (B_W / 2), self::PADDING + $usableHeight, $grey);

                        $last_x = $x0;
                        $last_y = $y0;

                        // Zeit
                        if ($cnt % 3 == 1) {
                            imagestring($im, self::FONT_SIZE, $left + (B_W / 2) - imagefontwidth(1) * 5 / 2, self::PADDING + $usableHeight + 5, date("H:i", $t), $black);
                            imagestring($im, self::FONT_SIZE, $left - 8 + (B_W / 2) - imagefontwidth(1) * 5 / 2, self::PADDING + $usableHeight + 13, date("d.m.y", $t), $black);
                        }

                        // Punkte
                        imagestringup($im, self::FONT_SIZE, $left + (B_W / 2) - imagefontheight(1), $usableHeight + self::PADDING - 10, StringUtils::formatNumber($p), $black);

                        $cnt++;
                    }
                } else {
                    imagestring($im, 3, 10, 10, "Keine Punktdaten (Punkte > 0) vorhanden!", $black);
                }
            } else {
                imagestring($im, 3, 10, 10, "Fehler! Allianz nicht vorhanden!", $black);
            }
        } else {
            imagestring($im, 3, 10, 10, "Fehler! Keine ID angegeben oder du bist nicht eingeloggt!", $black);
        }
        imagepng($im);
    }
}