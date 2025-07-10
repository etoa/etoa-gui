<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSort;

class UserSort extends AbstractSort
{
    public static function nick(string $order): UserSort
    {
        return new UserSort(['q.nick' => $order]);
    }

    public static function points(string $order): UserSort
    {
        return new UserSort(['q.points' => $order]);
    }

    public static function rank(string $order): UserSort
    {
        return new UserSort(['q.rank' => $order]);
    }
}
