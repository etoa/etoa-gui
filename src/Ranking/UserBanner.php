<?php declare(strict_types=1);

namespace EtoA\Ranking;

class UserBanner
{
    public function __construct(
        public int $userId,
        public string $storagePath,
        public string $url,
    ) {
    }
}
