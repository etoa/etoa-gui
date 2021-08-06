<?php declare(strict_types=1);

namespace EtoA\Core\Database;

class AbstractSort
{
    /** @var array<string, ?string> */
    public array $sorts;

    /** @param array<string, ?string> $sorts */
    public function __construct(array $sorts = [])
    {
        $this->sorts = $sorts;
    }
}
