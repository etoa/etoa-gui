<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

use EtoA\Core\Database\AbstractSort;

class EntityLabelSort extends AbstractSort
{
    public static function id(): EntityLabelSort
    {
        return new EntityLabelSort(['e.id' => null]);
    }

    public static function planetName(): EntityLabelSort
    {
        return new EntityLabelSort(['planets.planet_name' => null]);
    }

    public static function userNick(): EntityLabelSort
    {
        return new EntityLabelSort(['users.user_nick' => null]);
    }
}
