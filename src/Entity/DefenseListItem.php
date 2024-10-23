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

    #[ORM\Column(name: "deflist_user_id", type: "integer")]
    private int $userId;

    #[ORM\Column(name: "deflist_def_id", type: "integer")]
    private int $defenseId;

    #[ORM\Column(name: "deflist_entity_id", type: "integer")]
    private int $entityId;

    #[ORM\Column(name: "deflist_count", type: "integer")]
    private int $count;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getDefenseId(): ?int
    {
        return $this->defenseId;
    }

    public function setDefenseId(int $defenseId): static
    {
        $this->defenseId = $defenseId;

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

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }
}
