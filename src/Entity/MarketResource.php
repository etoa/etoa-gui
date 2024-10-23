<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Market\MarketResourceRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketResourceRepository::class)]
class MarketResource
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
    private int $sell0;

    #[ORM\Column(type: "integer")]
    private int $sell1;

    #[ORM\Column(type: "integer")]
    private int $sell2;

    #[ORM\Column(type: "integer")]
    private int $sell3;

    #[ORM\Column(type: "integer")]
    private int $sell4;

    #[ORM\Column(type: "integer")]
    private int $buy0;

    #[ORM\Column(type: "integer")]
    private int $buy1;

    #[ORM\Column(type: "integer")]
    private int $buy2;

    #[ORM\Column(type: "integer")]
    private int $buy3;

    #[ORM\Column(type: "integer")]
    private int $buy4;

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

    public function getSellResources(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->sell0;
        $resources->crystal = $this->sell1;
        $resources->plastic = $this->sell2;
        $resources->fuel = $this->sell3;
        $resources->food = $this->sell4;

        return $resources;
    }

    public function getBuyResources(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->buy0;
        $resources->crystal = $this->buy1;
        $resources->plastic = $this->buy2;
        $resources->fuel = $this->buy3;
        $resources->food = $this->buy4;

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

    public function getSell0(): ?int
    {
        return $this->sell0;
    }

    public function setSell0(int $sell0): static
    {
        $this->sell0 = $sell0;

        return $this;
    }

    public function getSell1(): ?int
    {
        return $this->sell1;
    }

    public function setSell1(int $sell1): static
    {
        $this->sell1 = $sell1;

        return $this;
    }

    public function getSell2(): ?int
    {
        return $this->sell2;
    }

    public function setSell2(int $sell2): static
    {
        $this->sell2 = $sell2;

        return $this;
    }

    public function getSell3(): ?int
    {
        return $this->sell3;
    }

    public function setSell3(int $sell3): static
    {
        $this->sell3 = $sell3;

        return $this;
    }

    public function getSell4(): ?int
    {
        return $this->sell4;
    }

    public function setSell4(int $sell4): static
    {
        $this->sell4 = $sell4;

        return $this;
    }

    public function getBuy0(): ?int
    {
        return $this->buy0;
    }

    public function setBuy0(int $buy0): static
    {
        $this->buy0 = $buy0;

        return $this;
    }

    public function getBuy1(): ?int
    {
        return $this->buy1;
    }

    public function setBuy1(int $buy1): static
    {
        $this->buy1 = $buy1;

        return $this;
    }

    public function getBuy2(): ?int
    {
        return $this->buy2;
    }

    public function setBuy2(int $buy2): static
    {
        $this->buy2 = $buy2;

        return $this;
    }

    public function getBuy3(): ?int
    {
        return $this->buy3;
    }

    public function setBuy3(int $buy3): static
    {
        $this->buy3 = $buy3;

        return $this;
    }

    public function getBuy4(): ?int
    {
        return $this->buy4;
    }

    public function setBuy4(int $buy4): static
    {
        $this->buy4 = $buy4;

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
