<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

use EtoA\Entity\User;

class EntityLabelSearch extends EntitySearch
{
    public static function create(): EntityLabelSearch
    {
        return new EntityLabelSearch();
    }

    public function planetUserId(int|User $planetUserId): self
    {
        $this->parts[] = 'planets.user = :planetUserId';
        $this->parameters['planetUserId'] = $planetUserId;

        return $this;
    }

    public function planetUserIdNotNull(): self
    {
        $this->parts[] = 'planets.user IS NOT NULL';

        return $this;
    }

    public function planetUserMain(bool $main): self
    {
        $this->parts[] = 'planets.mainPlanet = :planetUserMain';
        $this->parameters['planetUserMain'] = $main;

        return $this;
    }

    public function planetDebris(bool $debris): self
    {
        if ($debris) {
            $this->parts[] = 'planets.wfMetal > 0 OR planets.wfCrystal > 0 OR planets.pwfPlastic > 0';
        } else {
            $this->parts[] = 'planets.wfMetal = 0 AND planets.wfCrystal = 0 AND planets.wfPlastic = 0';
        }

        return $this;
    }

    public function planetHasDescription(bool $description): self
    {
        if ($description) {
            $this->parts[] = "p.description <> ''";
        } else {
            $this->parts[] = "p.description = ''";
        }

        return $this;
    }

    public function likePlanetName(string $planetName): self
    {
        $this->parts[] = 'planets.name LIKE :likePlanetName';
        $this->parameters['likePlanetName'] = '%' . $planetName . '%';

        return $this;
    }

    public function likePlanetUserNick(string $userNick): self
    {
        $this->parts[] = 'users.nick LIKE :userNick';
        $this->parameters['userNick'] = '%' . $userNick . '%';

        return $this;
    }
}
