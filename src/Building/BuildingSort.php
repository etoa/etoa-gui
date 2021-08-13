<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\Database\AbstractSort;

class BuildingSort extends AbstractSort
{
    public static function id(): BuildingSort
    {
        return new BuildingSort(['building_id' => null]);
    }

    public static function name(): BuildingSort
    {
        return new BuildingSort(['building_name' => null]);
    }

    public static function type(): BuildingSort
    {
        return new BuildingSort(['building_type_id' => null, 'building_order' => null, 'building_name' => null]);
    }
}
