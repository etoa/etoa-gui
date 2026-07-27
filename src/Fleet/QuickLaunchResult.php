<?php

declare(strict_types=1);

namespace EtoA\Fleet;

/**
 * Result of a fleet launch which is triggered without the haven wizard
 * (fleet bookmark, spy or analyze probe);
 */
final class QuickLaunchResult
{
    private function __construct(
        public readonly bool $success,
        public readonly string $message
    ) {
    }

    public static function success(string $message): self
    {
        return new self(true, $message);
    }

    public static function failure(string $message): self
    {
        return new self(false, $message !== '' ? $message : 'Die Flotte konnte nicht gestartet werden!');
    }
}
