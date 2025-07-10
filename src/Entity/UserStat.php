<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use EtoA\User\UserStatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserStatRepository::class)]
#[ORM\Table(name: 'user_stats')]
class UserStat
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $nick;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $blocked;

    #[ORM\Column]
    private bool $hmod;

    #[ORM\Column]
    private bool $inactive;

    /** Rank for the current selection */
    #[ORM\Column]
    private int $rank;

    #[ORM\Column]
    private int $rankShips;

    #[ORM\Column]
    private int $rankTech;

    #[ORM\Column]
    private int $rankBuildings;

    #[ORM\Column]
    private int $rankExp;

    /** Points for the current selection */
    #[ORM\Column]
    private int $points;

    /** Shift for the current selection */
    #[ORM\Column(name:"rankshift")]
    private int $shift;

    #[ORM\Column(name:"rankshift_ships")]
    private int $shiftShips;

    #[ORM\Column(name:"rankshift_tech")]
    private int $shiftTechs;

    #[ORM\Column(name:"rankshift_buildings")]
    private int $shiftBuildings;

    #[ORM\Column(name:"rankshift_exp")]
    private int $shiftExp;

    #[ORM\Column]
    private string $raceName;

    #[ORM\JoinColumn(name: 'alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private ?Alliance $alliance = null;

    #[ORM\Column]
    private ?string $allianceTag;

    #[ORM\Column]
    private int $sx;

    #[ORM\Column]
    private int $sy;

    #[ORM\Column(name:"points_ships")]
    private int $shipPoints;

    #[ORM\Column(name:"points_tech")]
    private int $techPoints;

    #[ORM\Column(name:"points_buildings")]
    private int $buildingPoints;

    #[ORM\Column(name:"points_exp")]
    private int $expPoints;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(string $nick): static
    {
        $this->nick = $nick;

        return $this;
    }

    public function isBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): static
    {
        $this->blocked = $blocked;

        return $this;
    }

    public function isHmod(): ?bool
    {
        return $this->hmod;
    }

    public function setHmod(bool $hmod): static
    {
        $this->hmod = $hmod;

        return $this;
    }

    public function isInactive(): ?bool
    {
        return $this->inactive;
    }

    public function setInactive(bool $inactive): static
    {
        $this->inactive = $inactive;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getShift(): ?int
    {
        return $this->shift;
    }

    public function setShift(int $shift): static
    {
        $this->shift = $shift;

        return $this;
    }

    public function getRaceName(): ?string
    {
        return $this->raceName;
    }

    public function setRaceName(string $raceName): static
    {
        $this->raceName = $raceName;

        return $this;
    }

    public function getAllianceTag(): ?string
    {
        return $this->allianceTag;
    }

    public function setAllianceTag(string $allianceTag): static
    {
        $this->allianceTag = $allianceTag;

        return $this;
    }

    public function getSx(): ?int
    {
        return $this->sx;
    }

    public function setSx(int $sx): static
    {
        $this->sx = $sx;

        return $this;
    }

    public function getSy(): ?int
    {
        return $this->sy;
    }

    public function setSy(int $sy): static
    {
        $this->sy = $sy;

        return $this;
    }

    public function getShipPoints(): ?int
    {
        return $this->shipPoints;
    }

    public function setShipPoints(int $shipPoints): static
    {
        $this->shipPoints = $shipPoints;

        return $this;
    }

    public function getTechPoints(): ?int
    {
        return $this->techPoints;
    }

    public function setTechPoints(int $techPoints): static
    {
        $this->techPoints = $techPoints;

        return $this;
    }

    public function getBuildingPoints(): ?int
    {
        return $this->buildingPoints;
    }

    public function setBuildingPoints(int $buildingPoints): static
    {
        $this->buildingPoints = $buildingPoints;

        return $this;
    }

    public function getExpPoints(): ?int
    {
        return $this->expPoints;
    }

    public function setExpPoints(int $expPoints): static
    {
        $this->expPoints = $expPoints;

        return $this;
    }

    public function getShiftShips(): ?int
    {
        return $this->shiftShips;
    }

    public function setShiftShips(int $shiftShips): static
    {
        $this->shiftShips = $shiftShips;

        return $this;
    }

    public function getShiftTechs(): ?int
    {
        return $this->shiftTechs;
    }

    public function setShiftTechs(int $shiftTechs): static
    {
        $this->shiftTechs = $shiftTechs;

        return $this;
    }

    public function getShiftBuildings(): ?int
    {
        return $this->shiftBuildings;
    }

    public function setShiftBuildings(int $shiftBuildings): static
    {
        $this->shiftBuildings = $shiftBuildings;

        return $this;
    }

    public function getShiftExp(): ?int
    {
        return $this->shiftExp;
    }

    public function setShiftExp(int $shiftExp): static
    {
        $this->shiftExp = $shiftExp;

        return $this;
    }

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

        return $this;
    }

    public function getRankShips(): ?int
    {
        return $this->rankShips;
    }

    public function setRankShips(int $rankShips): static
    {
        $this->rankShips = $rankShips;

        return $this;
    }

    public function getRankTech(): ?int
    {
        return $this->rankTech;
    }

    public function setRankTech(int $rankTech): static
    {
        $this->rankTech = $rankTech;

        return $this;
    }

    public function getRankBuildings(): ?int
    {
        return $this->rankBuildings;
    }

    public function setRankBuildings(int $rankBuildings): static
    {
        $this->rankBuildings = $rankBuildings;

        return $this;
    }

    public function getRankExp(): ?int
    {
        return $this->rankExp;
    }

    public function setRankExp(int $rankExp): static
    {
        $this->rankExp = $rankExp;

        return $this;
    }
}
