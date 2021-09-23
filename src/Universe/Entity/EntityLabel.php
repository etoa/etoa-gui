<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

use EtoA\Core\ObjectWithImage;

class EntityLabel extends Entity
{
    public ?string $planetName;
    public ?string $starName;
    public ?int $ownerId;
    public ?string $ownerNick;
    public bool $ownerMain;
    public ?int $typeId = null;
    public ?string $image = null;
    private ?bool $wormholePersistent = null;
    public ?int $wormholeTarget = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->planetName = $data['planet_name'];
        $this->starName = $data['star_name'];
        $this->ownerId = (int) $data['user_id'];
        $this->ownerNick = $data['user_nick'];
        $this->ownerMain = (bool) $data['planet_user_main'];

        if ($data['planet_type'] !== null) {
            $this->typeId = (int) $data['planet_type'];
            $this->image = $data['planet_image'];
        } elseif ($data['star_type'] !== null) {
            $this->typeId = (int) $data['star_type'];
        } elseif ($data['wormhole_persistent'] !== null) {
            $this->wormholePersistent = (bool) $data['wormhole_persistent'];
            $this->wormholeTarget = (int) $data['wormhole_target'];
        }
    }

    public function displayName(): ?string
    {
        switch ($this->code) {
            case EntityType::PLANET:
                return (filled($this->planetName) ? $this->planetName : 'Unbenannt');
            case EntityType::STAR:
                return (filled($this->starName) ? $this->starName : 'Unbenannt');
            default:
                return null;
        }
    }

    public function toString(): string
    {
        switch ($this->code) {
            case EntityType::PLANET:
            case EntityType::STAR:
                return $this->coordinatesString() . ' ' . $this->displayName();
            case EntityType::ALLIANCE_MARKET:
            case EntityType::MARKET:
                return '';
            default:
                return $this->coordinatesString();
        }
    }

    public function getImagePath(): string
    {
        switch ($this->code) {
            case EntityType::ASTEROID:
                $r = ($this->id % 5) + 1;

                return ObjectWithImage::BASE_PATH . "/asteroids/asteroids" . $r . "_small.png";
            case EntityType::NEBULA:
                $r = ($this->id % 9) + 1;

                return ObjectWithImage::BASE_PATH . "/nebulas/nebula" . $r . "_small.png";
            case EntityType::PLANET:
                return ObjectWithImage::BASE_PATH . "/planets/planet" . $this->image . "_small.png";
            case EntityType::STAR:
                return ObjectWithImage::BASE_PATH . "/stars/star" . $this->typeId . "_small.png";
            case EntityType::WORMHOLE:
                $prefix = $this->wormholePersistent ? 'wormhole_persistent' : 'wormhole';

                return ObjectWithImage::BASE_PATH . "/wormholes/" . $prefix . "1_small.png";
            default:
                return ObjectWithImage::BASE_PATH . "/space/space" . random_int(1, 10) . "_small.png";
        }
    }
}
