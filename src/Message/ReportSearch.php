<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Entity;
use EtoA\Entity\User;

class ReportSearch extends AbstractSearch
{
    public static function create(): ReportSearch
    {
        return new ReportSearch();
    }

    public function id(int $id): self
    {
        $this->parts[] = 'q.id = :id';
        $this->parameters['id'] = $id;

        return $this;
    }

    public function userId(int|User $userId): self
    {
        $this->parts[] = 'q.user = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function opponentId(int|User $opponentId): self
    {
        $this->parts[] = 'q.opponent = :opponentId';
        $this->parameters['opponentId'] = $opponentId;

        return $this;
    }

    public function read(bool $read): self
    {
        $this->parts[] = '`q.read` = :read';
        $this->parameters['read'] = (int) $read;

        return $this;
    }

    public function archived(bool $archived): self
    {
        $this->parts[] = 'q.archived = :archived';
        $this->parameters['archived'] = (int) $archived;

        return $this;
    }

    public function deleted(bool $deleted): self
    {
        $this->parts[] = 'q.deleted = :deleted';
        $this->parameters['deleted'] = (int) $deleted;

        return $this;
    }

    public function type(string $type): self
    {
        $this->parts[] = 'q.type = :type';
        $this->parameters['type'] = $type;

        return $this;
    }

    public function dateFrom(int $dateTime): self
    {
        $this->parts[] = 'q.timestamp > :dateFrom';
        $this->parameters['dateFrom'] = $dateTime;

        return $this;
    }

    public function dateTo(int $dateTime): self
    {
        $this->parts[] = 'q.timestamp < :dateTo';
        $this->parameters['dateTo'] = $dateTime;

        return $this;
    }

    public function entity1Id(int $entityId): self
    {
        $this->parts[] = 'q.entity1 = :entity1Id';
        $this->parameters['entity1Id'] = $entityId;

        return $this;
    }

    public function entityId(int|Entity $entityId, int $identifier = null): self
    {
        $this->parts[] = sprintf('q.entity1 = :entityId%s OR q.entity2 = :entityId%s', $identifier, $identifier);
        $this->parameters['q.entity' . $identifier] = $entityId;

        return $this;
    }
}
