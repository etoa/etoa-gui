<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\Database\AbstractSearch;

class GameLogSearch extends AbstractSearch
{
    public static function create(): GameLogSearch
    {
        return new GameLogSearch();
    }

    public function messageLike(string $message): self
    {
        $this->parts[] = 'message LIKE :message';
        $this->parameters['message'] = '%' . $message . '%';

        return $this;
    }

    public function severity(int $severity): self
    {
        $this->parts[] = 'severity >= :severity';
        $this->parameters['severity'] = $severity . '%';

        return $this;
    }

    public function facility(int $facility): self
    {
        $this->parts[] = 'facility = :facility';
        $this->parameters['facility'] = $facility;

        return $this;
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function userNickLike(string $nick): self
    {
        $this->parts[] = 'users.user_nick LIKE :userNickLike';
        $this->parameters['userNickLike'] = '%' . $nick . '%';

        return $this;
    }

    public function allianceId(int $allianceId): self
    {
        $this->parts[] = 'alliance_id = :allianceId';
        $this->parameters['allianceId'] = $allianceId;

        return $this;
    }

    public function allianceNameLike(string $allianceName): self
    {
        $this->parts[] = 'alliances.alliance_name LIKE :allianceNameLike';
        $this->parameters['allianceNameLike'] = '%' . $allianceName . '%';

        return $this;
    }

    public function entityId(int $entityId): self
    {
        $this->parts[] = 'entity_id = :entityId';
        $this->parameters['entityId'] = $entityId;

        return $this;
    }

    public function planetNameLike(string $planetName): self
    {
        $this->parts[] = 'planets.planet_name LIKE :planetNameLike';
        $this->parameters['planetNameLike'] = '%' . $planetName . '%';

        return $this;
    }

    public function objectId(int $objectId): self
    {
        $this->parts[] = 'object_id = :objectId';
        $this->parameters['objectId'] = $objectId;

        return $this;
    }
}
