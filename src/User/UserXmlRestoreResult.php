<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Entity\User;

class UserXmlRestoreResult
{
    /** @var string[] */
    public array $restoredPlanets = [];

    /** @var string[] */
    public array $skippedPlanets = [];

    /** @var string[] */
    public array $skippedItems = [];

    public ?string $originalAlliance = null;

    public function __construct(
        public readonly User   $user,
        public readonly string $generatedPassword,
    ) {
    }
}
