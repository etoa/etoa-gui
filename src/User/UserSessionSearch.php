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

    public function ip(string $ip): self
    {
        $this->parts[] = 's.ip_addr = :ip';
        $this->parameters['ip'] = $ip;

        return $this;
    }

    public function ipLike(string $ip): self
    {
        $this->parts[] = 's.ip_addr LIKE :ipLike';
        $this->parameters['ipLike'] = '%' . $ip . '%';

        return $this;
    }

    public function userAgentLike(string $userAgent): self
    {
        $this->parts[] = 's.user_agent LIKE :userAgent';
        $this->parameters['userAgent'] = '%' . $userAgent . '%';

        return $this;
    }
}
