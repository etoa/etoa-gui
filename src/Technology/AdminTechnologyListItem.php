<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Universe\Entity\Entity;

class AdminTechnologyListItem extends TechnologyListItem
{
    public Entity $entity;
    public string $technologyName;
    public ?string $planetName;
    public int $planetUserId;
    public string $userNick;
    public int $userPoints;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->entity = new Entity($data);
        $this->technologyName = $data['tech_name'];
        $this->planetUserId = (int) $data['planet_user_id'];
        $this->planetName = $data['planet_name'];
        $this->userNick = $data['user_nick'];
        $this->userPoints = (int) $data['user_points'];
    }
}
