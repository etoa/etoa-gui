<?php declare(strict_types=1);

namespace EtoA\Technology;

class TechnologySort
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

    public static function id(): TechnologySort
    {
        return new TechnologySort(['t.tech_id']);
    }

    public static function name(): TechnologySort
    {
        return new TechnologySort(['t.tech_name']);
    }

    public static function type(): TechnologySort
    {
        return new TechnologySort(['tt.type_order', 't.tech_order', 't.tech_name']);
    }
}
