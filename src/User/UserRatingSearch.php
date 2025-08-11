<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\User;

class UserRatingSearch extends AbstractSearch
{
    public static function create(): UserRatingSearch
    {
        return new UserRatingSearch();
    }

    public function nick(string $userNick): self
    {
        $this->parts[] = 'u.nick LIKE :nick';
        $this->parameters['nick'] = $userNick . '%';

        return $this;
    }

    public function user(User $user): self
    {
        $this->parts[] = 'u.id LIKE :user';
        $this->parameters['user'] = $user . '%';

        return $this;
    }

    public function ghost(bool $ghost): self
    {
        $this->parts[] = 'u.ghost = :ghost';
        $this->parameters['ghost'] = (int) $ghost;

        return $this;
    }
}
