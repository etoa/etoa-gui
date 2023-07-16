<?php declare(strict_types=1);

namespace EtoA\Security\Player;

use EtoA\User\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentPlayer implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(private readonly User $user)
    {
    }

    public function getId(): int
    {
        return $this->user->id;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_PLAYER'];
    }

    public function getPassword(): string
    {
        return $this->user->password;  // TODO
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): string
    {
        return $this->user->nick;
    }

    public function getUserIdentifier(): string
    {
        return $this->user->nick;
    }

    public function getData(): User
    {
        return $this->user;
    }
}
