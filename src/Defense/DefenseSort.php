<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\Database\AbstractSort;

class DefenseSort extends AbstractSort
{
    public static function id(): DefenseSort
    {
        return new DefenseSort(['def_id' => null]);
    }

    public static function name(): DefenseSort
    {
        return new DefenseSort(['def_name' => null]);
    }

    public static function category(): DefenseSort
    {
        return new DefenseSort(['def_cat_id' => null, 'def_order' => null, 'def_name' => null]);
    }
}
