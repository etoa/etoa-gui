<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\Database\AbstractSearch;

class DefenseQueueSearch extends AbstractSearch
{
    public static function create(): DefenseQueueSearch
    {
        return new DefenseQueueSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'queue_user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function entityId(int $entityId): self
    {
        $this->parts[] = 'queue_entity_id = :entityId';
        $this->parameters['entityId'] = $entityId;

        return $this;
    }

    public function startEqualAfter(int $time): self
    {
        $this->parts[] = 'queue_endtime >= :startEqualAfter';
        $this->parameters['startEqualAfter'] = $time;

        return $this;
    }

    public function endAfter(int $time): self
    {
        $this->parts[] = 'queue_endtime > :endAfter';
        $this->parameters['endAfter'] = $time;

        return $this;
    }
}