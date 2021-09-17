<?php declare(strict_types=1);

namespace EtoA\Security\Admin;

use EtoA\Admin\AdminUser;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentAdmin implements UserInterface, PasswordAuthenticatedUserInterface
{
    private AdminUser $adminUser;

    public function __construct(AdminUser $adminUser)
    {
        $this->adminUser = $adminUser;
    }

    public function getId(): int
    {
        return $this->adminUser->id;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return array_map(fn (string $role) => 'ROLE_ADMIN_' . strtoupper($role), $this->adminUser->roles);
    }

    public function getPassword(): string
    {
        return $this->adminUser->passwordString;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): string
    {
        return $this->adminUser->nick;
    }

    public function getUserIdentifier(): string
    {
        return $this->adminUser->nick;
    }

    public function getData(): AdminUser
    {
        return $this->adminUser;
    }
}
