<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;

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

    public function id(int $userId): self
    {
        $this->parts[] = 'u.user_id LIKE :id';
        $this->parameters['id'] = $userId . '%';

        return $this;
    }

    public function ghost(bool $ghost): self
    {
        $this->parts[] = 'u.user_ghost = :ghost';
        $this->parameters['ghost'] = (int) $ghost;

        return $this;
    }
}
