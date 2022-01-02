<?php declare(strict_types=1);

namespace EtoA\Core\Database;

class PropertyAssign
{
    public static function assign(object $from, object $to): void
    {
        foreach (get_object_vars($from) as $key => $value) {
            $to->{$key} = $value;
        }
    }
}
