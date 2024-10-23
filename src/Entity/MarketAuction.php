<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Market\MarketAuctionRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketAuctionRepository::class)]
class MarketAuction
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
    private int $dateStart;

    #[ORM\Column(type: "integer")]
    private int $dateEnd;

    #[ORM\Column(type: "integer")]
    private int $deleted;

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
    private int $shipId;

    #[ORM\Column(type: "integer")]
    private int $shipCount;

    #[ORM\Column]
    private string $text;

    #[ORM\Column(type: "integer")]
    private int $currency0;

    #[ORM\Column(type: "integer")]
    private int $currency1;

    #[ORM\Column(type: "integer")]
    private int $currency2;

    #[ORM\Column(type: "integer")]
    private int $currency3;

    #[ORM\Column(type: "integer")]
    private int $currency4;

    #[ORM\Column(type: "integer")]
    private int $currentBuyerId;

    #[ORM\Column(type: "integer")]
    private int $currentBuyerEntityId;

    #[ORM\Column(type: "integer")]
    private int $currentBuyerDate;

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
    private int $bidCount;

    #[ORM\Column(type: "boolean")]
    private bool $buyable;

    #[ORM\Column(type: "integer")]
    private int $sent;

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

    public function getCurrencyResources(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->currency0;
        $resources->crystal = $this->currency1;
        $resources->plastic = $this->currency2;
        $resources->fuel = $this->currency3;
        $resources->food = $this->currency4;

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

    public function getDateStart(): ?int
    {
        return $this->dateStart;
    }

    public function setDateStart(int $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?int
    {
        return $this->dateEnd;
    }

    public function setDateEnd(int $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDeleted(): ?int
    {
        return $this->deleted;
    }

    public function setDeleted(int $deleted): static
    {
        $this->deleted = $deleted;

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

    public function getShipId(): ?int
    {
        return $this->shipId;
    }

    public function setShipId(int $shipId): static
    {
        $this->shipId = $shipId;

        return $this;
    }

    public function getShipCount(): ?int
    {
        return $this->shipCount;
    }

    public function setShipCount(int $shipCount): static
    {
        $this->shipCount = $shipCount;

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

    public function getCurrency0(): ?int
    {
        return $this->currency0;
    }

    public function setCurrency0(int $currency0): static
    {
        $this->currency0 = $currency0;

        return $this;
    }

    public function getCurrency1(): ?int
    {
        return $this->currency1;
    }

    public function setCurrency1(int $currency1): static
    {
        $this->currency1 = $currency1;

        return $this;
    }

    public function getCurrency2(): ?int
    {
        return $this->currency2;
    }

    public function setCurrency2(int $currency2): static
    {
        $this->currency2 = $currency2;

        return $this;
    }

    public function getCurrency3(): ?int
    {
        return $this->currency3;
    }

    public function setCurrency3(int $currency3): static
    {
        $this->currency3 = $currency3;

        return $this;
    }

    public function getCurrency4(): ?int
    {
        return $this->currency4;
    }

    public function setCurrency4(int $currency4): static
    {
        $this->currency4 = $currency4;

        return $this;
    }

    public function getCurrentBuyerId(): ?int
    {
        return $this->currentBuyerId;
    }

    public function setCurrentBuyerId(int $currentBuyerId): static
    {
        $this->currentBuyerId = $currentBuyerId;

        return $this;
    }

    public function getCurrentBuyerEntityId(): ?int
    {
        return $this->currentBuyerEntityId;
    }

    public function setCurrentBuyerEntityId(int $currentBuyerEntityId): static
    {
        $this->currentBuyerEntityId = $currentBuyerEntityId;

        return $this;
    }

    public function getCurrentBuyerDate(): ?int
    {
        return $this->currentBuyerDate;
    }

    public function setCurrentBuyerDate(int $currentBuyerDate): static
    {
        $this->currentBuyerDate = $currentBuyerDate;

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

    public function getBidCount(): ?int
    {
        return $this->bidCount;
    }

    public function setBidCount(int $bidCount): static
    {
        $this->bidCount = $bidCount;

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

    public function getSent(): ?int
    {
        return $this->sent;
    }

    public function setSent(int $sent): static
    {
        $this->sent = $sent;

        return $this;
    }
}
