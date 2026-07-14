<?php declare(strict_types=1);

namespace EtoA\Security\Player;

use EtoA\Entity\User;
use EtoA\Entity\UserSitting;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentPlayer implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private readonly User $user,
        private readonly ?UserSitting $sitting = null
    ) {
    }

    public function getId(): int
    {
        return $this->user->getId();
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        if ($this->isSitter()) {
            return ['ROLE_PLAYER_SITTER'];
        }
        return ['ROLE_PLAYER'];
    }

    public function getPassword(): string
    {
        if ($this->sitting !== null) {
            return $this->sitting->getPassword();
        }
        return $this->user->getPassword();
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->user->getNick();
    }

    public function getData(): User
    {
        return $this->user;
    }

    public function isSitter(): bool
    {
        return $this->sitting !== null;
    }

    public function getSitting(): ?UserSitting
    {
        return $this->sitting;
    }
}
