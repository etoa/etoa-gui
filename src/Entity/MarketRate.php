<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Market\MarketRateRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\PreciseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketRateRepository::class)]
#[ORM\Table(name: 'market_rates')]
class MarketRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "integer")]
    private int $timestamp = 0;

    #[ORM\Column(name: 'supply_0', type: "integer")]
    private int $supplyMetal = 0;

    #[ORM\Column(name: 'supply_1', type: "integer")]
    private int $supplyCrystal = 0;

    #[ORM\Column(name: 'supply_2', type: "integer")]
    private int $supplyPlastic = 0;

    #[ORM\Column(name: 'supply_3', type: "integer")]
    private int $supplyFuel = 0;

    #[ORM\Column(name: 'supply_4', type: "integer")]
    private int $supplyFood = 0;

    #[ORM\Column(name: 'supply_5', type: "integer")]
    private int $supplyPeople = 0;

    #[ORM\Column(name: 'demand_0', type: "integer")]
    private int $demandMetal = 0;

    #[ORM\Column(name: 'demand_1', type: "integer")]
    private int $demandCrystal = 0;

    #[ORM\Column(name: 'demand_2', type: "integer")]
    private int $demandPlastic = 0;

    #[ORM\Column(name: 'demand_3', type: "integer")]
    private int $demandFuel = 0;

    #[ORM\Column(name: 'demand_4', type: "integer")]
    private int $demandFood = 0;

    #[ORM\Column(name: 'demand_5', type: "integer")]
    private int $demandPeople = 0;

    #[ORM\Column(name: 'rate_0', type: "float")]
    private float $rateMetal = 1;

    #[ORM\Column(name: 'rate_1', type: "float")]
    private float $rateCrystal = 1;

    #[ORM\Column(name: 'rate_2', type: "float")]
    private float $ratePlastic = 1;

    #[ORM\Column(name: 'rate_3', type: "float")]
    private float $rateFuel = 1;

    #[ORM\Column(name: 'rate_4', type: "float")]
    private float $rateFood = 1;

    #[ORM\Column(name: 'rate_5', type: "float")]
    private float $ratePeople = 1;

    private BaseResources $supply;
    private BaseResources $demand;
    private PreciseResources $rate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getSupply(): BaseResources
    {
        $this->supply = new BaseResources();
        $this->supply->metal = $this->supplyMetal;
        $this->supply->crystal = $this->supplyCrystal;
        $this->supply->plastic = $this->supplyPlastic;
        $this->supply->fuel = $this->supplyFuel;
        $this->supply->food = $this->supplyFood;
        $this->supply->people = $this->supplyPeople;

        return $this->supply;
    }

    public function setSupply(BaseResources $supply): static
    {
        $this->setSupplyMetal($supply->metal);
        $this->setSupplyCrystal($supply->crystal);
        $this->setSupplyPlastic($supply->plastic);
        $this->setSupplyFuel($supply->fuel);
        $this->setSupplyFood($supply->food);
        $this->setSupplyPeople($supply->people);

        $this->supply = $supply;
    }

    public function getDemand(): BaseResources
    {
        $this->demand = new BaseResources();
        $this->demand->metal = $this->demandMetal;
        $this->demand->crystal = $this->demandCrystal;
        $this->demand->plastic = $this->demandPlastic;
        $this->demand->fuel = $this->demandFuel;
        $this->demand->food = $this->demandFood;
        $this->demand->people = $this->demandPeople;

        return $this->demand;
    }

    public function setDemand(BaseResources $demand): void
    {
        $this->setDemandMetal($demand->metal);
        $this->setDemandCrystal($demand->crystal);
        $this->setDemandPlastic($demand->plastic);
        $this->setDemandFuel($demand->fuel);
        $this->setDemandFood($demand->food);
        $this->setDemandPeople($demand->people);

        $this->demand = $demand;
    }

    public function getRate(): PreciseResources
    {
        $this->rate = new PreciseResources();
        $this->rate->metal = $this->rateMetal;
        $this->rate->crystal = $this->rateCrystal;
        $this->rate->plastic = $this->ratePlastic;
        $this->rate->fuel = $this->rateFuel;
        $this->rate->food = $this->rateFood;
        $this->rate->people = $this->ratePeople;

        return $this->rate;
    }

    public function setRate(PreciseResources $rate): static
    {
        $this->setRateMetal($rate->metal);
        $this->setRateCrystal($rate->crystal);
        $this->setRatePlastic($rate->plastic);
        $this->setRateFuel($rate->fuel);
        $this->setRateFood($rate->food);
        $this->setRatePeople($rate->people);

        $this->rate = $rate;
    }

    public function getSupplyMetal(): ?int
    {
        return $this->supplyMetal;
    }

    public function setSupplyMetal(int $supplyMetal): static
    {
        $this->supplyMetal = $supplyMetal;

        return $this;
    }

    public function getSupplyCrystal(): ?int
    {
        return $this->supplyCrystal;
    }

    public function setSupplyCrystal(int $supplyCrystal): static
    {
        $this->supplyCrystal = $supplyCrystal;

        return $this;
    }

    public function getSupplyPlastic(): ?int
    {
        return $this->supplyPlastic;
    }

    public function setSupplyPlastic(int $supplyPlastic): static
    {
        $this->supplyPlastic = $supplyPlastic;

        return $this;
    }

    public function getSupplyFuel(): ?int
    {
        return $this->supplyFuel;
    }

    public function setSupplyFuel(int $supplyFuel): static
    {
        $this->supplyFuel = $supplyFuel;

        return $this;
    }

    public function getSupplyFood(): ?int
    {
        return $this->supplyFood;
    }

    public function setSupplyFood(int $supplyFood): static
    {
        $this->supplyFood = $supplyFood;

        return $this;
    }

    public function getSupplyPeople(): ?int
    {
        return $this->supplyPeople;
    }

    public function setSupplyPeople(int $supplyPeople): static
    {
        $this->supplyPeople = $supplyPeople;

        return $this;
    }

    public function getDemandMetal(): ?int
    {
        return $this->demandMetal;
    }

    public function setDemandMetal(int $demandMetal): static
    {
        $this->demandMetal = $demandMetal;

        return $this;
    }

    public function getDemandCrystal(): ?int
    {
        return $this->demandCrystal;
    }

    public function setDemandCrystal(int $demandCrystal): static
    {
        $this->demandCrystal = $demandCrystal;

        return $this;
    }

    public function getDemandPlastic(): ?int
    {
        return $this->demandPlastic;
    }

    public function setDemandPlastic(int $demandPlastic): static
    {
        $this->demandPlastic = $demandPlastic;

        return $this;
    }

    public function getDemandFuel(): ?int
    {
        return $this->demandFuel;
    }

    public function setDemandFuel(int $demandFuel): static
    {
        $this->demandFuel = $demandFuel;

        return $this;
    }

    public function getDemandFood(): ?int
    {
        return $this->demandFood;
    }

    public function setDemandFood(int $demandFood): static
    {
        $this->demandFood = $demandFood;

        return $this;
    }

    public function getDemandPeople(): ?int
    {
        return $this->demandPeople;
    }

    public function setDemandPeople(int $demandPeople): static
    {
        $this->demandPeople = $demandPeople;

        return $this;
    }

    public function getRateMetal(): ?float
    {
        return $this->rateMetal;
    }

    public function setRateMetal(float $rateMetal): static
    {
        $this->rateMetal = $rateMetal;

        return $this;
    }

    public function getRateCrystal(): ?float
    {
        return $this->rateCrystal;
    }

    public function setRateCrystal(float $rateCrystal): static
    {
        $this->rateCrystal = $rateCrystal;

        return $this;
    }

    public function getRatePlastic(): ?float
    {
        return $this->ratePlastic;
    }

    public function setRatePlastic(float $ratePlastic): static
    {
        $this->ratePlastic = $ratePlastic;

        return $this;
    }

    public function getRateFuel(): ?float
    {
        return $this->rateFuel;
    }

    public function setRateFuel(float $rateFuel): static
    {
        $this->rateFuel = $rateFuel;

        return $this;
    }

    public function getRateFood(): ?float
    {
        return $this->rateFood;
    }

    public function setRateFood(float $rateFood): static
    {
        $this->rateFood = $rateFood;

        return $this;
    }

    public function getRatePeople(): ?float
    {
        return $this->ratePeople;
    }

    public function setRatePeople(float $ratePeople): static
    {
        $this->ratePeople = $ratePeople;

        return $this;
    }
}
