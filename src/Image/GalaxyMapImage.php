<?php

namespace EtoA\Image;

use EtoA\Universe\GalaxyMap;

class GalaxyMapImage
{

    public $image;
    public readonly int $width;
    public readonly int $height;
    public readonly int $legendHeight;
    public readonly float $galaxyImageScale;

    public int $colorBlack;
    public int $colorGrey;
    public int $colorWhite;

    public function __construct(
        public readonly int $size,
        public readonly int $showLegend,
        public readonly int $numSectorsX,
        public readonly int $numSectorsY,
        public readonly int $numCellsX,
        public readonly int $numCellsY,
        public readonly int $maxNumPlanets,
    )
    {
        $this->legendHeight = $showLegend ? GalaxyMap::LEGEND_HEIGHT : 0;

        $this->galaxyImageScale = $size / ((($numSectorsX - 1) * 10) + $numCellsX);

        $this->width = $size;
        $this->height = $numSectorsY * $numCellsY * $this->galaxyImageScale + $this->legendHeight;

        $this->image = imagecreatetruecolor($this->width, $this->height);

        $this->colorBlack = imagecolorallocate($this->image, 0, 0, 0);
        $this->colorGrey = imagecolorallocate($this->image, 120, 120, 120);
        $this->colorWhite = imagecolorallocate($this->image, 255, 255, 255);
    }

}