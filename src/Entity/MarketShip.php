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

    #[ORM\Column(type: "integer")]
    private int $userId;

    #[ORM\Column(type: "integer")]
    private int $entityId;

    #[ORM\Column(type: "integer")]
    private int $shipId;

    #[ORM\Column(type: "integer")]
    private int $count;

    #[ORM\Column(type: "integer")]
    private int $costs0;

    #[ORM\Column(type: "integer")]
    private int $costs1;

    #[ORM\Column(type: "integer")]
    private int $costs2;

    #[ORM\Column(type: "integer")]
    private int $costs3;

    #[ORM\Column(type: "integer")]
    private int $costs4;

    #[ORM\Column(type: "integer")]
    private int $buyerId;

    #[ORM\Column(type: "integer")]
    private int $buyerEntityId;

    #[ORM\Column(type: "integer")]
    private int $forUserId;

    #[ORM\Column(type: "integer")]
    private int $forAllianceId;

    #[ORM\Column(type: "boolean")]
    private bool $buyable;

    #[ORM\Column]
    private string $text;

    #[ORM\Column(type: "integer")]
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

    public function getShipId(): ?int
    {
        return $this->shipId;
    }

    public function setShipId(int $shipId): static
    {
        $this->shipId = $shipId;

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

    public function getBuyerId(): ?int
    {
        return $this->buyerId;
    }

    public function setBuyerId(int $buyerId): static
    {
        $this->buyerId = $buyerId;

        return $this;
    }

    public function getBuyerEntityId(): ?int
    {
        return $this->buyerEntityId;
    }

    public function setBuyerEntityId(int $buyerEntityId): static
    {
        $this->buyerEntityId = $buyerEntityId;

        return $this;
    }

    public function getForUserId(): ?int
    {
        return $this->forUserId;
    }

    public function setForUserId(int $forUserId): static
    {
        $this->forUserId = $forUserId;

        return $this;
    }

    public function getForAllianceId(): ?int
    {
        return $this->forAllianceId;
    }

    public function setForAllianceId(int $forAllianceId): static
    {
        $this->forAllianceId = $forAllianceId;

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
}
