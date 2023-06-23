<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;

class UserSurveillanceSearch extends AbstractSearch
{
    public static function create(): UserSurveillanceSearch
    {
        return new UserSurveillanceSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function session(string $session): self
    {
        $this->parts[] = 'session = :session';
        $this->parameters['session'] = $session;

        return $this;
    }
}
