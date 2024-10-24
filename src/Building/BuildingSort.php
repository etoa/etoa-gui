<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\Database\AbstractSort;

class BuildingSort extends AbstractSort
{
    public static function id(): BuildingSort
    {
        return new BuildingSort(['q.id' => null]);
    }

    public static function name(): BuildingSort
    {
        return new BuildingSort(['q.name' => null]);
    }

    public static function type(): BuildingSort
    {
        return new BuildingSort(['q.typeId' => null, 'q.order' => null, 'q.name' => null]);
    }
}
