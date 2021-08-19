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
        $this->parts[] = 'user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function ip(string $ip): self
    {
        $this->parts[] = 'ip_addr = :ip';
        $this->parameters['ip'] = $ip;

        return $this;
    }
}
