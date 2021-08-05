<?php declare(strict_types=1);

namespace EtoA\Building;

class BuildingSort
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

    public static function id(): BuildingSort
    {
        return new BuildingSort(['building_id']);
    }

    public static function name(): BuildingSort
    {
        return new BuildingSort(['building_name']);
    }

    public static function type(): BuildingSort
    {
        return new BuildingSort(['building_type_id', 'building_order', 'building_name']);
    }
}
