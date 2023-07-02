<?php

namespace EtoA\Image;

use EtoA\Support\StringUtils;
use EtoA\User\UserPointsRepository;
use EtoA\User\UserRepository;

class StatsImageGenerator
{
    const DETAIL_LIMIT = 48;
    const STEP = 6;
    const PADDING = 25;
    const SHADOW_L = 5;
    const FONT_SIZE = 1;
    const BG_FAC_W = 5 / 6;
    const BG_FAC_H = 0.41;

    public function __construct(
        private readonly UserRepository       $userRepository,
        private readonly UserPointsRepository $userPointsRepository,
        private readonly string               $projectDir,
    )
    {
    }

    public function create(int $userId, int $width = 600, ?int $start = null, ?int $end = null): void
    {
        $height = $width / 3 * 2;
        $im = imagecreate($width, $height);

        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $grey = imagecolorallocate($im, 150, 150, 150);
        $lightBlue = imagecolorallocate($im, 34, 34, 200);

        $usableHeight = $height - (2 * self::PADDING);

        imagefill($im, 0, 0, $white);
        $imh = imagecreatefromjpeg($this->projectDir . "/assets/images/logo_trans.jpg");
        imagecopyresized($im, $imh, (int)($width - ($width * self::BG_FAC_W)) / 2, (int)($height - ($height * self::BG_FAC_H)) / 2, 0, 0, (int)($width * self::BG_FAC_W), (int)($height * self::BG_FAC_H), imagesx($imh), imagesy($imh));
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $black);

        if ($userId > 0) {
            $user = $this->userRepository->getUser($userId);
            if ($user !== null) {
                $pointsEntries = $this->userPointsRepository->getPoints($userId, self::DETAIL_LIMIT * 6, $start, $end);

                if (count($pointsEntries) > 0) {
                    $records_per_step = floor(count($pointsEntries) / self::STEP);

                    define('B_W', ($width - self::PADDING) / max($records_per_step, 1) / 2);
                    // Bar colors
                    $b_col = [];
                    for ($x = 0; $x < B_W; $x++) {
                        $b_col[$x] = imagecolorallocate($im, 34 / B_W * $x, 34 / B_W * $x, 85 / B_W * $x);
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
                    foreach ($pointsEntries as $entry) {
                        if ($last_update == 0) $last_update = $entry->timestamp;
                        if ($cnt == 0) {
                            $points[$entry->timestamp] = $entry->points;
                            $pmax = max($pmax, $entry->points);
                        }
                        $cnt++;
                        if ($cnt == self::STEP) $cnt = 0;
                    }
                    ksort($points);

                    imagestring($im, self::FONT_SIZE, (int)(self::PADDING / 3), (int)(self::PADDING / 3), "Statistiken von " . $user->nick . ", Rang " . $user->rank . ", letztes Update: " . date("d.m.Y H:i", $last_update), $black);
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
                            imagestring($im, self::FONT_SIZE, $left - 8 + (B_W / 2) - imagefontwidth(1) * 5 / 2, self::PADDING + $usableHeight + 13, date("d.m.y", $t), $black);
                        }
                        // Punkte
                        imagestringup($im, self::FONT_SIZE, $left + (B_W / 2) - imagefontheight(1), $usableHeight + self::PADDING - 10, StringUtils::formatNumber($p), $black);

                        $cnt++;
                    }
                } else {
                    imagestring($im, 3, 10, 10, "Keine Punktdaten vorhanden!", $black);
                }
            } else {
                imagestring($im, 3, 10, 10, "Fehler! Benutzer nicht vorhanden!", $black);
            }
        } else {
            imagestring($im, 3, 10, 10, "Fehler! Keine ID angegeben!", $black);
        }
        imagepng($im);
    }
}