<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

class EntityLabel extends Entity
{
    public ?string $planetName;
    public ?string $starName;
    public ?int $ownerId;
    public ?string $ownerNick;
    public bool $ownerMain;
    public ?int $typeId = null;

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
        } elseif ($data['star_type'] !== null) {
            $this->typeId = (int) $data['star_type'];
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
}
