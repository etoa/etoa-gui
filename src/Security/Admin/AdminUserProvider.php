<?php declare(strict_types=1);

namespace EtoA\Security\Admin;

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AdminUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private AdminUserRepository $adminUserRepository;

    public function __construct(AdminUserRepository $adminUserRepository)
    {
        $this->adminUserRepository = $adminUserRepository;
    }

    public function refreshUser(UserInterface $user): ?CurrentAdmin
    {
        if (!$user instanceof CurrentAdmin) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_debug_type($user)));
        }

        $data = $this->adminUserRepository->find($user->getId());

        return $data !== null ? new CurrentAdmin($data) : null;
    }

    public function supportsClass(string $class): bool
    {
        return $class === CurrentAdmin::class;
    }

    public function loadUserByUsername(string $username)
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): ?CurrentAdmin
    {
        $user = $this->adminUserRepository->findOneByNick($identifier);

        if (null === $user) {
            $e = new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
            $e->setUserIdentifier($identifier);

            throw $e;
        }

        return new CurrentAdmin($user);
    }

    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof CurrentAdmin) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_debug_type($user)));
        }

        $this->adminUserRepository->setPassword($user->getData(), $newHashedPassword, false);
    }
}
