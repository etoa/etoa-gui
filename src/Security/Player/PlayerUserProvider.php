<?php declare(strict_types=1);

namespace EtoA\Security\Player;

use EtoA\User\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class PlayerUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function refreshUser(UserInterface $user): CurrentPlayer
    {
        if (!$user instanceof CurrentPlayer) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_debug_type($user)));
        }

        $data = $this->userRepository->getUser($user->getId());
        if ($data === null) {
            $e = new UserNotFoundException('User with id ' . $user->getId() . ' not found.');
            $e->setUserIdentifier(json_encode($user->getId()));

            throw $e;
        }

        return new CurrentPlayer($data);
    }

    public function supportsClass(string $class): bool
    {
        return $class === CurrentPlayer::class;
    }

    public function loadUserByIdentifier(string $identifier): CurrentPlayer
    {
        $user = $this->userRepository->getUserByNick($identifier);

        if (null === $user) {
            $e = new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
            $e->setUserIdentifier($identifier);

            throw $e;
        }

        return new CurrentPlayer($user);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof CurrentPlayer) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_debug_type($user)));
        }

        $this->userRepository->updatePassword($user->getData()->getId(), $newHashedPassword, true);
    }
}
