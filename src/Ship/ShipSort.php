<?php declare(strict_types=1);

namespace EtoA\Ship;

class ShipSort
{
    /** @var string[] */
    public array $sorts;

    /**
     * @param string[] $sorts
     */
    public function __construct(array $sorts)
    {
        $this->sorts = $sorts;
    }

    public static function id(): ShipSort
    {
        return new ShipSort(['ship_id']);
    }

    public static function name(): ShipSort
    {
        return new ShipSort(['ship_name']);
    }

    public static function category(): ShipSort
    {
        return new ShipSort(['ship_cat_id', 'ship_order', 'ship_name']);
    }
}
