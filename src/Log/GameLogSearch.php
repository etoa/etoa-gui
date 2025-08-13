<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Alliance;
use EtoA\Entity\Entity;
use EtoA\Entity\User;

class GameLogSearch extends AbstractSearch
{
    public static function create(): GameLogSearch
    {
        return new GameLogSearch();
    }

    public function messageLike(string $message): self
    {
        $this->parts[] = 'q.message LIKE :message';
        $this->parameters['message'] = '%' . $message . '%';

        return $this;
    }

    public function severity(int $severity): self
    {
        $this->parts[] = 'q.severity >= :severity';
        $this->parameters['severity'] = $severity;

        return $this;
    }

    public function facility(int $facility): self
    {
        $this->parts[] = 'q.facility = :facility';
        $this->parameters['facility'] = $facility;

        return $this;
    }

    public function user(User $user): self
    {
        $this->parts[] = 'q.user = :user';
        $this->parameters['user'] = $user;

        return $this;
    }

    public function allianceId(int|Alliance $alliance): self
    {
        $this->parts[] = 'q.alliance = :allianceId';
        $this->parameters['alliance'] = $alliance;

        return $this;
    }

    public function entity(int|Entity $entity): self
    {
        $this->parts[] = 'q.entity = :entity';
        $this->parameters['entity'] = $entity;

        return $this;
    }

    public function objectId(int $objectId): self
    {
        $this->parts[] = 'object_id = :objectId';
        $this->parameters['objectId'] = $objectId;

        return $this;
    }
}
