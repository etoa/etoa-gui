<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\AdminUser;
use EtoA\Entity\User;

class DebrisLogSearch extends AbstractSearch
{
    public static function create(): DebrisLogSearch
    {
        return new DebrisLogSearch();
    }

    public function userId(int|User $userId): self
    {
        $this->parts[] = 'q.user = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function adminId(int|AdminUser $adminId): self
    {
        $this->parts[] = 'q.admin = :adminId';
        $this->parameters['adminId'] = $adminId;

        return $this;
    }

    public function timeBefore(int $timestamp): self
    {
        $this->parts[] = 'q.timestamp <= :timeBefore';
        $this->parameters['timeBefore'] = $timestamp;

        return $this;
    }
}
