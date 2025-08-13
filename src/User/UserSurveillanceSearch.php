<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\User;

class UserSurveillanceSearch extends AbstractSearch
{
    public static function create(): UserSurveillanceSearch
    {
        return new UserSurveillanceSearch();
    }

    public function user(int|User $user): self
    {
        $this->parts[] = 'q.user = :user';
        $this->parameters['user'] = $user;

        return $this;
    }

    public function session(string $session): self
    {
        $this->parts[] = 'q.session = :session';
        $this->parameters['session'] = $session;

        return $this;
    }
}
