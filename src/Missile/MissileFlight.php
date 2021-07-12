<?php declare(strict_types=1);

namespace EtoA\Missile;

class MissileFlight
{
    public int $id;
    public int $targetPlanetId;
    public ?string $targetPlanetName;
    public int $landTime;
    /** @var array<int, int> */
    public array $missiles;

    /**
     * @param array<int, int> $missiles
     */
    public function __construct(array $data, array $missiles)
    {
        $this->id = (int) $data['flight_id'];
        $this->targetPlanetId = (int) $data['id'];
        $this->targetPlanetName = $data['planet_name'];
        $this->landTime = (int) $data['flight_landtime'];
        $this->missiles = $missiles;
    }
}
