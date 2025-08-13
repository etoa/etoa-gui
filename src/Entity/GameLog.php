<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameLogRepository::class)]
#[ORM\Table(name: 'logs_game')]
class GameLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $severity;

    #[ORM\Column(type: "integer")]
    private int $facility;

    #[ORM\Column(name:'object_id', type: "integer")]
    private int $object;

    #[ORM\Column(type: "integer")]
    private int $level;

    #[ORM\Column]
    private string $message;

    #[ORM\Column(type: "integer")]
    private int $status;

    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    private ?Entity $entity = null;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private ?Alliance $alliance = null;

    #[ORM\JoinColumn(name: 'object_id', referencedColumnName: 'building_id')]
    #[ORM\ManyToOne(targetEntity: Building::class)]
    private ?Building $building = null;

    #[ORM\JoinColumn(name: 'object_id', referencedColumnName: 'tech_id')]
    #[ORM\ManyToOne(targetEntity: Technology::class)]
    private ?Technology $technology = null;

    #[ORM\JoinColumn(name: 'object_id', referencedColumnName: 'ship_id')]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    private ?Ship $ship= null;

    #[ORM\JoinColumn(name: 'object_id', referencedColumnName: 'def_id')]
    #[ORM\ManyToOne(targetEntity: Defense::class)]
    private ?Defense $defense = null;

    #[ORM\Column]
    private string $ip;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFacility(): ?int
    {
        return $this->facility;
    }

    public function setFacility(int $facility): static
    {
        $this->facility = $facility;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

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

    public function getObject(): Technology|Building|ship|Defense|null
    {
        return match ($this->facility) {
            GameLogFacility::BUILD => $this->building,
            GameLogFacility::TECH => $this->technology,
            GameLogFacility::SHIP => $this->ship,
            GameLogFacility::DEF => $this->defense,
            default => null,
        };
    }

    public function setObject(int $object): static
    {
        $this->object = $object;

        return $this;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): static
    {
        $this->entity = $entity;

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

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?User $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getTechnology(): ?Technology
    {
        return $this->technology;
    }

    public function setTechnology(?Technology $technology): static
    {
        $this->technology = $technology;

        return $this;
    }

    public function getShip(): ?Ship
    {
        return $this->ship;
    }

    public function setShip(?Ship $ship): static
    {
        $this->ship = $ship;

        return $this;
    }

    public function getDefense(): ?Defense
    {
        return $this->defense;
    }

    public function setDefense(?Defense $defense): static
    {
        $this->defense = $defense;

        return $this;
    }
}
