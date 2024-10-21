<?php declare(strict_types=1);

namespace EtoA\Security\Admin;

use EtoA\Entity\AdminUser;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfiguration;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentAdmin implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    public function __construct(private readonly AdminUser $adminUser)
    {
    }

    public function getId(): int
    {
        return $this->adminUser->getId();
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return array_map(fn(string $role) => 'ROLE_ADMIN_' . strtoupper($role), $this->adminUser->getRoles());
    }

    public function getPassword(): string
    {
        return $this->adminUser->getPasswordString();
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): string
    {
        return $this->adminUser->getNick();
    }

    public function getUserIdentifier(): string
    {
        return $this->adminUser->getNick();
    }

    public function getData(): AdminUser
    {
        return $this->adminUser;
    }

    public function isTotpAuthenticationEnabled(): bool
    {
        return (bool)$this->adminUser->getTfaSecret();
    }

    public function getTotpAuthenticationUsername(): string
    {
        return $this->adminUser->getNick();
    }

    public function getTotpAuthenticationConfiguration(): TotpConfiguration
    {
        return new TotpConfiguration($this->adminUser->getTfaSecret(), TotpConfiguration::ALGORITHM_SHA1, 30, 6);
    }
}
