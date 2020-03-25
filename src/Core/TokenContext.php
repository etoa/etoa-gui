<?php declare(strict_types=1);

namespace EtoA\Core;

use EtoA\User\UserInterface;

class TokenContext
{
    /** @var UserInterface */
    private $currentUser;

    public function __construct(UserInterface $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    public function getCurrentUser(): UserInterface
    {
        return $this->currentUser;
    }
}
