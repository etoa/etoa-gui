<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use EtoA\Core\Database\DataTransformer;
use EtoA\Message\Report\BattleReportRepository;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BattleReportRepository::class)]
#[ORM\Table(name: 'reports_battle')]
class BattleReportData
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'battleReportData', targetEntity: Report::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private ?Report $report = null;

    #[ORM\Column]
    private string $subtype = 'other';

    #[ORM\JoinColumn(name: 'fleet_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Fleet::class)]
    private ?Fleet $fleet = null;

    #[ORM\Column(name: 'user')]
    private string $users;

    #[ORM\Column(name: 'entity_user')]
    private string $entityUsers;

    #[ORM\Column]
    private string $ships;

    #[ORM\Column]
    private string $entityShips;

    #[ORM\Column(name: 'entity_def')]
    private string $entityDefense;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $weaponTech;

    #[ORM\Column(type: Types::INTEGER)]
    private int $shieldTech;

    #[ORM\Column(type: Types::INTEGER)]
    private int $structureTech;

    #[ORM\Column(name: 'weapon_1', type: Types::BIGINT)]
    private int $weapon1;

    #[ORM\Column(name: 'weapon_2', type: Types::BIGINT)]
    private int $weapon2;

    #[ORM\Column(name: 'weapon_3', type: Types::BIGINT)]
    private int $weapon3;

    #[ORM\Column(name: 'weapon_4', type: Types::BIGINT)]
    private int $weapon4;

    #[ORM\Column(name: 'weapon_5', type: Types::BIGINT)]
    private int $weapon5;

    #[ORM\Column(type: Types::BIGINT)]
    private int $shield;

    #[ORM\Column(type: Types::BIGINT)]
    private int $structure;

    #[ORM\Column(name: 'heal_1', type: Types::BIGINT)]
    private int $heal1;

    #[ORM\Column(name: 'heal_2', type: Types::BIGINT)]
    private int $heal2;

    #[ORM\Column(name: 'heal_3', type: Types::BIGINT)]
    private int $heal3;

    #[ORM\Column(name: 'heal_4', type: Types::BIGINT)]
    private int $heal4;

    #[ORM\Column(name: 'heal_5', type: Types::BIGINT)]
    private int $heal5;

    #[ORM\Column(name: 'count_1', type: Types::INTEGER)]
    private int $count1;

    #[ORM\Column(name: 'count_2', type: Types::INTEGER)]
    private int $count2;

    #[ORM\Column(name: 'count_3', type: Types::INTEGER)]
    private int $count3;

    #[ORM\Column(name: 'count_4', type: Types::INTEGER)]
    private int $count4;

    #[ORM\Column(name: 'count_5', type: Types::INTEGER)]
    private int $count5;

    #[ORM\Column(type: Types::INTEGER)]
    private int $exp;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $entityWeaponTech;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $entityShieldTech;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $entityStructureTech;

    #[ORM\Column(name: 'entity_weapon_1', type: Types::BIGINT)]
    private int $entityWeapon1;

    #[ORM\Column(name: 'entity_weapon_2', type: Types::BIGINT)]
    private int $entityWeapon2;

    #[ORM\Column(name: 'entity_weapon_3', type: Types::BIGINT)]
    private int $entityWeapon3;

    #[ORM\Column(name: 'entity_weapon_4', type: Types::BIGINT)]
    private int $entityWeapon4;

    #[ORM\Column(name: 'entity_weapon_5', type: Types::BIGINT)]
    private int $entityWeapon5;

    #[ORM\Column(type: Types::BIGINT)]
    private int $entityShield;

    #[ORM\Column(type: Types::BIGINT)]
    private int $entityStructure;

    #[ORM\Column(name: 'entity_heal_1', type: Types::BIGINT)]
    private int $entityHeal1;

    #[ORM\Column(name: 'entity_heal_2', type: Types::BIGINT)]
    private int $entityHeal2;

    #[ORM\Column(name: 'entity_heal_3', type: Types::BIGINT)]
    private int $entityHeal3;

    #[ORM\Column(name: 'entity_heal_4', type: Types::BIGINT)]
    private int $entityHeal4;

    #[ORM\Column(name: 'entity_heal_5', type: Types::BIGINT)]
    private int $entityHeal5;

    #[ORM\Column(name: 'entity_count_1', type: Types::BIGINT)]
    private int $entityCount1;

    #[ORM\Column(name: 'entity_count_2', type: Types::BIGINT)]
    private int $entityCount2;

    #[ORM\Column(name: 'entity_count_3', type: Types::BIGINT)]
    private int $entityCount3;

    #[ORM\Column(name: 'entity_count_4', type: Types::BIGINT)]
    private int $entityCount4;

    #[ORM\Column(name: 'entity_count_5', type: Types::BIGINT)]
    private int $entityCount5;

    #[ORM\Column(type: Types::INTEGER)]
    private int $entityExp;

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

    #[ORM\Column(name: "wf_0", type: Types::BIGINT)]
    private int $wfMetal;

    #[ORM\Column(name: "wf_1", type: Types::BIGINT)]
    private int $wfCrystal;

    #[ORM\Column(name: "wf_2", type: Types::BIGINT)]
    private int $wfPlastic;

    private BaseResources $resources;

    private BaseResources $wf;

    #[ORM\Column(type: Types::TEXT)]
    private string $shipsEnd;

    #[ORM\Column(type: Types::TEXT)]
    private string $entityShipsEnd;

    #[ORM\Column(name: 'entity_def_end', type: Types::TEXT)]
    private string $entityDefenseEnd;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $restore;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $result;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $restoreCivilShips;

    /*
    public static function createFromArray(array $row): BattleReportData
    {
        $data = new BattleReportData();
        $data->id = (int) $row['id'];
        $data->subtype = $row['subtype'];
        $data->fleetId = (int) $row['fleet_id'];
        $data->users = DataTransformer::userString($row['user']);
        $data->entityUsers = DataTransformer::userString($row['entity_user']);
        $data->ships = DataTransformer::dataString($row['ships']);
        $data->entityShips = DataTransformer::dataString($row['entity_ships']);
        $data->entityDefense = DataTransformer::dataString($row['entity_def']);
        $data->weaponTech = (int) $row['weapon_tech'];
        $data->shieldTech = (int) $row['shield_tech'];
        $data->structureTech = (int) $row['structure_tech'];
        $data->weapon = [
            1 => (int) $row['weapon_1'],
            2 => (int) $row['weapon_2'],
            3 => (int) $row['weapon_3'],
            4 => (int) $row['weapon_4'],
            5 => (int) $row['weapon_5'],
        ];
        $data->shield = (int) $row['shield'];
        $data->structure = (int) $row['structure'];
        $data->heal = [
            1 => (int) $row['heal_1'],
            2 => (int) $row['heal_2'],
            3 => (int) $row['heal_3'],
            4 => (int) $row['heal_4'],
            5 => (int) $row['heal_5'],
        ];
        $data->count = [
            1 => (int) $row['count_1'],
            2 => (int) $row['count_2'],
            3 => (int) $row['count_3'],
            4 => (int) $row['count_4'],
            5 => (int) $row['count_5'],
        ];
        $data->exp = (int) $row['exp'];
        $data->entityWeaponTech = (int) $row['entity_weapon_tech'];
        $data->entityShieldTech = (int) $row['entity_shield_tech'];
        $data->entityStructureTech = (int) $row['entity_structure_tech'];
        $data->entityWeapon = [
            1 => (int) $row['entity_weapon_1'],
            2 => (int) $row['entity_weapon_2'],
            3 => (int) $row['entity_weapon_3'],
            4 => (int) $row['entity_weapon_4'],
            5 => (int) $row['entity_weapon_5'],
        ];
        $data->entityShield = (int) $row['entity_shield'];
        $data->entityStructure = (int) $row['entity_structure'];
        $data->entityHeal = [
            1 => (int) $row['entity_heal_1'],
            2 => (int) $row['entity_heal_2'],
            3 => (int) $row['entity_heal_3'],
            4 => (int) $row['entity_heal_4'],
            5 => (int) $row['entity_heal_5'],
        ];
        $data->entityCount = [
            1 => (int) $row['entity_count_1'],
            2 => (int) $row['entity_count_2'],
            3 => (int) $row['entity_count_3'],
            4 => (int) $row['entity_count_4'],
            5 => (int) $row['entity_count_5'],
        ];
        $data->entityExp = (int) $row['entity_exp'];
        $data->resources = new BaseResources();
        $data->resources->metal = (int) $row['res_0'];
        $data->resources->crystal = (int) $row['res_1'];
        $data->resources->plastic = (int) $row['res_2'];
        $data->resources->fuel = (int) $row['res_3'];
        $data->resources->food = (int) $row['res_4'];
        $data->resources->people = (int) $row['res_5'];
        $data->wf = new BaseResources();
        $data->wf->metal = (int) $row['wf_0'];
        $data->wf->crystal = (int) $row['wf_1'];
        $data->wf->plastic = (int) $row['wf_2'];
        $data->shipsEnd = DataTransformer::dataString($row['ships_end']);
        $data->entityShipsEnd = DataTransformer::dataString($row['entity_ships_end']);
        $data->entityDefenseEnd = DataTransformer::dataString($row['entity_def_end']);
        $data->restore = (int) $row['restore'];
        $data->result = (int) $row['result'];
        $data->restoreCivilShips = (int) $row['restore_civil_ships'];

        return $data;
    }

    */

    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    public function setSubtype(string $subtype): static
    {
        $this->subtype = $subtype;

        return $this;
    }

    public function getUsers(): array
    {
        return DataTransformer::userString($this->users);
    }

    public function setUsers(string $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function getEntityUsers(): array
    {
        return DataTransformer::userString($this->entityUsers);
    }

    public function setEntityUsers(string $entityUsers): static
    {
        $this->entityUsers = $entityUsers;

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

    public function getEntityShips(): array
    {
        return DataTransformer::dataString($this->entityShips, Ship::class);
    }

    public function setEntityShip(string $entityShips): static
    {
        $this->entityShips = $entityShips;

        return $this;
    }

    public function getEntityDefense(): array
    {
        return DataTransformer::dataString($this->entityDefense, Defense::class);
    }

    public function setEntityDefense(string $entityDefense): static
    {
        $this->entityDefense = $entityDefense;

        return $this;
    }

    public function getWeaponTech(): ?int
    {
        return $this->weaponTech;
    }

    public function setWeaponTech(int $weaponTech): static
    {
        $this->weaponTech = $weaponTech;

        return $this;
    }

    public function getShieldTech(): ?int
    {
        return $this->shieldTech;
    }

    public function setShieldTech(int $shieldTech): static
    {
        $this->shieldTech = $shieldTech;

        return $this;
    }

    public function getStructureTech(): ?int
    {
        return $this->structureTech;
    }

    public function setStructureTech(int $structureTech): static
    {
        $this->structureTech = $structureTech;

        return $this;
    }

    public function getWeapon1(): int
    {
        return $this->weapon1;
    }

    public function setWeapon1(int $weapon1): static
    {
        $this->weapon1 = $weapon1;

        return $this;
    }

    public function getWeapon2(): int
    {
        return $this->weapon2;
    }

    public function setWeapon2(int $weapon2): static
    {
        $this->weapon2 = $weapon2;

        return $this;
    }

    public function getWeapon3(): int
    {
        return $this->weapon3;
    }

    public function setWeapon3(int $weapon3): static
    {
        $this->weapon3 = $weapon3;

        return $this;
    }

    public function getWeapon4(): int
    {
        return $this->weapon4;
    }

    public function setWeapon4(int $weapon4): static
    {
        $this->weapon4 = $weapon4;

        return $this;
    }

    public function getWeapon5(): int
    {
        return $this->weapon5;
    }

    public function setWeapon5(int $weapon5): static
    {
        $this->weapon5 = $weapon5;

        return $this;
    }

    public function getShield(): int
    {
        return $this->shield;
    }

    public function setShield(int $shield): static
    {
        $this->shield = $shield;

        return $this;
    }

    public function getStructure(): int
    {
        return $this->structure;
    }

    public function setStructure(int $structure): static
    {
        $this->structure = $structure;

        return $this;
    }

    public function getHeal1(): int
    {
        return $this->heal1;
    }

    public function setHeal1(int $heal1): static
    {
        $this->heal1 = $heal1;

        return $this;
    }

    public function getHeal2(): int
    {
        return $this->heal2;
    }

    public function setHeal2(int $heal2): static
    {
        $this->heal2 = $heal2;

        return $this;
    }

    public function getHeal3(): int
    {
        return $this->heal3;
    }

    public function setHeal3(int $heal3): static
    {
        $this->heal3 = $heal3;

        return $this;
    }

    public function getHeal4(): int
    {
        return $this->heal4;
    }

    public function setHeal4(int $heal4): static
    {
        $this->heal4 = $heal4;

        return $this;
    }

    public function getHeal5(): int
    {
        return $this->heal5;
    }

    public function setHeal5(int $heal5): static
    {
        $this->heal5 = $heal5;

        return $this;
    }

    public function getCount1(): int
    {
        return $this->count1;
    }

    public function setCount1(int $count1): static
    {
        $this->count1 = $count1;

        return $this;
    }

    public function getCount2(): ?int
    {
        return $this->count2;
    }

    public function setCount2(int $count2): static
    {
        $this->count2 = $count2;

        return $this;
    }

    public function getCount3(): ?int
    {
        return $this->count3;
    }

    public function setCount3(int $count3): static
    {
        $this->count3 = $count3;

        return $this;
    }

    public function getCount4(): ?int
    {
        return $this->count4;
    }

    public function setCount4(int $count4): static
    {
        $this->count4 = $count4;

        return $this;
    }

    public function getCount5(): ?int
    {
        return $this->count5;
    }

    public function setCount5(int $count5): static
    {
        $this->count5 = $count5;

        return $this;
    }

    public function getExp(): ?int
    {
        return $this->exp;
    }

    public function setExp(int $exp): static
    {
        $this->exp = $exp;

        return $this;
    }

    public function getEntityWeaponTech(): ?int
    {
        return $this->entityWeaponTech;
    }

    public function setEntityWeaponTech(int $entityWeaponTech): static
    {
        $this->entityWeaponTech = $entityWeaponTech;

        return $this;
    }

    public function getEntityShieldTech(): ?int
    {
        return $this->entityShieldTech;
    }

    public function setEntityShieldTech(int $entityShieldTech): static
    {
        $this->entityShieldTech = $entityShieldTech;

        return $this;
    }

    public function getEntityStructureTech(): ?int
    {
        return $this->entityStructureTech;
    }

    public function setEntityStructureTech(int $entityStructureTech): static
    {
        $this->entityStructureTech = $entityStructureTech;

        return $this;
    }

    public function getEntityWeapon1(): int
    {
        return $this->entityWeapon1;
    }

    public function setEntityWeapon1(int $entityWeapon1): static
    {
        $this->entityWeapon1 = $entityWeapon1;

        return $this;
    }

    public function getEntityWeapon2(): int
    {
        return $this->entityWeapon2;
    }

    public function setEntityWeapon2(int $entityWeapon2): static
    {
        $this->entityWeapon2 = $entityWeapon2;

        return $this;
    }

    public function getEntityWeapon3(): int
    {
        return $this->entityWeapon3;
    }

    public function setEntityWeapon3(int $entityWeapon3): static
    {
        $this->entityWeapon3 = $entityWeapon3;

        return $this;
    }

    public function getEntityWeapon4(): int
    {
        return $this->entityWeapon4;
    }

    public function setEntityWeapon4(int $entityWeapon4): static
    {
        $this->entityWeapon4 = $entityWeapon4;

        return $this;
    }

    public function getEntityWeapon5(): int
    {
        return $this->entityWeapon5;
    }

    public function setEntityWeapon5(int $entityWeapon5): static
    {
        $this->entityWeapon5 = $entityWeapon5;

        return $this;
    }

    public function getEntityShield(): int
    {
        return $this->entityShield;
    }

    public function setEntityShield(int $entityShield): static
    {
        $this->entityShield = $entityShield;

        return $this;
    }

    public function getEntityStructure(): int
    {
        return $this->entityStructure;
    }

    public function setEntityStructure(int $entityStructure): static
    {
        $this->entityStructure = $entityStructure;

        return $this;
    }

    public function getEntityHeal1(): int
    {
        return $this->entityHeal1;
    }

    public function setEntityHeal1(int $entityHeal1): static
    {
        $this->entityHeal1 = $entityHeal1;

        return $this;
    }

    public function getEntityHeal2(): int
    {
        return $this->entityHeal2;
    }

    public function setEntityHeal2(int $entityHeal2): static
    {
        $this->entityHeal2 = $entityHeal2;

        return $this;
    }

    public function getEntityHeal3(): int
    {
        return $this->entityHeal3;
    }

    public function setEntityHeal3(int $entityHeal3): static
    {
        $this->entityHeal3 = $entityHeal3;

        return $this;
    }

    public function getEntityHeal4(): int
    {
        return $this->entityHeal4;
    }

    public function setEntityHeal4(int $entityHeal4): static
    {
        $this->entityHeal4 = $entityHeal4;

        return $this;
    }

    public function getEntityHeal5(): int
    {
        return $this->entityHeal5;
    }

    public function setEntityHeal5(int $entityHeal5): static
    {
        $this->entityHeal5 = $entityHeal5;

        return $this;
    }

    public function getEntityCount1(): int
    {
        return $this->entityCount1;
    }

    public function setEntityCount1(int $entityCount1): static
    {
        $this->entityCount1 = $entityCount1;

        return $this;
    }

    public function getEntityCount2(): int
    {
        return $this->entityCount2;
    }

    public function setEntityCount2(int $entityCount2): static
    {
        $this->entityCount2 = $entityCount2;

        return $this;
    }

    public function getEntityCount3(): int
    {
        return $this->entityCount3;
    }

    public function setEntityCount3(int $entityCount3): static
    {
        $this->entityCount3 = $entityCount3;

        return $this;
    }

    public function getEntityCount4(): int
    {
        return $this->entityCount4;
    }

    public function setEntityCount4(int $entityCount4): static
    {
        $this->entityCount4 = $entityCount4;

        return $this;
    }

    public function getEntityCount5(): int
    {
        return $this->entityCount5;
    }

    public function setEntityCount5(int $entityCount5): static
    {
        $this->entityCount5 = $entityCount5;

        return $this;
    }

    public function getEntityExp(): ?int
    {
        return $this->entityExp;
    }

    public function setEntityExp(int $entityExp): static
    {
        $this->entityExp = $entityExp;

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

    public function getWfMetal(): int
    {
        return $this->wfMetal;
    }

    public function setWfMetal(int $wfMetal): static
    {
        $this->wfMetal = $wfMetal;

        return $this;
    }

    public function getWfCrystal(): int
    {
        return $this->wfCrystal;
    }

    public function setWfCrystal(int $wfCrystal): static
    {
        $this->wfCrystal = $wfCrystal;

        return $this;
    }

    public function getWfPlastic(): int
    {
        return $this->wfPlastic;
    }

    public function setWfPlastic(int $wfPlastic): static
    {
        $this->wfPlastic = $wfPlastic;

        return $this;
    }

    public function getShipsEnd(): array
    {
        return DataTransformer::dataString($this->shipsEnd, Ship::class);
    }

    public function setShipsEnd(string $shipsEnd): static
    {
        $this->shipsEnd = $shipsEnd;

        return $this;
    }

    public function getEntityShipsEnd(): array
    {
        return DataTransformer::dataString($this->entityShipsEnd,Ship::class);
    }

    public function setEntityShipsEnd(string $entityShipsEnd): static
    {
        $this->entityShipsEnd = $entityShipsEnd;

        return $this;
    }

    public function getEntityDefenseEnd(): array
    {
        return DataTransformer::dataString($this->entityDefenseEnd, Defense::class);
    }

    public function setEntityDefenseEnd(string $entityDefenseEnd): static
    {
        $this->entityDefenseEnd = $entityDefenseEnd;

        return $this;
    }

    public function getRestore(): ?int
    {
        return $this->restore;
    }

    public function setRestore(int $restore): static
    {
        $this->restore = $restore;

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

    public function getRestoreCivilShips(): ?int
    {
        return $this->restoreCivilShips;
    }

    public function setRestoreCivilShips(int $restoreCivilShips): static
    {
        $this->restoreCivilShips = $restoreCivilShips;

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

    public function getWf(): BaseResources
    {
        $this->wf = new BaseResources();

        $this->wf->metal = $this->getResMetal();
        $this->wf->crystal = $this->getResCrystal();
        $this->wf->plastic = $this->getResPlastic();
        $this->wf->fuel = $this->getResFuel();
        $this->wf->food = $this->getResFood();
        $this->wf->people = $this->getResPeople();

        return $this->wf;
    }

    public function setWf(BaseResources $wf): void
    {
        $this->setResMetal($wf->metal);
        $this->setResCrystal($wf->crystal);
        $this->setResPlastic($wf->plastic);
        $this->setResFuel($wf->fuel);
        $this->setResFood($wf->food);
        $this->setResPeople($wf->people);

        $this->wf = $wf;
    }

    public function getCount():array
    {
        return [
            1 => $this->getCount1(),
            2 => $this->getCount2(),
            3 => $this->getCount3(),
            4 => $this->getCount4(),
            5 => $this->getCount5(),
        ];
    }

    public function getEntityCount():array
    {
        return [
            1 => $this->getEntityCount1(),
            2 => $this->getEntityCount2(),
            3 => $this->getEntityCount3(),
            4 => $this->getEntityCount4(),
            5 => $this->getEntityCount5()
        ];
    }

    public function getWeapon():array
    {
        return [
            1 => $this->getWeapon1(),
            2 => $this->getWeapon2(),
            3 => $this->getWeapon3(),
            4 => $this->getWeapon4(),
            5 => $this->getWeapon5()
        ];
    }

    public function getEntityWeapon():array
    {
        return [
            1 => $this->getEntityWeapon1(),
            2 => $this->getEntityWeapon2(),
            3 => $this->getEntityWeapon3(),
            4 => $this->getEntityWeapon4(),
            5 => $this->getEntityWeapon5()
        ];
    }

    public function getHeal():array
    {
        return [
            1 => $this->getHeal1(),
            2 => $this->getHeal2(),
            3 => $this->getHeal3(),
            4 => $this->getHeal4(),
            5 => $this->getHeal5()
        ];
    }

    public function getEntityHeal():array
    {
        return [
            1 => $this->getEntityHeal1(),
            2 => $this->getEntityHeal2(),
            3 => $this->getEntityHeal3(),
            4 => $this->getEntityHeal4(),
            5 => $this->getEntityHeal5()
        ];
    }

    public function setEntityShips(string $entityShips): static
    {
        $this->entityShips = $entityShips;

        return $this;
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
