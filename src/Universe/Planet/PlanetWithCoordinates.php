<?php declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Universe\Entity\Entity;

class PlanetWithCoordinates extends Planet
{
    public Entity $entity;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->entity = new Entity($data);
    }

    public function toString(): string
    {
        return $this->entity->coordinatesString() . ' ' . ($this->displayName());
    }
}
