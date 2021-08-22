<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\Database\AbstractSearch;

class DebrisLogSearch extends AbstractSearch
{
    public static function create(): DebrisLogSearch
    {
        return new DebrisLogSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function adminId(int $adminId): self
    {
        $this->parts[] = 'admin_id = :adminId';
        $this->parameters['adminId'] = $adminId;

        return $this;
    }

    public function timeBefore(int $timestamp): self
    {
        $this->parts[] = 'time <= :timeBefore';
        $this->parameters['timeBefore'] = $timestamp;

        return $this;
    }
}
