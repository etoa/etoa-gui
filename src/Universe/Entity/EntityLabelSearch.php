<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

class EntityLabelSearch extends EntitySearch
{
    public static function create(): EntityLabelSearch
    {
        return new EntityLabelSearch();
    }

    public function likePlanetName(string $planetName): self
    {
        $this->parts[] = 'planets.planet_name LIKE :likePlanetName';
        $this->parameters['likePlanetName'] = '%' . $planetName . '%';

        return $this;
    }
}
