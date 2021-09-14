<?php

declare(strict_types=1);

namespace EtoA\Admin;

class AdminUser
{
    public const CONTACT_REQUIRED_EMAIL_SUFFIX = "@etoa.ch";

    public ?int $id = null;
    public ?string $passwordString;
    public bool $forcePasswordChange = false;
    public string $nick = "";
    public string $name = "";
    public string $email = "";
    public string $tfaSecret = "";
    public int $playerId = 0;
    public string $boardUrl = "";
    public string $userTheme = "";
    public bool $ticketEmail = false;
    public bool $locked = false;
    public bool $isContact = true;
    /** @var string[] */
    public array $roles = [];

    public static function createFromArray(array $data): AdminUser
    {
        $adminUser = new AdminUser();
        $adminUser->id = (int) $data['user_id'];
        $adminUser->passwordString = $data['user_password'];
        $adminUser->nick = $data['user_nick'];
        $adminUser->forcePasswordChange = (bool) $data['user_force_pwchange'];
        $adminUser->name = $data['user_name'];
        $adminUser->email = $data['user_email'];
        $adminUser->tfaSecret = $data['tfa_secret'];
        $adminUser->playerId = (int) $data['player_id'];
        $adminUser->boardUrl = $data['user_board_url'];
        $adminUser->userTheme = $data['user_theme'];
        $adminUser->ticketEmail = (bool) $data['ticketmail'];
        $adminUser->locked = (bool) $data['user_locked'];
        $adminUser->roles = explode(",", $data['roles']);
        $adminUser->isContact = (bool) $data['is_contact'];

        return $adminUser;
    }

    public function checkEqualPassword(string $newPassword): bool
    {
        return validatePasswort($newPassword, $this->passwordString);
    }
}
