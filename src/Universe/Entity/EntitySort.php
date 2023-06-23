<?php declare(strict_types=1);

namespace EtoA\Universe\Entity;

use EtoA\Core\Database\AbstractSort;

class EntitySort extends AbstractSort
{
    public static function pos(): EntitySort
    {
        return new EntitySort(['pos' => null]);
    }
}
