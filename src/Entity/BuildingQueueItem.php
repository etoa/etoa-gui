<?php

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use EtoA\Building\BuildingQueueItemRepository;

#[ORM\Entity(repositoryClass: BuildingQueueItemRepository::class)]
#[ORM\Table(name: 'building_queue')]
class BuildingQueueItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'entity_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    private ?Entity $entity = null;

    #[ORM\JoinColumn(name: 'item_id', referencedColumnName: 'building_id')]
    #[ORM\ManyToOne(targetEntity: Building::class)]
    private ?Building $building = null;

    #[ORM\Column]
    private int $timeStart = 0;

    #[ORM\Column]
    private int $timeEnd = 0;

    #[ORM\Column(name: 'level', type: Types::SMALLINT)]
    private int $level = 0;

    #[ORM\Column(type: Types::BIGINT)]
    private int $resMetal = 0;

    #[ORM\Column(type: Types::BIGINT)]
    private int $resCrystal = 0;

    #[ORM\Column(type: Types::BIGINT)]
    private int $resPlastic = 0;

    #[ORM\Column(type: Types::BIGINT)]
    private int $resFuel = 0;

    #[ORM\Column(type: Types::BIGINT)]
    private int $resFood = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeStart(): ?int
    {
        return $this->timeStart;
    }

    public function setTimeStart(int $timeStart): static
    {
        $this->timeStart = $timeStart;

        return $this;
    }

    public function getTimeEnd(): ?int
    {
        return $this->timeEnd;
    }

    public function setTimeEnd(int $timeEnd): static
    {
        $this->timeEnd = $timeEnd;

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

    public function getResMetal(): ?string
    {
        return $this->resMetal;
    }

    public function setResMetal(string $resMetal): static
    {
        $this->resMetal = $resMetal;

        return $this;
    }

    public function getResCrystal(): ?string
    {
        return $this->resCrystal;
    }

    public function setResCrystal(string $resCrystal): static
    {
        $this->resCrystal = $resCrystal;

        return $this;
    }

    public function getResPlastic(): ?string
    {
        return $this->resPlastic;
    }

    public function setResPlastic(string $resPlastic): static
    {
        $this->resPlastic = $resPlastic;

        return $this;
    }

    public function getResFuel(): ?string
    {
        return $this->resFuel;
    }

    public function setResFuel(string $resFuel): static
    {
        $this->resFuel = $resFuel;

        return $this;
    }

    public function getResFood(): ?string
    {
        return $this->resFood;
    }

    public function setResFood(string $resFood): static
    {
        $this->resFood = $resFood;

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

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }
}
