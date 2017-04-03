<?php

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

    public function getCurrentUser()
    {
        return $this->currentUser;
    }
}
