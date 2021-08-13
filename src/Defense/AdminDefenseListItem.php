<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Universe\Entity\Entity;

class AdminDefenseListItem extends DefenseListItem
{
    public Entity $entity;
    public string $defenseName;
    public ?string $planetName;
    public int $planetUserId;
    public string $userNick;
    public int $userPoints;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->entity = new Entity($data);
        $this->defenseName = $data['def_name'];
        $this->planetUserId = (int) $data['planet_user_id'];
        $this->planetName = $data['planet_name'];
        $this->userNick = $data['user_nick'];
        $this->userPoints = (int) $data['user_points'];
    }
}
