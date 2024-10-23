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

    #[ORM\Column(type: "json")]
    private BaseResources $supply;

    #[ORM\Column(type: "json")]
    private BaseResources $demand;

    #[ORM\Column(type: "json")]
    private PreciseResources $rate;

    public function __construct()
    {
        $this->supply = new BaseResources();
        $this->demand = new BaseResources();
        $this->rate = new PreciseResources();
    }

    public static function createFromArray(array $data): MarketRate
    {
        $rate = new MarketRate();
        $rate->id = (int) $data['id'];
        $rate->timestamp = (int) $data['timestamp'];
        $rate->supply->metal = (int) $data['supply_0'];
        $rate->supply->crystal = (int) $data['supply_1'];
        $rate->supply->plastic = (int) $data['supply_2'];
        $rate->supply->fuel = (int) $data['supply_3'];
        $rate->supply->food = (int) $data['supply_4'];
        $rate->supply->people = (int) $data['supply_5'];
        $rate->demand->metal = (int) $data['demand_0'];
        $rate->demand->crystal = (int) $data['demand_1'];
        $rate->demand->plastic = (int) $data['demand_2'];
        $rate->demand->fuel = (int) $data['demand_3'];
        $rate->demand->food = (int) $data['demand_4'];
        $rate->demand->people = (int) $data['demand_5'];
        $rate->rate->metal = (float) $data['rate_0'];
        $rate->rate->crystal = (float) $data['rate_1'];
        $rate->rate->plastic = (float) $data['rate_2'];
        $rate->rate->fuel = (float) $data['rate_3'];
        $rate->rate->food = (float) $data['rate_4'];
        $rate->rate->people = (float) $data['rate_5'];

        return $rate;
    }

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

    public function getSupply(): array
    {
        return $this->supply;
    }

    public function setSupply(array $supply): static
    {
        $this->supply = $supply;

        return $this;
    }

    public function getDemand(): array
    {
        return $this->demand;
    }

    public function setDemand(array $demand): static
    {
        $this->demand = $demand;

        return $this;
    }

    public function getRate(): array
    {
        return $this->rate;
    }

    public function setRate(array $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
}
