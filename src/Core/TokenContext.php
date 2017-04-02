<?php

namespace EtoA\Core;

class TokenContext
{
    /** @var \CurrentUser */
    private $currentUser;

    public function __construct(\CurrentUser $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    public function getCurrentUser()
    {
        return $this->currentUser;
    }
}
