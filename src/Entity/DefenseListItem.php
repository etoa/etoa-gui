<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Defense\DefenseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefenseRepository::class)]
#[ORM\Table(name: 'deflist')]
class DefenseListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "deflist_id", type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'deflist_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'deflist_def_id', referencedColumnName: 'def_id')]
    #[ORM\ManyToOne(targetEntity: Defense::class)]
    private ?Defense $defense = null;

    #[ORM\ManyToOne(targetEntity: Planet::class)]
    #[ORM\JoinColumn(name: 'deflist_entity_id', referencedColumnName: 'id')]
    private Planet $entity;

    #[ORM\Column(name: "deflist_count", type: "integer")]
    private int $count = 0;

    public static function createFromData(array $data): DefenseListItem
    {
        $item = new DefenseListItem();
        $item->id = (int) $data['deflist_id'];
        $item->userId = (int) $data['deflist_user_id'];
        $item->defenseId = (int) $data['deflist_def_id'];
        $item->entityId = (int) $data['deflist_entity_id'];
        $item->count = (int) $data['deflist_count'];

        return $item;
    }

    public static function empty(): DefenseListItem
    {
        $item = new DefenseListItem();
        $item->id = 0;
        $item->userId = 0;
        $item->entityId = 0;
        $item->defenseId = 0;
        $item->count = 0;

        return $item;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getEntity(): ?Planet
    {
        return $this->entity;
    }

    public function setEntity(?Planet $entity): static
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
