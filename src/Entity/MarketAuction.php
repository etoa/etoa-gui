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

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy:'marketAuctions')]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'entity_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class)]
    private Planet $entity;

    #[ORM\Column(name: "date_start", type: "integer")]
    private int $dateStart = 0;

    #[ORM\Column(name: "date_end", type: "integer")]
    private int $dateEnd = 0;

    #[ORM\Column(name: "date_delete", type: "integer")]
    private int $deleted = 0;

    #[ORM\Column('sell_0', type: "integer")]
    private int $sell0 = 0;

    #[ORM\Column('sell_1', type: "integer")]
    private int $sell1 = 0;

    #[ORM\Column('sell_2', type: "integer")]
    private int $sell2 = 0;

    #[ORM\Column('sell_3', type: "integer")]
    private int $sell3 = 0;

    #[ORM\Column('sell_4', type: "integer")]
    private int $sell4 = 0;

    #[ORM\JoinColumn(name: 'ship_id', referencedColumnName: 'ship_id')]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    private ?Ship $ship = null;

    #[ORM\Column(type: "integer")]
    private int $shipCount = 0;

    #[ORM\Column]
    private string $text = '';

    #[ORM\Column('currency_0', type: "integer")]
    private int $currency0 = 1;

    #[ORM\Column('currency_1', type: "integer")]
    private int $currency1 = 1;

    #[ORM\Column('currency_2', type: "integer")]
    private int $currency2 = 1;

    #[ORM\Column('currency_3', type: "integer")]
    private int $currency3 = 1;

    #[ORM\Column('currency_4', type: "integer")]
    private int $currency4 = 1;

    #[ORM\JoinColumn(name: 'current_buyer_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $currentBuyer = null;

    #[ORM\JoinColumn(name: 'current_buyer_entity_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class)]
    private Planet $currentBuyerEntity;

    #[ORM\Column(type: "integer")]
    private int $currentBuyerDate;

    #[ORM\Column('buy_0', type: "integer")]
    private int $buy0 = 0;

    #[ORM\Column('buy_1', type: "integer")]
    private int $buy1 = 0;

    #[ORM\Column('buy_2', type: "integer")]
    private int $buy2 = 0;

    #[ORM\Column('buy_3', type: "integer")]
    private int $buy3 = 0;

    #[ORM\Column('buy_4', type: "integer")]
    private int $buy4 = 0;

    #[ORM\Column('bidcount', type: "integer")]
    private int $bidCount = 0;

    #[ORM\Column(type: "boolean")]
    private bool $buyable = true;

    #[ORM\Column(type: "boolean")]
    private bool $sent = false;

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

    public function getSent(): ?bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): static
    {
        $this->sent = $sent;

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

    public function getCurrentBuyer(): ?User
    {
        return $this->currentBuyer;
    }

    public function setCurrentBuyer(?User $currentBuyer): static
    {
        $this->currentBuyer = $currentBuyer;

        return $this;
    }

    public function getCurrentBuyerEntity(): ?Planet
    {
        return $this->currentBuyerEntity;
    }

    public function setCurrentBuyerEntity(?Planet $currentBuyerEntity): static
    {
        $this->currentBuyerEntity = $currentBuyerEntity;

        return $this;
    }

    public function isSent(): ?bool
    {
        return $this->sent;
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
}
