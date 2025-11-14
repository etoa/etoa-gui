<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Log\BattleLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BattleLogRepository::class)]
#[ORM\Table(name: 'logs_battle')]
class BattleLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(name:"user_id")]
    private string $fleetUserIds;

    #[ORM\Column(name:"entity_user_id")]
    private string $entityUserIds;

    #[ORM\Column(name: "landtime")]
    private int $landTime;

    #[ORM\Column(type: "integer")]
    private int $entityId;

    #[ORM\Column]
    private string $action;

    #[ORM\Column]
    private int $facility;

    #[ORM\Column]
    private int $severity;

    #[ORM\Column]
    private int $result;

    #[ORM\Column(name: 'fleet_ships_cnt')]
    private int $fleetShipsCount;

    #[ORM\Column(name: 'entity_ships_cnt')]
    private int $entityShipsCount;

    #[ORM\Column(name: 'entity_defs_cnt')]
    private int $entityDefsCount;

    #[ORM\Column(type: 'bigint')]
    private string $fleetWeapon;

    #[ORM\Column(type: 'bigint')]
    private string $fleetShield;

    #[ORM\Column(type: 'bigint')]
    private string $fleetStructure;

    #[ORM\Column(type: 'bigint')]
    private string $fleetWeaponBonus;

    #[ORM\Column(type: 'bigint')]
    private string $fleetShieldBonus;

    #[ORM\Column(type: 'bigint')]
    private string $fleetStructureBonus;

    #[ORM\Column(type: 'bigint')]
    private string $entityWeapon;

    #[ORM\Column(type: 'bigint')]
    private string $entityShield;

    #[ORM\Column(type: 'bigint')]
    private string $entityStructure;

    #[ORM\Column(type: 'bigint')]
    private string $entityWeaponBonus;

    #[ORM\Column(type: 'bigint')]
    private string $entityShieldBonus;

    #[ORM\Column(type: 'bigint')]
    private string $entityStructureBonus;

    #[ORM\Column]
    private int $entityWinExp;

    #[ORM\Column]
    private int $fleetWinExp;

    #[ORM\Column(type: 'bigint')]
    private string $winMetal;

    #[ORM\Column(type: 'bigint')]
    private string $winCrystal;

    #[ORM\Column(type: 'bigint')]
    private string $winPvc;

    #[ORM\Column(type: 'bigint')]
    private string $winTritium;

    #[ORM\Column(type: 'bigint')]
    private string $winFood;

    #[ORM\Column(type: 'bigint')]
    private string $tfMetal;

    #[ORM\Column(type: 'bigint')]
    private string $tfCrystal;

    #[ORM\Column(type: 'bigint')]
    private string $tfPvc;

    #[ORM\Column(type: 'bigint')]
    private string $timestamp;

    #[ORM\JoinColumn(name: 'user_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private ?Alliance $alliance = null;

    #[ORM\JoinColumn(name: 'entity_user_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private ?Alliance $entityAlliance = null;

    #[ORM\JoinColumn(name: 'entity_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Planet::class)]
    private ?Planet $entity = null;

    #[ORM\Column(type: "boolean")]
    private bool $war;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFleetUserIds(): ?string
    {
        return $this->fleetUserIds;
    }

    public function setFleetUserIds(string $fleetUserIds): static
    {
        $this->fleetUserIds = $fleetUserIds;

        return $this;
    }

    public function getEntityUserIds(): ?string
    {
        return $this->entityUserIds;
    }

    public function setEntityUserIds(string $entityUserIds): static
    {
        $this->entityUserIds = $entityUserIds;

        return $this;
    }

    public function getLandTime(): ?int
    {
        return $this->landTime;
    }

    public function setLandTime(int $landTime): static
    {
        $this->landTime = $landTime;

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

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function isWar(): ?bool
    {
        return $this->war;
    }

    public function setWar(bool $war): static
    {
        $this->war = $war;

        return $this;
    }

    public function getFacility(): ?int
    {
        return $this->facility;
    }

    public function setFacility(int $facility): static
    {
        $this->facility = $facility;

        return $this;
    }

    public function getSeverity(): ?int
    {
        return $this->severity;
    }

    public function setSeverity(int $severity): static
    {
        $this->severity = $severity;

        return $this;
    }

    public function getResult(): ?int
    {
        return $this->result;
    }

    public function setResult(int $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getFleetShipsCount(): ?int
    {
        return $this->fleetShipsCount;
    }

    public function setFleetShipsCount(int $fleetShipsCount): static
    {
        $this->fleetShipsCount = $fleetShipsCount;

        return $this;
    }

    public function getEntityShipsCount(): ?int
    {
        return $this->entityShipsCount;
    }

    public function setEntityShipsCount(int $entityShipsCount): static
    {
        $this->entityShipsCount = $entityShipsCount;

        return $this;
    }

    public function getEntityDefsCount(): ?int
    {
        return $this->entityDefsCount;
    }

    public function setEntityDefsCount(int $entityDefsCount): static
    {
        $this->entityDefsCount = $entityDefsCount;

        return $this;
    }

    public function getFleetWeapon(): ?string
    {
        return $this->fleetWeapon;
    }

    public function setFleetWeapon(string $fleetWeapon): static
    {
        $this->fleetWeapon = $fleetWeapon;

        return $this;
    }

    public function getFleetShield(): ?string
    {
        return $this->fleetShield;
    }

    public function setFleetShield(string $fleetShield): static
    {
        $this->fleetShield = $fleetShield;

        return $this;
    }

    public function getFleetStructure(): ?string
    {
        return $this->fleetStructure;
    }

    public function setFleetStructure(string $fleetStructure): static
    {
        $this->fleetStructure = $fleetStructure;

        return $this;
    }

    public function getFleetWeaponBonus(): ?string
    {
        return $this->fleetWeaponBonus;
    }

    public function setFleetWeaponBonus(string $fleetWeaponBonus): static
    {
        $this->fleetWeaponBonus = $fleetWeaponBonus;

        return $this;
    }

    public function getFleetShieldBonus(): ?string
    {
        return $this->fleetShieldBonus;
    }

    public function setFleetShieldBonus(string $fleetShieldBonus): static
    {
        $this->fleetShieldBonus = $fleetShieldBonus;

        return $this;
    }

    public function getFleetStructureBonus(): ?string
    {
        return $this->fleetStructureBonus;
    }

    public function setFleetStructureBonus(string $fleetStructureBonus): static
    {
        $this->fleetStructureBonus = $fleetStructureBonus;

        return $this;
    }

    public function getEntityWeapon(): ?string
    {
        return $this->entityWeapon;
    }

    public function setEntityWeapon(string $entityWeapon): static
    {
        $this->entityWeapon = $entityWeapon;

        return $this;
    }

    public function getEntityShield(): ?string
    {
        return $this->entityShield;
    }

    public function setEntityShield(string $entityShield): static
    {
        $this->entityShield = $entityShield;

        return $this;
    }

    public function getEntityStructure(): ?string
    {
        return $this->entityStructure;
    }

    public function setEntityStructure(string $entityStructure): static
    {
        $this->entityStructure = $entityStructure;

        return $this;
    }

    public function getEntityWeaponBonus(): ?string
    {
        return $this->entityWeaponBonus;
    }

    public function setEntityWeaponBonus(string $entityWeaponBonus): static
    {
        $this->entityWeaponBonus = $entityWeaponBonus;

        return $this;
    }

    public function getEntityShieldBonus(): ?string
    {
        return $this->entityShieldBonus;
    }

    public function setEntityShieldBonus(string $entityShieldBonus): static
    {
        $this->entityShieldBonus = $entityShieldBonus;

        return $this;
    }

    public function getEntityStructureBonus(): ?string
    {
        return $this->entityStructureBonus;
    }

    public function setEntityStructureBonus(string $entityStructureBonus): static
    {
        $this->entityStructureBonus = $entityStructureBonus;

        return $this;
    }

    public function getEntityWinExp(): ?int
    {
        return $this->entityWinExp;
    }

    public function setEntityWinExp(int $entityWinExp): static
    {
        $this->entityWinExp = $entityWinExp;

        return $this;
    }

    public function getFleetWinExp(): ?int
    {
        return $this->fleetWinExp;
    }

    public function setFleetWinExp(int $fleetWinExp): static
    {
        $this->fleetWinExp = $fleetWinExp;

        return $this;
    }

    public function getWinMetal(): ?string
    {
        return $this->winMetal;
    }

    public function setWinMetal(string $winMetal): static
    {
        $this->winMetal = $winMetal;

        return $this;
    }

    public function getWinCrystal(): ?string
    {
        return $this->winCrystal;
    }

    public function setWinCrystal(string $winCrystal): static
    {
        $this->winCrystal = $winCrystal;

        return $this;
    }

    public function getWinPvc(): ?string
    {
        return $this->winPvc;
    }

    public function setWinPvc(string $winPvc): static
    {
        $this->winPvc = $winPvc;

        return $this;
    }

    public function getWinTritium(): ?string
    {
        return $this->winTritium;
    }

    public function setWinTritium(string $winTritium): static
    {
        $this->winTritium = $winTritium;

        return $this;
    }

    public function getWinFood(): ?string
    {
        return $this->winFood;
    }

    public function setWinFood(string $winFood): static
    {
        $this->winFood = $winFood;

        return $this;
    }

    public function getTfMetal(): ?string
    {
        return $this->tfMetal;
    }

    public function setTfMetal(string $tfMetal): static
    {
        $this->tfMetal = $tfMetal;

        return $this;
    }

    public function getTfCrystal(): ?string
    {
        return $this->tfCrystal;
    }

    public function setTfCrystal(string $tfCrystal): static
    {
        $this->tfCrystal = $tfCrystal;

        return $this;
    }

    public function getTfPvc(): ?string
    {
        return $this->tfPvc;
    }

    public function setTfPvc(string $tfPvc): static
    {
        $this->tfPvc = $tfPvc;

        return $this;
    }

    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): static
    {
        $this->timestamp = $timestamp;

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

    public function getEntityAlliance(): ?Alliance
    {
        return $this->entityAlliance;
    }

    public function setEntityAlliance(?Alliance $entityAlliance): static
    {
        $this->entityAlliance = $entityAlliance;

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
}
