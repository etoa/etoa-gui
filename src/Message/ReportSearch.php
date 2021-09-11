<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\Database\AbstractSearch;

class ReportSearch extends AbstractSearch
{
    public static function create(): ReportSearch
    {
        return new ReportSearch();
    }

    public function id(int $id): self
    {
        $this->parts[] = 'id = :id';
        $this->parameters['id'] = $id;

        return $this;
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function opponentId(int $opponentId): self
    {
        $this->parts[] = 'opponent_id = :opponentId';
        $this->parameters['opponentId'] = $opponentId;

        return $this;
    }

    public function read(bool $read): self
    {
        $this->parts[] = 'read = :read';
        $this->parameters['read'] = (int) $read;

        return $this;
    }

    public function archived(bool $archived): self
    {
        $this->parts[] = 'archived = :archived';
        $this->parameters['archived'] = (int) $archived;

        return $this;
    }

    public function deleted(bool $deleted): self
    {
        $this->parts[] = 'deleted = :deleted';
        $this->parameters['deleted'] = (int) $deleted;

        return $this;
    }

    public function type(string $type): self
    {
        $this->parts[] = 'type = :type';
        $this->parameters['type'] = $type;

        return $this;
    }

    public function dateFrom(int $dateTime): self
    {
        $this->parts[] = 'timestamp > :dateFrom';
        $this->parameters['dateFrom'] = $dateTime;

        return $this;
    }

    public function dateTo(int $dateTime): self
    {
        $this->parts[] = 'timestamp < :dateTo';
        $this->parameters['dateTo'] = $dateTime;

        return $this;
    }

    public function entityId(int $entityId, int $identifier = null): self
    {
        $this->parts[] = sprintf('entity1_id = :entityId%s OR entity2_id = :entityId%s', $identifier, $identifier);
        $this->parameters['entityId' . $identifier] = $entityId;

        return $this;
    }
}
