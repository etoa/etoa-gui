<?php declare(strict_types=1);

namespace EtoA\Components\Helper;

class SearchResult
{
    /**
     * @param list<mixed> $entries
     */
    public function __construct(
        public array $entries,
        public int $limit,
        public int $total,
        public int $perPage
    ) {
    }
}
