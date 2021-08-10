<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;

class UserSearch extends AbstractSearch
{
    public static function create(): UserSearch
    {
        return new UserSearch();
    }

    public function nick(string $nickname): self
    {
        $this->parts[] = "LCASE(user_nick) = :nickname";
        $this->parameters['nickname'] = strtolower($nickname);

        return $this;
    }

    public function nameLike(string $name): self
    {
        $this->parts[] = "user_nick LIKE :nameLike";
        $this->parameters['nameLike'] = $name . '%';

        return $this;
    }

    public function emailOrEmailFix(string $email): self
    {
        $this->parts[] = "user_email_fix = :emailOrEmailFix OR user_email = :emailOrEmailFix";
        $this->parameters['emailOrEmailFix'] = $email;

        return $this;
    }

    public function emailFix(string $emailFix): self
    {
        $this->parts[] = "user_email_fix = :emailFixed";
        $this->parameters['emailFixed'] = $emailFix;

        return $this;
    }

    public function nameOrEmailOrDualLike(string $like): self
    {
        $this->parts[] = 'user_nick LIKE :like OR user_name LIKE :like OR user_email LIKE :like OR user_email_fix LIKE :like OR dual_email LIKE :like OR dual_name LIKE :like';
        $this->parameters['like'] = '%' . $like . '%';

        return $this;
    }

    public function password(string $saltedPassword): self
    {
        $this->parts[] = "user_password = :password";
        $this->parameters['password'] = $saltedPassword;

        return $this;
    }

    public function notObserved(): self
    {
        $this->parts[] = "user_observe IS NULL";

        return $this;
    }

    public function withProfileImage(): self
    {
        $this->parts[] = "user_profile_img <> ''";

        return $this;
    }

    public function confirmedImageCheck(): self
    {
        $this->parts[] = "user_profile_img_check = 1 AND user_profile_img <> ''";

        return $this;
    }

    public function allianceId(int $allianceId): self
    {
        $this->parts[] = "user_alliance_id = :allianceId";
        $this->parameters['allianceId'] = $allianceId;

        return $this;
    }

    public function user(int $userId): self
    {
        $this->parts[] = "user_id = :userId";
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function notUser(int $userId): self
    {
        $this->parts[] = "user_id <> :notUserId";
        $this->parameters['notUserId'] = $userId;

        return $this;
    }
}
