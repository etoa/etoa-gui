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
        $this->parts[] = 'q.userId = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function ip(string $ip): self
    {
        $this->parts[] = 'q.ipAddr = :ip';
        $this->parameters['ip'] = $ip;

        return $this;
    }

    public function ipLike(string $ip): self
    {
        $this->parts[] = 'q.ipAddr LIKE :ipLike';
        $this->parameters['ipLike'] = '%' . $ip . '%';

        return $this;
    }

    public function userAgentLike(string $userAgent): self
    {
        $this->parts[] = 'q.userAgent LIKE :userAgent';
        $this->parameters['userAgent'] = '%' . $userAgent . '%';

        return $this;
    }
}
