<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSort;

class UserRatingSort extends AbstractSort
{
    public static function nick(string $order): UserRatingSort
    {
        return new UserRatingSort(['nick' => $order]);
    }

    public static function rank(string $order): UserRatingSort
    {
        return new UserRatingSort(['rank' => $order]);
    }

    public static function allianceTag(string $order): UserRatingSort
    {
        return new UserRatingSort(['alliance_tag' => $order]);
    }
}
