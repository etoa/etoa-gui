<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Log\BattleLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BattleLogRepository::class)]
#[ORM\Table(name: 'logs_battle')]
class BattleLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(name:"fleet_user_id")]
    private string $fleetUserIds;

    #[ORM\Column(name:"entity_user_id")]
    private string $entityUserIds;

    #[ORM\Column(type: "integer")]
    private int $landTime;

    #[ORM\Column(type: "integer")]
    private int $entityId;

    #[ORM\Column]
    private string $action;

    #[ORM\Column(type: "boolean")]
    private bool $war;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFleetUserIds(): ?string
    {
        return $this->fleetUserIds;
    }

    public function setFleetUserIds(string $fleetUserIds): static
    {
        $this->fleetUserIds = $fleetUserIds;

        return $this;
    }

    public function getEntityUserIds(): ?string
    {
        return $this->entityUserIds;
    }

    public function setEntityUserIds(string $entityUserIds): static
    {
        $this->entityUserIds = $entityUserIds;

        return $this;
    }

    public function getLandTime(): ?int
    {
        return $this->landTime;
    }

    public function setLandTime(int $landTime): static
    {
        $this->landTime = $landTime;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function isWar(): ?bool
    {
        return $this->war;
    }

    public function setWar(bool $war): static
    {
        $this->war = $war;

        return $this;
    }
}
