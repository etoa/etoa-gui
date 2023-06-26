<?php

namespace EtoA\Image;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Market\MarketRateRepository;
use EtoA\Universe\Resources\ResourceNames;

class MarketImageGenerator
{
    public function __construct(
        private readonly ConfigurationService $config,
        private readonly MarketRateRepository $marketRateRepository,
        private readonly string               $projectDir,
    )
    {
    }

    public function create(int $width = 600, int $height = 400): void
    {
        $im = imagecreatetruecolor($width, $height);

        $colBlack = imagecolorallocate($im, 0, 0, 0);
        $colBg = imagecolorallocate($im, 255, 255, 255);

        $rCol = [];
        $rCol[0] = imagecolorallocate($im, 157, 100, 94);
        $rCol[1] = imagecolorallocate($im, 94, 126, 139);
        $rCol[2] = imagecolorallocate($im, 129, 109, 139);
        $rCol[3] = imagecolorallocate($im, 064, 93, 139);
        $rCol[4] = imagecolorallocate($im, 94, 136, 94);

        $graphtx = 30;
        $graphty = 20;
        $graphbx = $width - 25;
        $graphby = $height - 50;
        $graphw = $graphbx - $graphtx;
        $graphh = $graphby - $graphty;

        imagefilledrectangle($im, 0, 0, $width, $height, $colBg);
        $imh = imagecreatefromjpeg($this->projectDir . "/assets/images/logo_trans.jpg");
        $bgfh = 0.5;
        $bgfw = 0.8;
        imagecopyresized($im, $imh, (int)(($width - ($width * $bgfw)) / 2), (int)(($height - ($height * $bgfh)) / 2), 0, 0, (int)($width * $bgfw), (int)($height * $bgfh), imagesx($imh), imagesy($imh));

        $marketRates = $this->marketRateRepository->getRates($this->config->getInt('market_rates_count'));
        $nr = count($marketRates);
        if ($nr > 0) {
            $ts1 = null;
            $ts2 = null;
            $grates = array();
            $drate = 0;

            foreach ($marketRates as $marketRate) {
                $rates = [
                    $marketRate->rate->metal,
                    $marketRate->rate->crystal,
                    $marketRate->rate->plastic,
                    $marketRate->rate->fuel,
                    $marketRate->rate->food,
                    $marketRate->rate->people,
                ];
                $drate = max(max($rates), $drate);
                $grates[] = $rates;
                if ($ts1 === null) {
                    $ts1 = $marketRate->timestamp;
                }

                $ts2 = $marketRate->timestamp;
            }
            $grates = array_reverse($grates);
            $drate *= 1.2;

            // X-Axis
//            for ($i = 0; $i < $nr; $i++) {
//                imageline($im,$graphtx+($graphw/$nr*$i),$graphty,$graphtx+($graphw/$nr*$i),$graphby,$colLLGrey);
//            }

            // Y-Axis
            for ($i = 0; $i <= $drate; $i++) {
                imagestring($im, 1, $graphtx - 15, (int)($graphby - ($graphh / $drate * $i) - 3), (string)$i, $colBlack);
                //imageline($im,$graphtx,$graphby-($graphh/$drate*$i),$graphbx,$graphby-($graphh/$drate*$i),$colLLGrey);
            }

            $numberOfResources = count(ResourceNames::NAMES);
            $j = 0;
            $lastx = array_fill(0, $numberOfResources, 0);
            $lasty = array_fill(0, $numberOfResources, 0);
            foreach ($grates as $rates) {
                foreach (array_keys(ResourceNames::NAMES) as $i) {
                    $x = $graphtx + ($graphw / $nr * $j);
                    $y = $graphty + ($graphh - ($graphh / $drate * $rates[$i]));

                    if ($lastx[$i] == 0) {
                        $lastx[$i] = $x;
                        $lasty[$i] = $y;
                    } else {
                        imageline($im, $lastx[$i] + 1, $lasty[$i] + 1, $x + 1, (int)$y + 1, $rCol[$i]);
                        imageline($im, $lastx[$i] + 1, $lasty[$i], $x + 1, (int)$y, $rCol[$i]);
                        imageline($im, $lastx[$i], $lasty[$i] + 1, $x, (int)$y + 1, $rCol[$i]);
                        imageline($im, $lastx[$i], $lasty[$i], $x, (int)$y, $rCol[$i]);
                        $lastx[$i] = $x;
                        $lasty[$i] = $y;
                    }
                }
                $j++;
            }

            foreach (ResourceNames::NAMES as $i => $resourceName) {
                imagestring($im, 1, $lastx[$i] + 5, $lasty[$i] - 3, (string)round($rates[$i], 2), $rCol[$i]);

                imagestring($im, 5, $graphtx + 20 + ($graphw / $numberOfResources * $i), $graphby + 25, $resourceName, $rCol[$i]);
            }

            // Timestampss
            imagestring($im, 1, $graphtx + 10, $graphby + 10, date("d.m.Y, H:i", $ts2), $colBlack);
            imagestring($im, 1, $graphbx - 80, $graphby + 10, date("d.m.Y, H:i", $ts1), $colBlack);
        } else {
            imagestring($im, 3, $graphtx + 10, $graphby - 20, "Kein Kursdaten vorhanden!", $colBlack);
        }

        ImageUtil::imageArrow($im, $graphtx, $graphby, $graphtx, $graphty, $colBlack);
        //ImageFilledPolygon($im, array($graphtx,$graphty,$graphtx-5,$graphty+10,$graphtx+5,$graphty+10), 3, $colBlack);

        ImageUtil::imageArrow($im, $graphtx, $graphby, $graphbx, $graphby, $colBlack);
        //ImageFilledPolygon($im, array($graphbx,$graphby,$graphbx-10,$graphby-5,$graphbx-10,$graphby+5), 3, $colBlack);

        imagestring($im, 3, $graphbx + 10, $graphby - 6, "t", $colBlack);
        imagestring($im, 3, $graphtx - 10, $graphty - 15, "Kurs", $colBlack);

        imagepng($im);
    }
}