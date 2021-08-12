<?php declare(strict_types=1);

namespace EtoA\Core\Database;

abstract class AbstractSearch
{
    /** @var string[] */
    public array $parts = [];
    /** @var array<string, mixed> */
    public array $parameters = [];
    /** @var array<string, string[]> */
    public array $stringArrayParameters = [];
}
