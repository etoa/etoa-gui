<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Technology\TechnologyListItemRepository;

#[ORM\Entity(repositoryClass: TechnologyListItemRepository::class)]
#[ORM\Table(name: 'techlist')]
class TechnologyListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "techlist_id", type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'techlist_user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy:'techlist')]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'techlist_tech_id', referencedColumnName: 'tech_id')]
    #[ORM\ManyToOne(targetEntity: Technology::class)]
    private ?Technology $technology = null;

    #[ORM\ManyToOne(targetEntity: Entity::class)]
    #[ORM\JoinColumn(name: 'techlist_entity_id', referencedColumnName: 'id')]
    private Entity $entity;

    #[ORM\Column(name: "techlist_current_level", type: "smallint")]
    private int $currentLevel;

    #[ORM\Column(name: "techlist_build_type", type: "smallint")]
    private int $buildType = 0;

    #[ORM\Column(name: "techlist_build_start_time", type: "integer")]
    private int $startTime = 0;

    #[ORM\Column(name: "techlist_build_end_time", type: "integer")]
    private int $endTime = 0;

    #[ORM\Column(name: "techlist_prod_percent", type: "integer")]
    private int $prodPercent = 100;

    public static function empty(): TechnologyListItem
    {
        return new TechnologyListItem();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrentLevel(): ?int
    {
        return $this->currentLevel;
    }

    public function setCurrentLevel(int $currentLevel): static
    {
        $this->currentLevel = $currentLevel;

        return $this;
    }

    public function getBuildType(): ?int
    {
        return $this->buildType;
    }

    public function setBuildType(int $buildType): static
    {
        $this->buildType = $buildType;

        return $this;
    }

    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    public function setStartTime(int $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?int
    {
        return $this->endTime;
    }

    public function setEndTime(int $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getProdPercent(): ?int
    {
        return $this->prodPercent;
    }

    public function setProdPercent(int $prodPercent): static
    {
        $this->prodPercent = $prodPercent;

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

    public function getTechnology(): ?Technology
    {
        return $this->technology;
    }

    public function setTechnology(?Technology $technology): static
    {
        $this->technology = $technology;

        return $this;
    }
}
