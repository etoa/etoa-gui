<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Core\Database\DataTransformer;
use EtoA\Log\FleetLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FleetLogRepository::class)]
#[ORM\Table(name: 'logs_fleet')]
class FleetLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user;

    #[ORM\JoinColumn(name: 'fleet_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Fleet::class)]
    private ?Fleet $fleet;

    #[ORM\JoinColumn(name: 'entity_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $entityUser;

    #[ORM\Column]
    private string $action;

    #[ORM\JoinColumn(name: 'entity_from', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    private ?Entity $entityFrom;

    #[ORM\JoinColumn(name: 'entity_to', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    private ?Entity $entityTo;

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

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getEntityFrom(): Entity
    {
        return $this->entityFrom;
    }

    public function setEntityFrom(Entity $entityFrom): static
    {
        $this->entityFrom = $entityFrom;

        return $this;
    }

    public function getEntityTo(): Entity
    {
        return $this->entityTo;
    }

    public function setEntityTo(Entity $entityTo): static
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

    public function getFleetShipsStart(): array
    {
        return DataTransformer::dataString($this->fleetShipsStart,Ship::class);
    }

    public function setFleetShipsStart(string $fleetShipsStart): static
    {
        $this->fleetShipsStart = $fleetShipsStart;

        return $this;
    }

    public function getFleetShipsEnd(): array
    {
        return DataTransformer::dataString($this->fleetShipsEnd,Ship::class);
    }

    public function setFleetShipsEnd(string $fleetShipsEnd): static
    {
        $this->fleetShipsEnd = $fleetShipsEnd;

        return $this;
    }

    public function getEntityShipsStart(): array
    {
        return DataTransformer::dataString($this->entityShipsStart,Ship::class);
    }

    public function setEntityShipsStart(string $entityShipsStart): static
    {
        $this->entityShipsStart = $entityShipsStart;

        return $this;
    }

    public function getEntityShipsEnd(): array
    {
        return DataTransformer::dataString($this->entityShipsEnd,Ship::class);
    }

    public function setEntityShipsEnd(string $entityShipsEnd): static
    {
        $this->entityShipsEnd = $entityShipsEnd;

        return $this;
    }

    public function getFleetResStart(): ?array
    {
        return DataTransformer::ressourceString($this->fleetResStart);
    }

    public function setFleetResStart(string $fleetResStart): static
    {
        $this->fleetResStart = $fleetResStart;

        return $this;
    }

    public function getFleetResEnd(): ?array
    {
        return DataTransformer::ressourceString($this->fleetResEnd);
    }

    public function setFleetResEnd(string $fleetResEnd): static
    {
        $this->fleetResEnd = $fleetResEnd;

        return $this;
    }

    public function getEntityResStart(): ?array
    {
        return DataTransformer::ressourceString($this->entityResStart);
    }

    public function setEntityResStart(string $entityResStart): static
    {
        $this->entityResStart = $entityResStart;

        return $this;
    }

    public function getEntityResEnd(): ?array
    {
        return DataTransformer::ressourceString($this->entityResEnd);
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getFleet(): ?Fleet
    {
        return $this->fleet;
    }

    public function setFleet(?Fleet $fleet): static
    {
        $this->fleet = $fleet;

        return $this;
    }

    public function getEntityUser(): ?User
    {
        return $this->entityUser;
    }

    public function setEntityUser(?User $entityUser): static
    {
        $this->entityUser = $entityUser;

        return $this;
    }
}
