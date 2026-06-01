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
    private int $id;

    #[ORM\JoinColumn(name: 'missilelist_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy:'missiles')]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'missilelist_entity_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class, inversedBy: 'missilelist')]
    private ?Planet $entity = null;

    #[ORM\JoinColumn(name: 'missilelist_missile_id', referencedColumnName: 'missile_id')]
    #[ORM\ManyToOne(targetEntity: Missile::class)]
    private ?Missile $missile = null;

    #[ORM\Column(name: "missilelist_count", type: "integer")]
    private int $count = 0;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getMissile(): ?Missile
    {
        return $this->missile;
    }

    public function setMissile(?Missile $missile): static
    {
        $this->missile = $missile;

        return $this;
    }
}
