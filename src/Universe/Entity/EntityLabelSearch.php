<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

class EntityLabelSearch extends EntitySearch
{
    public static function create(): EntityLabelSearch
    {
        return new EntityLabelSearch();
    }

    public function planetUserId(int $planetUserId): self
    {
        $this->parts[] = 'planets.planet_user_id = :planetUserId';
        $this->parameters['planetUserId'] = $planetUserId;

        return $this;
    }

    public function planetUserMain(bool $main): self
    {
        $this->parts[] = 'planets.planet_user_main = :planetUserMain';
        $this->parameters['planetUserMain'] = (int) $main;

        return $this;
    }

    public function planetDebris(bool $debris): self
    {
        if ($debris) {
            $this->parts[] = 'planets.planet_wf_metal > 0 OR planets.planet_wf_crystal > 0 OR planets.planet_wf_plastic > 0';
        } else {
            $this->parts[] = 'planets.planet_wf_metal = 0 AND planets.planet_wf_crystal = 0 AND planets.planet_wf_plastic = 0';
        }

        return $this;
    }

    public function planetHasDescription(bool $description): self
    {
        if ($description) {
            $this->parts[] = "p.planet_desc <> ''";
        } else {
            $this->parts[] = "p.planet_desc = ''";
        }

        return $this;
    }

    public function likePlanetName(string $planetName): self
    {
        $this->parts[] = 'planets.planet_name LIKE :likePlanetName';
        $this->parameters['likePlanetName'] = '%' . $planetName . '%';

        return $this;
    }

    public function likePlanetUserNick(string $userNick): self
    {
        $this->parts[] = 'users.user_nick LIKE :userNick';
        $this->parameters['userNick'] = '%' . $userNick . '%';

        return $this;
    }
}
