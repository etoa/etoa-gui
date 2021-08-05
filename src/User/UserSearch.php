<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;

class UserSearch extends AbstractSearch
{
    public static function create(): UserSearch
    {
        return new UserSearch();
    }

    public function nameLike(string $name): self
    {
        $this->parts[] = "user_nick LIKE :nameLike";
        $this->parameters['nameLike'] = $name . '%';

        return $this;
    }

    public function nameOrEmailOrDualLike(string $like): self
    {
        $this->parts[] = 'user_nick LIKE :like OR user_name LIKE :like OR user_email LIKE :like OR user_email_fix LIKE :like OR dual_email LIKE :like OR dual_name LIKE :like';
        $this->parameters['like'] = '%' . $like . '%';

        return $this;
    }
}
