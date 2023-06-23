<?php declare(strict_types=1);

namespace EtoA\Message\ReportData;

use EtoA\Core\Database\DataTransformer;
use EtoA\Universe\Resources\BaseResources;

class BattleReportData
{
    public int $id;
    public string $subtype;
    public int $fleetId;
    /** @var int[] */
    public array $users;
    /** @var int[] */
    public array $entityUsers;
    /** @var int[] */
    public array $ships;
    /** @var int[] */
    public array $entityShips;
    /** @var int[] */
    public array $entityDefense;
    public int $weaponTech;
    public int $shieldTech;
    public int $structureTech;
    /** @var array<int, int> */
    public array $weapon;
    public int $shield;
    public int $structure;
    /** @var array<int, int> */
    public array $heal;
    /** @var array<int, int> */
    public array $count;
    public int $exp;
    public int $entityWeaponTech;
    public int $entityShieldTech;
    public int $entityStructureTech;
    /** @var array<int, int> */
    public array $entityWeapon;
    public int $entityShield;
    public int $entityStructure;
    /** @var array<int, int> */
    public array $entityHeal;
    /** @var array<int, int> */
    public array $entityCount;
    public int $entityExp;
    public BaseResources $resources;
    public BaseResources $wf;
    /** @var int[] */
    public array $shipsEnd;
    /** @var int[] */
    public array $entityShipsEnd;
    /** @var int[] */
    public array $entityDefenseEnd;
    public int $restore;
    public int $result;
    public int $restoreCivilShips;

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
}
