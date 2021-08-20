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

    public function nickLike(string $name): self
    {
        $this->parts[] = "user_nick LIKE :nickLike";
        $this->parameters['nickLike'] = $name . '%';

        return $this;
    }

    public function nameLike(string $name): self
    {
        $this->parts[] = "user_name LIKE :nameLike";
        $this->parameters['nameLike'] = '%' . $name . '%';

        return $this;
    }

    public function emailLike(string $email): self
    {
        $this->parts[] = "user_email LIKE :emailLike";
        $this->parameters['emailLike'] = '%' . $email . '%';

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

    public function emailFixLike(string $emailFix): self
    {
        $this->parts[] = "user_email_fix LIKE :emailFixedLike";
        $this->parameters['emailFixedLike'] = '%' . $emailFix . '%';

        return $this;
    }

    public function nickOrEmailOrDualLike(string $like): self
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

    public function observed(): self
    {
        $this->parts[] = "user_observe IS NOT NULL";

        return $this;
    }

    public function notObserved(): self
    {
        $this->parts[] = "user_observe IS NULL";

        return $this;
    }

    public function notGhost(): self
    {
        $this->parts[] = "user_ghost = 0";

        return $this;
    }

    public function blocked(): self
    {
        $this->parts[] = "(user_blocked_from < :now AND user_blocked_to > :now)";
        $this->parameters['now'] = time();

        return $this;
    }

    public function inHolidays(?bool $active = true): self
    {
        if ($active === true) {
            $this->parts[] = "user_hmode_from > 0";
        } elseif ($active === false) {
            $this->parts[] = "user_hmode_from = 0";
        }

        return $this;
    }

    public function notBlocked(): self
    {
        $this->parts[] = "user_blocked_to < :now";
        $this->parameters['now'] = time();

        return $this;
    }

    public function hasPoints(): self
    {
        $this->parts[] = "user_points > 0";

        return $this;
    }

    public function inHmode(): self
    {
        $this->parts[] = "(user_hmode_from < :now AND user_hmode_to > :now)";
        $this->parameters['now'] = time();

        return $this;
    }

    public function notInHmode(): self
    {
        $this->parts[] = "user_hmode_to < :now";
        $this->parameters['now'] = time();

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

    public function raceId(int $raceId): self
    {
        $this->parts[] = "user_race_id = :raceId";
        $this->parameters['raceId'] = $raceId;

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

    public function race(int $raceId): self
    {
        $this->parts[] = "user_race_id = :race";
        $this->parameters['race'] = $raceId;

        return $this;
    }

    public function ip(string $ip): self
    {
        $this->parts[] = "user_ip = :ip";
        $this->parameters['ip'] = $ip;

        return $this;
    }

    public function ipLike(string $ip): self
    {
        $this->parts[] = "user_ip LIKE :ipLike";
        $this->parameters['ipLike'] = '%' . $ip . '%';

        return $this;
    }

    public function hostname(string $hostname): self
    {
        $this->parts[] = "user_hostname = :hostname";
        $this->parameters['hostname'] = $hostname;

        return $this;
    }

    public function profileTextLike(string $profileText): self
    {
        $this->parts[] = "user_profile_text LIKE :profileTextLike";
        $this->parameters['profileTextLike'] = '%' . $profileText . '%';

        return $this;
    }

    public function chatadmin(bool $chatadmin): self
    {
        $this->parts[] = "user_chatadmin = :chatadmin";
        $this->parameters['chatadmin'] = (int) $chatadmin;

        return $this;
    }

    public function ghost(bool $ghost): self
    {
        $this->parts[] = "user_ghost = :ghost";
        $this->parameters['ghost'] = (int) $ghost;

        return $this;
    }

    public function allianceLike(string $allianceName): self
    {
        $this->parts[] = "alliances.alliance_name LIKE :allianceLike";
        $this->parameters['allianceLike'] = '%' . $allianceName . '%';

        return $this;
    }
}
