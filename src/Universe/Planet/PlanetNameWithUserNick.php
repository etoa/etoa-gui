<?php declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Support\ValidationUtils;

class PlanetNameWithUserNick
{
    public int $id;
    public ?string $planetName;
    public int $userId;
    public ?string $userNick;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->planetName = $data['planet_name'];
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
    }

    public function displayName(): string
    {
        return ValidationUtils::filled($this->planetName) ? $this->planetName : 'Unbenannt';
    }
}
