<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Planet\PlanetRepository;

class UserStats
{
    private UserOnlineStatsRepository $userOnlineStatsRepository;
    private PlanetRepository $planetRepository;
    private ConfigurationService $config;

    public function __construct(UserOnlineStatsRepository $userOnlineStatsRepository, PlanetRepository $planetRepository, ConfigurationService $config)
    {
        $this->userOnlineStatsRepository = $userOnlineStatsRepository;
        $this->planetRepository = $planetRepository;
        $this->config = $config;
    }

    public function generateImage(string $file): void
    {
        $w = 700;
        $h = 400;
        $borderLeftRight = 50;
        $borderTop = 70;
        $borderBottom = 80;
        $yLegend = 20;
        $bottomLegend = 65;

        $totalSteps = 288;

        $im = imagecreate($w, $h);
        $imh = imagecreatefromjpeg(__DIR__ . '/../../htdocs/images/logo_trans.jpg');
        imagecopyresized($im, $imh, (int) (($w - imagesx($imh)) / 2), (int) (($h - imagesy($imh)) / 2), 0, 0, imagesx($imh), imagesy($imh), imagesx($imh), imagesy($imh));

        $colWhite = imagecolorallocate($im, 255, 255, 255);
        $colBlack = imagecolorallocate($im, 0, 0, 0);
        $colGrey = imagecolorallocate($im, 180, 180, 180);
        $colBlue = imagecolorallocate($im, 0, 0, 255);
        $colGreen = imagecolorallocate($im, 0, 200, 0);
        $colRed = imagecolorallocate($im, 255, 0, 0);

        imagerectangle($im, 0, 0, $w - 1, $h - 1, $colBlack);
        imagerectangle($im, $borderLeftRight, $borderTop, $w - $borderLeftRight, $h - $borderBottom, $colBlack);

        $time = time() - (int) date("s");

        // Renderzeit-Start festlegen
        $render_time = explode(" ", microtime());
        $render_starttime = (int) $render_time[1] + (int) $render_time[0];

        $data = array();
        $max = 0;
        $maxo = 0;
        $acto = false;
        $actr = false;
        $index0 = 0;
        $userOnlineStats = $this->userOnlineStatsRepository->getEntries($totalSteps + 1);
        $mnr = count($userOnlineStats);
        $sumo = $sumr = 0;
        if ($mnr > 0) {
            foreach ($userOnlineStats as $stats) {
                $t = $stats->timestamp;
                $data[$t]['o'] = $stats->sessionCount;
                $data[$t]['r'] = $stats->userCount;
                $max = max($max, $stats->userCount);
                $maxo = max($maxo, $stats->sessionCount);
                if ($acto == false) {
                    $acto = $stats->sessionCount;
                }
                if ($actr == false) {
                    $actr = $stats->userCount;
                }
                $sumo += $stats->sessionCount;
                $sumr += $stats->userCount;
                $index0 = $stats->timestamp;
            }
            $avgo = round($sumo / $mnr, 2);
            $avgr = round($sumr / $mnr, 2);

            $maxr = $max;
            $max = ceil($max / 100) * 100;

            ksort($data);

            $graphHeight = $h - $borderTop - $borderBottom;
            $starti = $time - ($totalSteps * 5 * 60);

            // Horizontale Linien und Gr?ssen
            if ($max > 0) {
                for ($i = 0; $i <= ceil($max / 100); $i++) {
                    $y = (int) ($h - $borderBottom - ($graphHeight / ($max / 100) * $i));
                    imagestring($im, 2, $yLegend, (int) ($y - (imagefontheight(2) / 2)), (string) ($i * 100), $colBlack);
                    if ($i != 0) {
                        imageline($im, $borderLeftRight + 1, $y, $w - $borderLeftRight - 1, $y, $colGrey);
                    }
                }
            }

            $step = ($w - $borderLeftRight - $borderLeftRight) * 5 * 60 / ($time - $starti);

            $x = $borderLeftRight;
            $y = $h - $borderBottom;
            $lastx = $borderLeftRight;
            $lastyo = $h - $borderBottom; // - ($graphHeight/$max*$data[$index0]['o']);
            $lastyr = $h - $borderBottom; // - ($graphHeight/$max*$data[$index0]['r']);;

            $ic = 0;
            foreach ($data as $i => $d) {
                $x = (int) ($borderLeftRight + ($ic * $step));
                // Vertikale Stundenlinien
                if (date("i", $i) == "00") {
                    if (date("H", $i) == "00") {
                        imageline($im, $x, $borderTop + 1, $x, $h - $borderBottom - 1, $colRed);
                    } else {
                        imageline($im, $x, $borderTop + 1, $x, $h - $borderBottom - 1, $colGrey);
                    }
                    imagestring($im, 2, $x - (int) ((imagefontheight(2) / 2)), $h - $bottomLegend, date("H", $i), $colBlack);
                }
                $t = date("dmyHi", $i);
                if ($max > 0) {
                    $yo = $h - $borderBottom - ($graphHeight / $max * $d['o']);
                    $yr = $h - $borderBottom - ($graphHeight / $max * $d['r']);
                } else {
                    $yo = $h - $borderBottom;
                    $yr = $h - $borderBottom;
                }
                imageline($im, $lastx, (int) $lastyo, $x, (int) $yo, $colGreen);
                imageline($im, $lastx, (int) $lastyr, $x, (int) $yr, $colBlue);
                $lastyo = $yo;
                $lastyr = $yr;
                $lastx = $x;
                $ic++;
            }

            // Renderzeit
            $render_time = explode(" ", microtime());
            $rtime = (int) $render_time[1] + (int) $render_time[0] - $render_starttime;
            imagestring($im, 6, 10, 5, getGameIdentifier($this->config), $colBlack);
            imagestring($im, 6, 10, 20, "Userstatistik der letzten 24 Stunden", $colBlack);
            imagestring($im, 2, 10, 40, "Erstellt: " . date("d.m.Y, H:i") . ", Renderzeit: " . round($rtime, 3) . " sec", $colBlack);

            imagestring($im, 3, 110, $h - 40, "Max    Durchschnitt   Aktuell", $colBlack);
            imagestring($im, 3, 50, $h - 25, "Online", $colGreen);
            imagestring($im, 2, 110, $h - 25, (string) $maxo, $colBlack);
            imagestring($im, 2, 160, $h - 25, (string) $avgo, $colBlack);
            imagestring($im, 2, 265, $h - 25, (string) $acto, $colBlack);

            imagestring($im, 3, 450, $h - 40, "Max    Durchschnitt   Aktuell", $colBlack);
            imagestring($im, 3, 350, $h - 25, "Registriert", $colBlue);
            imagestring($im, 2, 450, $h - 25, (string) $maxr, $colBlack);
            imagestring($im, 2, 500, $h - 25, (string) $avgr, $colBlack);
            imagestring($im, 2, 605, $h - 25, (string) $actr, $colBlack);

            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            if (is_file($file)) {
                unlink($file);
            }
            imagepng($im, $file);
        }
    }

    public function generateXml(string $file): void
    {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $stats = $this->userOnlineStatsRepository->getEntries(1);
        $mnr = count($stats);
        $acto = 0;
        $actr = 0;
        if ($mnr > 0) {
            $acto = $stats[0]->sessionCount;
            $actr = $stats[0]->userCount;
        }

        $text = "<gameserver>
            <users>
                <online>" . $acto . "</online>
                <registered>" . $actr . "</registered>
            </users>
            <galaxy>
                <planets>
                    <inhabited>" . $this->planetRepository->countWithUser() . "</inhabited>
                    <total>" . $this->planetRepository->count() . "</total>
                </planets>
            </galaxy>
        </gameserver>";

        file_put_contents($file, $text);
    }
}
