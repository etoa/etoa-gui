<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use EtoA\Core\Database\DataTransformer;
use EtoA\Message\Report\OtherReportRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OtherReportRepository::class)]
#[ORM\Table(name: 'reports_other')]
class OtherReportData
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'otherReportData', targetEntity: Report::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private ?Report $report = null;

    #[ORM\Column]
    private string $subtype = 'other';

    #[ORM\JoinColumn(name: 'fleet_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Fleet::class)]
    private ?Fleet $fleet = null;

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

    private BaseResources $resources;

    #[ORM\Column(type: Types::TEXT)]
    private string $ships;

    #[ORM\Column]
    private string $action;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $status;

    /*
    public static function createFromArray(array $row): OtherReportData
    {
        $data = new OtherReportData();
        $data->id = (int) $row['id'];
        $data->subtype = $row['subtype'];
        $data->resources = new BaseResources();
        $data->resources->metal = (int) $row['res_0'];
        $data->resources->crystal = (int) $row['res_1'];
        $data->resources->plastic = (int) $row['res_2'];
        $data->resources->fuel = (int) $row['res_3'];
        $data->resources->food = (int) $row['res_4'];
        $data->resources->people = (int) $row['res_5'];
        $data->ships = DataTransformer::dataString($row['ships']);
        $data->action = $row['action'];
        $data->status = (int) $row['status'];
        $data->fleetId = (int) $row['fleet_id'];

        return $data;
    }*/

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

    public function setResMetal(int $resMetal): static
    {
        $this->resMetal = $resMetal;

        return $this;
    }

    public function getResCrystal(): int
    {
        return $this->resCrystal;
    }

    public function setResCrystal(int $resCrystal): static
    {
        $this->resCrystal = $resCrystal;

        return $this;
    }

    public function getResPlastic(): int
    {
        return $this->resPlastic;
    }

    public function setResPlastic(int $resPlastic): static
    {
        $this->resPlastic = $resPlastic;

        return $this;
    }

    public function getResFuel(): int
    {
        return $this->resFuel;
    }

    public function setResFuel(int $resFuel): static
    {
        $this->resFuel = $resFuel;

        return $this;
    }

    public function getResFood(): int
    {
        return $this->resFood;
    }

    public function setResFood(int $resFood): static
    {
        $this->resFood = $resFood;

        return $this;
    }

    public function getResPeople(): int
    {
        return $this->resPeople;
    }

    public function setResPeople(int $resPeople): static
    {
        $this->resPeople = $resPeople;

        return $this;
    }

    public function getShips(): array
    {
        return DataTransformer::dataString($this->ships,Ship::class);
    }

    public function setShips(string $ships): static
    {
        $this->ships = $ships;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

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

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): static
    {
        $this->report = $report;

        return $this;
    }
}
