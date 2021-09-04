<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;

class UserSessionSearch extends AbstractSearch
{
    public static function create(): UserSessionSearch
    {
        return new UserSessionSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 's.user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function userNickLike(string $userNick): self
    {
        $this->parts[] = 'users.user_nick LIKE :userNick';
        $this->parameters['userNick'] = '%' . $userNick . '%';

        return $this;
    }

    public function ip(string $ip): self
    {
        $this->parts[] = 's.ip_addr = :ip';
        $this->parameters['ip'] = $ip;

        return $this;
    }

    public function userAgentLike(string $userAgent): self
    {
        $this->parts[] = 's.user_agent LIKE :userAgent';
        $this->parameters['userAgent'] = '%' . $userAgent . '%';

        return $this;
    }

    public function minDuration(int $seconds): self
    {
        $this->parts[] = '(s.time_action - s.time_login)> :minDuration';
        $this->parameters['minDuration'] = $seconds;

        return $this;
    }
}
