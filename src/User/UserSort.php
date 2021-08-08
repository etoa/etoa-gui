<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSort;

class UserSort extends AbstractSort
{
    public static function nick(string $order): UserSort
    {
        return new UserSort(['user_nick' => $order]);
    }

    public static function points(string $order): UserSort
    {
        return new UserSort(['user_points' => $order]);
    }

    public static function rank(string $order): UserSort
    {
        return new UserSort(['user_rank' => $order]);
    }
}
