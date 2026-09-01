<?php declare(strict_types=1);

namespace EtoA\Security\Player;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

/**
 * Shared by every authenticator on the "game" firewall: resolves the nick to
 * a CurrentPlayer, preferring an active user-sitting password over the
 * player's own one, exactly like the native login does.
 */
trait PlayerCredentialsUserBadgeTrait
{
    private function createPlayerUserBadge(string $username, string $password): UserBadge
    {
        return new UserBadge($username, function (string $username) use ($password): CurrentPlayer {
            $user = $this->userProvider->loadUserByIdentifier($username);
            $activeSitting = $this->sittingRepository->getActiveUserEntry($user->getData());
            if ($activeSitting !== null && password_verify($password, $activeSitting->getPassword())) {
                return $this->userProvider->loadUserByIdentifierWithSitting($username, $activeSitting);
            }

            return $user;
        });
    }
}
