<?php

namespace EtoA\Image;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingTypeId;
use EtoA\Ship\ShipDataRepository;
use EtoA\Universe\Planet\Planet;

class PowerProductionImageGenerator
{
    const NUM_LEVELS = 25;
    const PADDING_LEFT = 20;
    const PADDING_RIGHT = 10;
    const PADDING_TOP = 10;
    const PADDING_BOTTOM = 20;
    const LEGEND_HEIGHT = 40;

    public function __construct(
        private readonly ConfigurationService   $config,
        private readonly BuildingDataRepository $buildingRepository,
        private readonly ShipDataRepository     $shipRepository,
    )
    {
    }

    public function create(int $width = 640, int $height = 400, $maxRatio = 30): void
    {
        $im = imagecreatetruecolor($width, $height);

        $colWhite = imagecolorallocate($im, 255, 255, 255);
        $colBlack = imagecolorallocate($im, 0, 0, 0);
        $colLGrey = imagecolorallocate($im, 230, 230, 230);

        $lineCol = [];
        $lineCol[0] = imagecolorallocate($im, 0, 255, 0);
        $lineCol[1] = imagecolorallocate($im, 11, 9, 159);
        $lineCol[2] = imagecolorallocate($im, 150, 0, 255);
        $lineCol[3] = imagecolorallocate($im, 255, 7, 255);
        $lineCol[4] = imagecolorallocate($im, 255, 150, 00);
        $lineCol[5] = imagecolorallocate($im, 255, 0, 00);
        $lineCol[6] = imagecolorallocate($im, 255, 200, 00);

        imagefilledrectangle($im, 0, 0, $width, $height, $colWhite);

        $areaW = $width - self::PADDING_LEFT - self::PADDING_RIGHT;
        $areaH = $height - self::PADDING_TOP - self::PADDING_BOTTOM - self::LEGEND_HEIGHT;
        $areaOriginX = self::PADDING_LEFT;
        $areaOriginY = $height - self::LEGEND_HEIGHT - self::PADDING_BOTTOM;
        $stepX = floor($areaW / (self::NUM_LEVELS));

        $cnt = 1;
        for ($i = $areaOriginX + $stepX; $i <= $areaOriginX + $areaW; $i += $stepX) {
            imageline($im, (int)$i, 10, (int)$i, $height - self::LEGEND_HEIGHT - 10, $colLGrey);
            imagestring($im, 2, (int)($i - imagefontwidth(2) * strlen((string)$cnt) / 2), $areaOriginY, (string)$cnt++, $colBlack);
        }

        ob_start();

        for ($i = 0; $i <= $maxRatio; $i += $maxRatio / 10) {
            imagestring($im, 2, self::PADDING_LEFT - imagefontwidth(2) * strlen((string)$i) - 2, $areaOriginY - ($i / $maxRatio * $areaH), (string)$i, $colBlack);
        }

        $strx = self::PADDING_LEFT;
        $i = 0;

        $buildings = $this->buildingRepository->getBuildingsByType(BuildingTypeId::POWER);
        foreach ($buildings as $building) {
            $startX = $areaOriginX;
            $startY = $areaOriginY;
            for ($level = 0; $level <= self::NUM_LEVELS; $level++) {
                $costs1 = $building->costsMetal + $building->costsCrystal + $building->costsPlastic + $building->costsFuel + $building->costsFood;
                $prod1 = $building->prodPower;
                $costsLvl = round($costs1 * pow($building->buildCostsFactor, $level - 1));
                $prodLvl = round($prod1 * pow($building->productionFactor, $level - 1));
                $ratio = round($costsLvl / $prodLvl, 1);

                $newX = $areaOriginX + ($stepX * $level);
                $newY = $areaOriginY - (($ratio / $maxRatio) * $areaH);
                imageline($im, $startX, $startY, (int)$newX, (int)$newY, $lineCol[$i % 7]);
                $startX = $newX;
                $startY = $newY;
            }

            imagestring($im, 2, $strx, $height - self::LEGEND_HEIGHT, $building->name, $lineCol[$i % 7]);
            $strx += (imagefontwidth(2) * strlen($building->name)) + 10;
            $i++;
        }

        $ships = $this->shipRepository->getShipWithPowerProduction();

        $strx = self::PADDING_LEFT;

        foreach ($ships as $ship) {
            $costs1 = $ship->costsMetal + $ship->costsCrystal + $ship->costsPlastic + $ship->costsFuel + $ship->costsFood;
            $prod1 = $ship->powerProduction;
            $ratio = round($costs1 / $prod1, 1);
            imageline($im, $areaOriginX, (int)($areaOriginY - (($ratio / $maxRatio) * $areaH)), $areaOriginX + $areaW, (int)($areaOriginY - (($ratio / $maxRatio) * $areaH)), $lineCol[$i % 7]);

            $tpb1 = Planet::getSolarPowerBonus($this->config->param1Int('planet_temp'), $this->config->param1Int('planet_temp') + $this->config->getInt('planet_temp'));
            $ratio = round($costs1 / ($prod1 + $tpb1), 1);
            ImageUtil::dashedLine($im, $areaOriginX, $areaOriginY - (($ratio / $maxRatio) * $areaH), $areaOriginX + $areaW, $areaOriginY - (($ratio / $maxRatio) * $areaH), $lineCol[$i % 7], $colWhite);

            $tpb2 = Planet::getSolarPowerBonus($this->config->param2Int('planet_temp') - $this->config->getInt('planet_temp'), $this->config->param2Int('planet_temp'));
            $ratio = round($costs1 / ($prod1 + $tpb2), 1);
            ImageUtil::dashedLine($im, $areaOriginX, $areaOriginY - (($ratio / $maxRatio) * $areaH), $areaOriginX + $areaW, $areaOriginY - (($ratio / $maxRatio) * $areaH), $lineCol[$i % 7], $colWhite);

            imagestring($im, 2, $strx, $height - self::LEGEND_HEIGHT / 2, $ship->name, $lineCol[$i % 7]);
            $strx += (imagefontwidth(2) * strlen($ship->name . " ("));
            $i++;
        }

        $str = ob_get_clean();
        imagestring($im, 2, 10, 10, $str, $colBlack);

        $str = ob_get_clean();
        imagestring($im, 2, 10, 10, $str, $colBlack);

        imageline($im, self::PADDING_LEFT, $height - self::LEGEND_HEIGHT - self::PADDING_BOTTOM, $width - self::PADDING_RIGHT, $height - self::LEGEND_HEIGHT - self::PADDING_BOTTOM, $colBlack);
        imageline($im, self::PADDING_LEFT, self::PADDING_TOP, self::PADDING_LEFT, $height - self::LEGEND_HEIGHT - self::PADDING_BOTTOM, $colBlack);

        echo imagepng($im);
    }
}