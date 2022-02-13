<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;

class UserWarningSearch extends AbstractSearch
{
    public static function create(): UserWarningSearch
    {
        return new UserWarningSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'w.warning_user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }
}
