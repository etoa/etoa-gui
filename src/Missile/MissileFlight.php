<?php declare(strict_types=1);

namespace EtoA\Missile;

class MissileFlight
{
    public int $id;
    public int $targetPlanetId;
    public ?string $targetPlanetName;
    public int $landTime;

    public function __construct(array $data)
    {
        $this->id = (int) $data['flight_id'];
        $this->targetPlanetId = (int) $data['id'];
        $this->targetPlanetName = $data['planet_name'];
        $this->landTime = (int) $data['flight_landtime'];
    }
}
