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

    #[ORM\Column(type: "integer")]
    private int $fleetId;

    #[ORM\Column(type: "integer")]
    private int $entityUserId;

    #[ORM\Column]
    private string $action;

    #[ORM\Column(type: "integer")]
    private int $entityFrom;

    #[ORM\Column(type: "integer")]
    private int $entityTo;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    #[ORM\Column(type: "integer")]
    private int $facility;

    #[ORM\Column(type: "integer")]
    private int $severity = 1;

    #[ORM\Column(type: "integer")]
    private int $status;

    #[ORM\Column(name: "launchtime", type: "integer")]
    private int $launchTime;

    #[ORM\Column(name: "landtime", type: "integer")]
    private int $landTime;

    #[ORM\Column]
    private string $fleetShipsStart;

    #[ORM\Column]
    private string $fleetShipsEnd;

    #[ORM\Column]
    private string $entityShipsStart;

    #[ORM\Column]
    private string $entityShipsEnd;

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

    public function getEntityFrom(): ?int
    {
        return $this->entityFrom;
    }

    public function setEntityFrom(int $entityFrom): static
    {
        $this->entityFrom = $entityFrom;

        return $this;
    }

    public function getEntityTo(): ?int
    {
        return $this->entityTo;
    }

    public function setEntityTo(int $entityTo): static
    {
        $this->entityTo = $entityTo;

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

    public function getFleetShipsStart(): string
    {
        return $this->fleetShipsStart;
    }

    public function setFleetShipsStart(string $fleetShipsStart): static
    {
        $this->fleetShipsStart = $fleetShipsStart;

        return $this;
    }

    public function getFleetShipsEnd(): string
    {
        return $this->fleetShipsEnd;
    }

    public function setFleetShipsEnd(string $fleetShipsEnd): static
    {
        $this->fleetShipsEnd = $fleetShipsEnd;

        return $this;
    }

    public function getEntityShipsStart(): string
    {
        return $this->entityShipsStart;
    }

    public function setEntityShipsStart(string $entityShipsStart): static
    {
        $this->entityShipsStart = $entityShipsStart;

        return $this;
    }

    public function getEntityShipsEnd(): string
    {
        return $this->entityShipsEnd;
    }

    public function setEntityShipsEnd(string $entityShipsEnd): static
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

    public function getFleetId(): ?int
    {
        return $this->fleetId;
    }

    public function setFleetId(int $fleetId): static
    {
        $this->fleetId = $fleetId;

        return $this;
    }

    public function getEntityUserId(): ?int
    {
        return $this->entityUserId;
    }

    public function setEntityUserId(int $entityUserId): static
    {
        $this->entityUserId = $entityUserId;

        return $this;
    }
}
