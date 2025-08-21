<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\User;

class UserLoginFailureSearch extends AbstractSearch
{
    public static function create(): UserLoginFailureSearch
    {
        return new UserLoginFailureSearch();
    }

    public function userId(int|User $userId): self
    {
        $this->parts[] = 'q.user = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function likeIp(string $ip): self
    {
        $this->parts[] = 'q.ip LIKE :likeIp';
        $this->parameters['likeIp'] = '%' . $ip . '%';

        return $this;
    }

    public function likeHost(string $host): self
    {
        $this->parts[] = 'q.host LIKE :likeHost';
        $this->parameters['likeHost'] = '%' . $host . '%';

        return $this;
    }

    public function likeClient(string $client): self
    {
        $this->parts[] = 'q.client LIKE :clientLike';
        $this->parameters['clientLike'] = '%' . $client . '%';

        return $this;
    }
}
