<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Market\MarketShipRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketShipRepository::class)]
class MarketShip
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy:'marketShips')]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'entity_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class)]
    private Planet $entity;

    #[ORM\JoinColumn(name: 'ship_id', referencedColumnName: 'ship_id')]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    private Ship $ship;

    #[ORM\Column(type: "integer")]
    private int $count;

    #[ORM\Column(name: "costs_0", type: "integer")]
    private int $costs0;

    #[ORM\Column(name: "costs_1", type: "integer")]
    private int $costs1;

    #[ORM\Column(name: "costs_2", type: "integer")]
    private int $costs2;

    #[ORM\Column(name: "costs_3", type: "integer")]
    private int $costs3;

    #[ORM\Column(name: "costs_4", type: "integer")]
    private int $costs4;

    #[ORM\JoinColumn(name: 'buyer_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $buyer = null;

    #[ORM\JoinColumn(name: 'buyer_entity_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class)]
    private Planet $buyerEntity;

    #[ORM\JoinColumn(name: 'for_user', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $forUser = null;

    #[ORM\JoinColumn(name: 'for_alliance', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private ?Alliance $forAlliance = null;

    #[ORM\Column(type: "boolean")]
    private bool $buyable = true;

    #[ORM\Column]
    private string $text = '';

    #[ORM\Column(name: "datum", type: "integer")]
    private int $date;

    public function getCosts(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->costs0;
        $resources->crystal = $this->costs1;
        $resources->plastic = $this->costs2;
        $resources->fuel = $this->costs3;
        $resources->food = $this->costs4;

        return $resources;
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

    public function getCosts0(): ?int
    {
        return $this->costs0;
    }

    public function setCosts0(int $costs0): static
    {
        $this->costs0 = $costs0;

        return $this;
    }

    public function getCosts1(): ?int
    {
        return $this->costs1;
    }

    public function setCosts1(int $costs1): static
    {
        $this->costs1 = $costs1;

        return $this;
    }

    public function getCosts2(): ?int
    {
        return $this->costs2;
    }

    public function setCosts2(int $costs2): static
    {
        $this->costs2 = $costs2;

        return $this;
    }

    public function getCosts3(): ?int
    {
        return $this->costs3;
    }

    public function setCosts3(int $costs3): static
    {
        $this->costs3 = $costs3;

        return $this;
    }

    public function getCosts4(): ?int
    {
        return $this->costs4;
    }

    public function setCosts4(int $costs4): static
    {
        $this->costs4 = $costs4;

        return $this;
    }

    public function isBuyable(): ?bool
    {
        return $this->buyable;
    }

    public function setBuyable(bool $buyable): static
    {
        $this->buyable = $buyable;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): static
    {
        $this->date = $date;

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

    public function getShip(): ?Ship
    {
        return $this->ship;
    }

    public function setShip(?Ship $ship): static
    {
        $this->ship = $ship;

        return $this;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getBuyerEntity(): ?Planet
    {
        return $this->buyerEntity;
    }

    public function setBuyerEntity(?Planet $buyerEntity): static
    {
        $this->buyerEntity = $buyerEntity;

        return $this;
    }

    public function getForUser(): ?User
    {
        return $this->forUser;
    }

    public function setForUser(?User $forUser): static
    {
        $this->forUser = $forUser;

        return $this;
    }

    public function getForAlliance(): ?Alliance
    {
        return $this->forAlliance;
    }

    public function setForAlliance(?Alliance $forAlliance): static
    {
        $this->forAlliance = $forAlliance;

        return $this;
    }
}
