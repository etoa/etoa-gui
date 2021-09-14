<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

use EtoA\Core\ObjectWithImage;

class Star implements ObjectWithImage
{
    public int $id;
    public ?string $name;
    public int $typeId;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->name = $data['name'];
        $this->typeId = (int) $data['type_id'];
    }

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH."/stars/star".$this->typeId."_small.png";
            case 'medium':
                return self::BASE_PATH."/stars/star".$this->typeId."_middle.png";
            default:
                return self::BASE_PATH."/stars/star".$this->typeId.".png";
        }
    }
}
