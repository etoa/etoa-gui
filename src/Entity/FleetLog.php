<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use EtoA\Log\FleetLogRepository;
use EtoA\Universe\Resources\ResourceNames;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FleetLogRepository::class)]
#[ORM\Table(name: 'logs_fleet')]
class FleetLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $userId;

    #[ORM\Column]
    private string $action;

    #[ORM\Column(type: "integer")]
    private int $entityFromId;

    #[ORM\Column(type: "integer")]
    private int $entityToId;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    #[ORM\Column(type: "integer")]
    private int $facility;

    #[ORM\Column(type: "integer")]
    private int $severity;

    #[ORM\Column(type: "integer")]
    private int $status;

    #[ORM\Column(type: "integer")]
    private int $launchTime;

    #[ORM\Column(type: "integer")]
    private int $landTime;

    /** @var array<int, int> */
    #[ORM\Column(type: Types::JSON)]
    private array $fleetShipsStart;

    /** @var array<int, int> */
    #[ORM\Column(type: Types::JSON)]
    private array $fleetShipsEnd;

    /** @var array<int, int> */
    #[ORM\Column(type: Types::JSON)]
    private array $entityShipsStart;

    /** @var array<int, int> */
    #[ORM\Column(type: Types::JSON)]
    private array $entityShipsEnd;

    #[ORM\Column]
    private string $fleetResStart;

    #[ORM\Column]
    private string $fleetResEnd;

    #[ORM\Column]
    private string $entityResStart;

    #[ORM\Column]
    private string $entityResEnd;

    #[ORM\Column]
    private string $message;

    /**
     * @return array<int, int>
     */
    private function transformShips(string $shipString): array
    {
        $ships = [];
        $shipEntries = array_filter(explode(',', $shipString));
        foreach ($shipEntries as $entry) {
            [$shipId, $count] = explode(":", $entry);
            if ($shipId > 0) {
                $ships[(int) $shipId] = (int) $count;
            }
        }

        return $ships;
    }

    /**
     * @return iterable<int, array{0: int, 1: int}>
     */
    public function iterateFleetShips(): iterable
    {
        $shipIds = array_unique(array_merge(array_keys($this->fleetShipsStart), array_keys($this->fleetShipsEnd)));
        foreach ($shipIds as $shipId) {
            yield $shipId => [$this->fleetShipsStart[$shipId] ?? 0, $this->fleetShipsEnd[$shipId] ?? 0];
        }
    }

    /**
     * @return iterable<int, array{0: int, 1: int}>
     */
    public function iterateEntityShips(): iterable
    {
        $shipIds = array_unique(array_merge(array_keys($this->entityShipsStart), array_keys($this->entityShipsEnd)));
        foreach ($shipIds as $shipId) {
            yield $shipId => [$this->entityShipsStart[$shipId] ?? 0, $this->entityShipsEnd[$shipId] ?? 0];
        }
    }

    /**
     * @return iterable<string, array{0: int, 1: int}>
     */
    public function iterateFleetResources(): iterable
    {
        $startResources = explode(":", $this->fleetResStart);
        $endResources = explode(":", $this->fleetResEnd);
        foreach (ResourceNames::NAMES as $k => $v) {
            yield $v => [(int) $startResources[$k], (int) $endResources[$k]];
        }
    }

    /**
     * @return iterable<string, array{0: int, 1: int}>
     */
    public function iterateEntityResources(): iterable
    {
        $startResources = explode(":", $this->entityResStart);
        $endResources = explode(":", $this->entityResEnd);
        foreach (ResourceNames::NAMES as $k => $v) {
            yield $v => [(int) $startResources[$k], (int) $endResources[$k]];
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

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

    public function getEntityFromId(): ?int
    {
        return $this->entityFromId;
    }

    public function setEntityFromId(int $entityFromId): static
    {
        $this->entityFromId = $entityFromId;

        return $this;
    }

    public function getEntityToId(): ?int
    {
        return $this->entityToId;
    }

    public function setEntityToId(int $entityToId): static
    {
        $this->entityToId = $entityToId;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getFacility(): ?int
    {
        return $this->facility;
    }

    public function setFacility(int $facility): static
    {
        $this->facility = $facility;

        return $this;
    }

    public function getSeverity(): ?int
    {
        return $this->severity;
    }

    public function setSeverity(int $severity): static
    {
        $this->severity = $severity;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLaunchTime(): ?int
    {
        return $this->launchTime;
    }

    public function setLaunchTime(int $launchTime): static
    {
        $this->launchTime = $launchTime;

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

    public function getFleetShipsStart(): array
    {
        return $this->fleetShipsStart;
    }

    public function setFleetShipsStart(array $fleetShipsStart): static
    {
        $this->fleetShipsStart = $fleetShipsStart;

        return $this;
    }

    public function getFleetShipsEnd(): array
    {
        return $this->fleetShipsEnd;
    }

    public function setFleetShipsEnd(array $fleetShipsEnd): static
    {
        $this->fleetShipsEnd = $fleetShipsEnd;

        return $this;
    }

    public function getEntityShipsStart(): array
    {
        return $this->entityShipsStart;
    }

    public function setEntityShipsStart(array $entityShipsStart): static
    {
        $this->entityShipsStart = $entityShipsStart;

        return $this;
    }

    public function getEntityShipsEnd(): array
    {
        return $this->entityShipsEnd;
    }

    public function setEntityShipsEnd(array $entityShipsEnd): static
    {
        $this->entityShipsEnd = $entityShipsEnd;

        return $this;
    }

    public function getFleetResStart(): ?string
    {
        return $this->fleetResStart;
    }

    public function setFleetResStart(string $fleetResStart): static
    {
        $this->fleetResStart = $fleetResStart;

        return $this;
    }

    public function getFleetResEnd(): ?string
    {
        return $this->fleetResEnd;
    }

    public function setFleetResEnd(string $fleetResEnd): static
    {
        $this->fleetResEnd = $fleetResEnd;

        return $this;
    }

    public function getEntityResStart(): ?string
    {
        return $this->entityResStart;
    }

    public function setEntityResStart(string $entityResStart): static
    {
        $this->entityResStart = $entityResStart;

        return $this;
    }

    public function getEntityResEnd(): ?string
    {
        return $this->entityResEnd;
    }

    public function setEntityResEnd(string $entityResEnd): static
    {
        $this->entityResEnd = $entityResEnd;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
