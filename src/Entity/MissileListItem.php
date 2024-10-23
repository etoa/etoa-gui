<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Missile\MissileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissileRepository::class)]
#[ORM\Table(name: 'missilelist')]
class MissileListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "missilelist_id", type: "integer")]
    private int $id = 0;

    #[ORM\Column(name: "missilelist_user_id", type: "integer")]
    private int $userId = 0;

    #[ORM\Column(name: "missilelist_entity_id", type: "integer")]
    private int $entityId = 0;

    #[ORM\Column(name: "missilelist_missile_id", type: "integer")]
    private int $missileId = 0;

    #[ORM\Column(name: "missilelist_count", type: "integer")]
    private int $count = 0;

    public static function createFromArray(array $data): MissileListItem
    {
        $item = new MissileListItem();
        $item->id = (int) $data['missilelist_id'];
        $item->userId = (int) $data['missilelist_user_id'];
        $item->entityId = (int) $data['missilelist_entity_id'];
        $item->missileId = (int) $data['missilelist_missile_id'];
        $item->count = (int) $data['missilelist_count'];

        return $item;
    }

    public static function empty(): MissileListItem
    {
        $item = new MissileListItem();
        $item->id = 0;
        $item->userId = 0;
        $item->entityId = 0;
        $item->missileId = 0;
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

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getMissileId(): ?int
    {
        return $this->missileId;
    }

    public function setMissileId(int $missileId): static
    {
        $this->missileId = $missileId;

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
