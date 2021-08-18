<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

class EntityLabel extends Entity
{
    public ?string $planetName;
    public ?string $starName;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->planetName = $data['planet_name'];
        $this->starName = $data['star_name'];
    }

    public function toString(): string
    {
        switch ($this->code) {
            case EntityType::PLANET:
                return $this->coordinatesString() . ' ' . (filled($this->planetName) ? $this->planetName : 'Unbenannt');
            case EntityType::STAR:
                return $this->coordinatesString() . ' ' . (filled($this->starName) ? $this->starName : 'Unbenannt');
            case EntityType::ALLIANCE_MARKET:
            case EntityType::MARKET:
                return '';
            default:
                return $this->coordinatesString();
        }
    }
}
