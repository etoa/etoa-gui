<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;

class UserLoginFailureSearch extends AbstractSearch
{
    public static function create(): UserLoginFailureSearch
    {
        return new UserLoginFailureSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'l.failure_user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function likeIp(string $ip): self
    {
        $this->parts[] = 'l.failure_ip LIKE :likeIp';
        $this->parameters['likeIp'] = '%' . $ip . '%';

        return $this;
    }

    public function likeHost(string $host): self
    {
        $this->parts[] = 'l.failure_host LIKE :likeHost';
        $this->parameters['likeHost'] = '%' . $host . '%';

        return $this;
    }

    public function likeClient(string $client): self
    {
        $this->parts[] = 'l.failure_client LIKE :clientLike';
        $this->parameters['clientLike'] = '%' . $client . '%';

        return $this;
    }
}
