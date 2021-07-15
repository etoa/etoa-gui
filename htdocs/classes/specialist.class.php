<?PHP
class Specialist
{
    private $id;
    private $name;
    private $desc;
    private $enable = false;
    private $pointsReq = 0;
    private $costsMetal = 0;
    private $costsCrystal = 0;
    private $costsPlastic = 0;
    private $costsFuel = 0;
    private $costsFood = 0;
    private $days = 0;
    private $prodMetal = 1;
    private $prodCrystal = 1;
    private $prodPlastic = 1;
    private $prodFuel = 1;
    private $prodFood = 1;
    private $power = 1;
    private $population = 1;
    private $researchTime = 1;
    private $buildTime = 1;
    private $defenseTime = 1;
    private $shipTime = 1;
    private $costsBuilding = 1;
    private $costsDefense = 1;
    private $costsShip = 1;
    private $costsResearch = 1;
    private $fleetSpeedFactor = 1;
    private $fleetMax = 0;
    private $defRepair = 1;
    private $spyLevel = 0;
    private $tarnLevel = 0;
    private $tradeTime = 1;
    private $tradeBonus = 1;

    public function __construct($id = 0, $endTime = 0, $userId = -1)
    {
        if ($userId > 0) {
            $ures = dbquery("
								SELECT
									user_specialist_id,
									user_specialist_time
								FROM
									users
								WHERE
									user_id='" . $userId . "'
								LIMIT 1;");
            if (mysql_num_rows($ures) > 0) {
                $uarr = mysql_fetch_row($ures);
                $id = $uarr[0];
                $endTime = $uarr[1];
            }
        }

        if ($id > 0) {
            $sres = dbquery("
				SELECT
					*
				FROM
					specialists
				WHERE
					specialist_id=" . $id . "
				");
            if (mysql_num_rows($sres) > 0) {
                $sarr = mysql_fetch_assoc($sres);

                if (time() < $endTime) {
                    $this->id = $id;
                    $this->name = $sarr['specialist_name'];
                    $this->desc = $sarr['specialist_desc'];
                    $this->enable = $sarr['specialist_enabled'];
                    $this->pointsReq = $sarr['specialist_points_req'];
                    $this->costsMetal = $sarr['specialist_costs_metal'];
                    $this->costsCrystal = $sarr['specialist_costs_crystal'];
                    $this->costsPlastic = $sarr['specialist_costs_plastic'];
                    $this->costsFuel = $sarr['specialist_costs_fuel'];
                    $this->costsFood = $sarr['specialist_costs_food'];
                    $this->days = $sarr['specialist_days'];
                    $this->prodMetal = $sarr['specialist_prod_metal'];
                    $this->prodCrystal = $sarr['specialist_prod_crystal'];
                    $this->prodPlastic = $sarr['specialist_prod_plastic'];
                    $this->prodFuel = $sarr['specialist_prod_fuel'];
                    $this->prodFood = $sarr['specialist_prod_food'];
                    $this->power = $sarr['specialist_power'];
                    $this->population = $sarr['specialist_population'];
                    $this->researchTime = $sarr['specialist_time_tech'];
                    $this->buildTime = $sarr['specialist_time_buildings'];
                    $this->defenseTime = $sarr['specialist_time_defense'];
                    $this->shipTime = $sarr['specialist_time_ships'];
                    $this->costsBuilding = $sarr['specialist_costs_buildings'];
                    $this->costsDefense = $sarr['specialist_costs_defense'];
                    $this->costsShip = $sarr['specialist_costs_ships'];
                    $this->costsResearch = $sarr['specialist_costs_tech'];
                    $this->fleetSpeedFactor = $sarr['specialist_fleet_speed'];
                    $this->fleetMax = $sarr['specialist_fleet_max'];
                    $this->defRepair = $sarr['specialist_def_repair'];
                    $this->spyLevel = $sarr['specialist_spy_level'];
                    $this->tarnLevel = $sarr['specialist_tarn_level'];
                    $this->tradeTime = $sarr['specialist_trade_time'];
                    $this->tradeBonus = $sarr['specialist_trade_bonus'];
                    return;
                } else {
                    $this->id = 0;
                    $this->name = "Kein Spezialist";
                    $this->desc = "-";
                }
            }
        }

        $this->id = 0;
        $this->name = "Kein Spezialist";
        $this->desc = "-";
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __set($key, $val)
    {
        try {
            throw new EException("Properties der Klasse " . __CLASS__ . " sind read-only!");
            /*
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
				$this->$key = $val;*/
        } catch (EException $e) {
            echo $e;
        }
    }

    public function __get($key)
    {
        try {
            if (!property_exists($this, $key))
                throw new EException("Property $key existiert nicht in " . __CLASS__);


            return $this->$key;
        } catch (EException $e) {
            echo $e;
            return null;
        }
    }
}
