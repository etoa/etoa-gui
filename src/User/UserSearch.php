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
        $this->parts[] = "LOWER(q.nick) = :nickname";
        $this->parameters['nickname'] = strtolower($nickname);

        return $this;
    }

    public function nickLike(string $name): self
    {
        $this->parts[] = "q.nick LIKE :nickLike";
        $this->parameters['nickLike'] = '%' .$name . '%';

        return $this;
    }

    public function nameLike(string $name): self
    {
        $this->parts[] = "q.name LIKE :nameLike";
        $this->parameters['nameLike'] = '%' . $name . '%';

        return $this;
    }

    public function emailLike(string $email): self
    {
        $this->parts[] = "q.email LIKE :emailLike";
        $this->parameters['emailLike'] = '%' . $email . '%';

        return $this;
    }

    public function emailOrEmailFix(string $email): self
    {
        $this->parts[] = "q.emailFix = :emailOrEmailFix OR q.email = :emailOrEmailFix";
        $this->parameters['emailOrEmailFix'] = $email;

        return $this;
    }

    public function emailFix(string $emailFix): self
    {
        $this->parts[] = "q.emailFix = :emailFixed";
        $this->parameters['emailFixed'] = $emailFix;

        return $this;
    }

    public function emailFixLike(string $emailFix): self
    {
        $this->parts[] = "q.emailFix LIKE :emailFixedLike";
        $this->parameters['emailFixedLike'] = '%' . $emailFix . '%';

        return $this;
    }

    public function nickOrEmailOrDualLike(string $like): self
    {
        $this->parts[] = 'q.nick LIKE :like OR q.name LIKE :like OR q.email LIKE :like OR q.emailFix LIKE :like OR q.dualEmail LIKE :like OR q.dualName LIKE :like';
        $this->parameters['like'] = '%' . $like . '%';

        return $this;
    }

    public function password(string $saltedPassword): self
    {
        $this->parts[] = "q.password = :password";
        $this->parameters['password'] = $saltedPassword;

        return $this;
    }

    public function observed(): self
    {
        $this->parts[] = "q.observe IS NOT NULL";

        return $this;
    }

    public function notObserved(): self
    {
        $this->parts[] = "q.observe IS NULL";

        return $this;
    }

    public function notGhost(): self
    {
        $this->parts[] = "q.ghost = 0";

        return $this;
    }

    public function blocked(): self
    {
        $this->parts[] = "(q.blockedFrom < :now AND q.blockedTo > :now)";
        $this->parameters['now'] = time();

        return $this;
    }

    public function inHolidays(?bool $active = true): self
    {
        if ($active === true) {
            $this->parts[] = "q.hmodeFrom > 0";
        } elseif ($active === false) {
            $this->parts[] = "q.hmodeFrom = 0";
        }

        return $this;
    }

    public function notBlocked(): self
    {
        $this->parts[] = "q.blockedTo < :now";
        $this->parameters['now'] = time();

        return $this;
    }

    public function hasPoints(): self
    {
        $this->parts[] = "q.points > 0";

        return $this;
    }

    public function inHmode(): self
    {
        $this->parts[] = "(q.hmodeFrom < :now AND q.hmodeTo > :now)";
        $this->parameters['now'] = time();

        return $this;
    }

    public function notInHmode(): self
    {
        $this->parts[] = "q.hmodeTo < :now";
        $this->parameters['now'] = time();

        return $this;
    }

    public function withProfileImage(): self
    {
        $this->parts[] = "q.profileImg <> ''";

        return $this;
    }

    public function confirmedImageCheck(): self
    {
        $this->parts[] = "q.profileImgCheck = 1 AND q.profileImg <> ''";

        return $this;
    }

    public function allianceId(int $allianceId): self
    {
        $this->parts[] = "q.allianceId = :allianceId";
        $this->parameters['allianceId'] = $allianceId;

        return $this;
    }

    public function raceId(int $raceId): self
    {
        $this->parts[] = "q.raceId = :raceId";
        $this->parameters['raceId'] = $raceId;

        return $this;
    }

    public function user(int $userId): self
    {
        $this->parts[] = "q.id = :userId";
        $this->parameters['userId'] = $userId;

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function ids(array $ids): self
    {
        $this->parts[] = "q.id IN(:ids)";
        $this->stringArrayParameters['ids'] = $ids;

        return $this;
    }

    public function notUser(int $userId): self
    {
        $this->parts[] = "q.id <> :notUserId";
        $this->parameters['notUserId'] = $userId;

        return $this;
    }

    public function race(int $raceId): self
    {
        $this->parts[] = "q.raceId = :race";
        $this->parameters['race'] = $raceId;

        return $this;
    }

    public function ip(string $ip): self
    {
        $this->parts[] = "q.ip = :ip";
        $this->parameters['ip'] = $ip;

        return $this;
    }

    public function ipLike(string $ip): self
    {
        $this->parts[] = "q.ip LIKE :ipLike";
        $this->parameters['ipLike'] = '%' . $ip . '%';

        return $this;
    }

    public function hostname(string $hostname): self
    {
        $this->parts[] = "q.hostname = :hostname";
        $this->parameters['hostname'] = $hostname;

        return $this;
    }

    public function profileTextLike(string $profileText): self
    {
        $this->parts[] = "q.profileText LIKE :profileTextLike";
        $this->parameters['profileTextLike'] = '%' . $profileText . '%';

        return $this;
    }

    public function chatadmin(bool $chatadmin): self
    {
        $this->parts[] = "q.chatadmin = :chatadmin";
        $this->parameters['chatadmin'] = (int) $chatadmin;

        return $this;
    }

    public function ghost(bool $ghost): self
    {
        $this->parts[] = "q.ghost = :ghost";
        $this->parameters['ghost'] = (int) $ghost;

        return $this;
    }

    public function allianceLike(string $allianceName): self
    {
        $this->parts[] = "q.alliances.name LIKE :allianceLike";
        $this->parameters['allianceLike'] = '%' . $allianceName . '%';

        return $this;
    }
}
