<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use EtoA\Core\Database\DataTransformer;
use EtoA\Message\Report\SpyReportRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpyReportRepository::class)]
#[ORM\Table(name: 'reports_spy')]
class SpyReportData
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $subtype;

    #[ORM\JoinColumn(name: 'fleet_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Fleet::class)]
    private ?Fleet $fleet = null;

    private BaseResources $resources;

    #[ORM\Column(name: "res_0", type: Types::BIGINT)]
    private int $resMetal;

    #[ORM\Column(name: "res_1", type: Types::BIGINT)]
    private int $resCrystal;

    #[ORM\Column(name: "res_2", type: Types::BIGINT)]
    private int $resPlastic;

    #[ORM\Column(name: "res_3", type: Types::BIGINT)]
    private int $resFuel;

    #[ORM\Column(name: "res_4", type: Types::BIGINT)]
    private int $resFood;

    #[ORM\Column(name: "res_5", type: Types::BIGINT)]
    private int $resPeople;

    #[ORM\Column]
    private string $ships;

    #[ORM\Column]
    private string $defense;

    #[ORM\Column]
    private string $buildings;

    #[ORM\Column]
    private string $technologies;

    #[ORM\Column(name: "spydefense")]
    private int $spyDefense;

    #[ORM\Column]
    private int $coverage;

    private bool $showShips;

    private bool $showBuildings;

    private bool $showTechnologies;

    private bool $showDefense;

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

    public function getResMetal(): int
    {
        return $this->resMetal;
    }

    public function setResMetal($resMetal): static
    {
        $this->resMetal = $resMetal;

        return $this;
    }

    public function getResCrystal(): int
    {
        return $this->resCrystal;
    }

    public function setResCrystal($resCrystal): static
    {
        $this->resCrystal = $resCrystal;

        return $this;
    }

    public function getResPlastic(): int
    {
        return $this->resPlastic;
    }

    public function setResPlastic($resPlastic): static
    {
        $this->resPlastic = $resPlastic;

        return $this;
    }

    public function getResFuel(): int
    {
        return $this->resFuel;
    }

    public function setResFuel($resFuel): static
    {
        $this->resFuel = $resFuel;

        return $this;
    }

    public function getResFood(): int
    {
        return $this->resFood;
    }

    public function setResFood($resFood): static
    {
        $this->resFood = $resFood;

        return $this;
    }

    public function getResPeople(): int
    {
        return $this->resPeople;
    }

    public function setResPeople($resPeople): static
    {
        $this->resPeople = $resPeople;

        return $this;
    }

    public function getShips(): array
    {
        return DataTransformer::dataString($this->ships, Ship::class);
    }

    public function setShips(string $ships): static
    {
        $this->ships = $ships;

        return $this;
    }

    public function getDefense(): array
    {
        return DataTransformer::dataString($this->defense, Defense::class);
    }

    public function setDefense(string $defense): static
    {
        $this->defense = $defense;

        return $this;
    }

    public function getBuildings(): array
    {
        return DataTransformer::dataString($this->buildings, Building::class);
    }

    public function setBuildings(string $buildings): static
    {
        $this->buildings = $buildings;

        return $this;
    }

    public function getTechnologies(): array
    {
        return DataTransformer::dataString($this->technologies, Technology::class);
    }

    public function setTechnologies(string $technologies): static
    {
        $this->technologies = $technologies;

        return $this;
    }

    public function getSpyDefense(): ?int
    {
        return $this->spyDefense;
    }

    public function setSpyDefense(int $spyDefense): static
    {
        $this->spyDefense = $spyDefense;

        return $this;
    }

    public function getCoverage(): ?int
    {
        return $this->coverage;
    }

    public function setCoverage(int $coverage): static
    {
        $this->coverage = $coverage;

        return $this;
    }

    public function getFleet(): ?Fleet
    {
        return $this->fleet;
    }

    public function setFleet(?Fleet $fleet): static
    {
        $this->fleet = $fleet;

        return $this;
    }

    public function getResources(): BaseResources
    {
        $this->resources = new BaseResources();
        $this->resources->metal = $this->getResMetal();
        $this->resources->crystal = $this->getResCrystal();
        $this->resources->plastic = $this->getResPlastic();
        $this->resources->fuel = $this->getResFuel();
        $this->resources->food = $this->getResFood();
        $this->resources->people = $this->getResPeople();

        return $this->resources;
    }

    public function setResources(BaseResources $resources): void
    {
        $this->setResMetal($resources->metal);
        $this->setResCrystal($resources->crystal);
        $this->setResPlastic($resources->plastic);
        $this->setResFuel($resources->fuel);
        $this->setResFood($resources->food);
        $this->setResPeople($resources->people);

        $this->resources = $resources;
    }

    public function isShowShips(): bool
    {
        return $this->ships != '';
    }

    public function isShowBuildings(): bool
    {
        return $this->buildings != '';
    }

    public function isShowTechnologies(): bool
    {
        return $this->technologies != '';
    }

    public function isShowDefense(): bool
    {
        return $this->defense != '';
    }
}
