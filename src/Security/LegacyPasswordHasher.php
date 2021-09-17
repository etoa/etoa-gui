<?php declare(strict_types=1);

namespace EtoA\Security;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class LegacyPasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        return saltPasswort($plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        return validatePasswort($plainPassword, $hashedPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }
}
