<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Message\MarketReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketReportRepository::class)]
#[ORM\Table(name: 'reports_market')]
class MarketReportData
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $subtype;

    #[ORM\Column]
    private int $recordId;

    #[ORM\JoinColumn(name: 'fleet1_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Fleet::class)]
    private Fleet $fleet1;

    #[ORM\JoinColumn(name: 'fleet2_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Fleet::class)]
    private Fleet $fleet2;

    #[ORM\Column(name:'buy_0')]
    private int $buyMetal;

    #[ORM\Column(name:'buy_1')]
    private int $buyCrystal;

    #[ORM\Column(name:'buy_2')]
    private int $buyPlastic;

    #[ORM\Column(name:'buy_3')]
    private int $buyFuel;

    #[ORM\Column(name:'buy_4')]
    private int $buyFood;

    #[ORM\Column(name:'buy_5')]
    private int $buyPeople;

    #[ORM\Column(name:'sell_0')]
    private int $sellMetal;

    #[ORM\Column(name:'sell_1')]
    private int $sellCrystal;

    #[ORM\Column(name:'sell_2')]
    private int $sellPlastic;

    #[ORM\Column(name:'sell_3')]
    private int $sellFuel;

    #[ORM\Column(name:'sell_4')]
    private int $sellFood;

    #[ORM\Column(name:'sell_5')]
    private int $sellPeople;

    #[ORM\Column]
    private float $factor;

    #[ORM\JoinColumn(name: 'ship_id', referencedColumnName: 'ship_id')]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    private Ship $ship;

    #[ORM\Column]
    private int $shipCount;

    #[ORM\Column]
    private int $timestamp2;

    public static function createFromArray(array $row): MarketReportData
    {
        $data = new MarketReportData();
        $data->id = (int) $row['id'];
        $data->recordId = (int) $row['record_id'];
        $data->subtype = $row['subtype'];
        $data->sellMetal = (int) $row['sell_0'];
        $data->sellCrystal = (int) $row['sell_1'];
        $data->sellPlastic = (int) $row['sell_2'];
        $data->sellFuel = (int) $row['sell_3'];
        $data->sellFood = (int) $row['sell_4'];
        $data->sellPeople = (int) $row['sell_5'];
        $data->buyMetal = (int) $row['buy_0'];
        $data->buyCrystal = (int) $row['buy_1'];
        $data->buyPlastic = (int) $row['buy_2'];
        $data->buyFuel = (int) $row['buy_3'];
        $data->buyFood = (int) $row['buy_4'];
        $data->buyPeople = (int) $row['buy_5'];
        $data->shipId = (int) $row['ship_id'];
        $data->shipCount = (int) $row['ship_count'];
        $data->timestamp2 = (int) $row['timestamp2'];
        $data->fleetId1 = (int) $row['fleet1_id'];
        $data->fleetId2 = (int) $row['fleet2_id'];
        $data->factor = (float) $row['factor'];

        return $data;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    public function setSubtype(string $subtype): static
    {
        $this->subtype = $subtype;

        return $this;
    }

    public function getRecordId(): ?int
    {
        return $this->recordId;
    }

    public function setRecordId(int $recordId): static
    {
        $this->recordId = $recordId;

        return $this;
    }

    public function getBuyMetal(): ?int
    {
        return $this->buyMetal;
    }

    public function setBuyMetal(int $buyMetal): static
    {
        $this->buyMetal = $buyMetal;

        return $this;
    }

    public function getBuyCrystal(): ?int
    {
        return $this->buyCrystal;
    }

    public function setBuyCrystal(int $buyCrystal): static
    {
        $this->buyCrystal = $buyCrystal;

        return $this;
    }

    public function getBuyPlastic(): ?int
    {
        return $this->buyPlastic;
    }

    public function setBuyPlastic(int $buyPlastic): static
    {
        $this->buyPlastic = $buyPlastic;

        return $this;
    }

    public function getBuyFuel(): ?int
    {
        return $this->buyFuel;
    }

    public function setBuyFuel(int $buyFuel): static
    {
        $this->buyFuel = $buyFuel;

        return $this;
    }

    public function getBuyFood(): ?int
    {
        return $this->buyFood;
    }

    public function setBuyFood(int $buyFood): static
    {
        $this->buyFood = $buyFood;

        return $this;
    }

    public function getBuyPeople(): ?int
    {
        return $this->buyPeople;
    }

    public function setBuyPeople(int $buyPeople): static
    {
        $this->buyPeople = $buyPeople;

        return $this;
    }

    public function getSellMetal(): ?int
    {
        return $this->sellMetal;
    }

    public function setSellMetal(int $sellMetal): static
    {
        $this->sellMetal = $sellMetal;

        return $this;
    }

    public function getSellCrystal(): ?int
    {
        return $this->sellCrystal;
    }

    public function setSellCrystal(int $sellCrystal): static
    {
        $this->sellCrystal = $sellCrystal;

        return $this;
    }

    public function getSellPlastic(): ?int
    {
        return $this->sellPlastic;
    }

    public function setSellPlastic(int $sellPlastic): static
    {
        $this->sellPlastic = $sellPlastic;

        return $this;
    }

    public function getSellFuel(): ?int
    {
        return $this->sellFuel;
    }

    public function setSellFuel(int $sellFuel): static
    {
        $this->sellFuel = $sellFuel;

        return $this;
    }

    public function getSellFood(): ?int
    {
        return $this->sellFood;
    }

    public function setSellFood(int $sellFood): static
    {
        $this->sellFood = $sellFood;

        return $this;
    }

    public function getSellPeople(): ?int
    {
        return $this->sellPeople;
    }

    public function setSellPeople(int $sellPeople): static
    {
        $this->sellPeople = $sellPeople;

        return $this;
    }

    public function getFactor(): ?float
    {
        return $this->factor;
    }

    public function setFactor(float $factor): static
    {
        $this->factor = $factor;

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

    public function getTimestamp2(): ?int
    {
        return $this->timestamp2;
    }

    public function setTimestamp2(int $timestamp2): static
    {
        $this->timestamp2 = $timestamp2;

        return $this;
    }

    public function getFleet1(): ?Fleet
    {
        return $this->fleet1;
    }

    public function setFleet1(?Fleet $fleet1): static
    {
        $this->fleet1 = $fleet1;

        return $this;
    }

    public function getFleet2(): ?Fleet
    {
        return $this->fleet2;
    }

    public function setFleet2(?Fleet $fleet2): static
    {
        $this->fleet2 = $fleet2;

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
}
