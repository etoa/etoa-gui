<?php declare(strict_types=1);

namespace EtoA\Defense;

class DefenseSort
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

    public static function id(): DefenseSort
    {
        return new DefenseSort(['def_id']);
    }

    public static function name(): DefenseSort
    {
        return new DefenseSort(['def_name']);
    }

    public static function category(): DefenseSort
    {
        return new DefenseSort(['def_cat_id', 'def_order', 'def_name']);
    }
}