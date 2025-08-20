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
        return new TechnologySort(['q.id']);
    }

    public static function name(): TechnologySort
    {
        return new TechnologySort(['q.name']);
    }

    public static function type(): TechnologySort
    {
        return new TechnologySort(['tt.order', 'q.order', 'q.name']);
    }
}
