<?php

namespace EtoA\Components\Admin;

use EtoA\User\UserRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('admin_user_info_link')]
class UserInfoLinkComponent
{
    public string $id;

    public function __construct(private readonly UserRepository $userRepo)
    {
    }

    public function getNick(): string
    {
        return $this->userRepo->getNick($this->id);
    }
}