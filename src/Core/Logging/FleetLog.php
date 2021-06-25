<?php

declare(strict_types=1);

namespace EtoA\Core\Log;

class FleetLog extends BaseLog
{
    /**
     * Others
     */
    const F_OTHER = 0;
    /**
     * Launch
     */
    const F_LAUNCH = 1;
    /**
     * Cancel
     */
    const F_CANCEL = 2;
    /**
     * Action
     */
    const F_ACTION = 3;
    /**
     * Return
     */
    const F_RETURN = 4;


    private $fleetId;
    private $userId;
    private $launchtime;
    private $landtime;
    private $sourceEntity;
    private $sourceId;
    private $targetId;
    private $action;
    private $status;
    private $entityResStart;
    private $entityResEnd;
    private $entityShipStart;
    private $entityShipEnd;
    private $fleetResStart;
    private $fleetResEnd;
    private $fleetShipStart;
    private $fleetShipEnd;
    private $launched;
    private $fuel;
    private $food;
    private $pilots;
    private $text;

    private $severity;
    private $facility;
    static public $facilities = array(
    "Sonstige",
    "Start",
    "Abbruch",
    "Aktion",
    "Rückkehr"
    );

    public function __construct($userId=0,$sourceId, &$sourceEnt=null)
    {
        $this->userId=$userId;
        if ($sourceEnt)
        {
            $this->sourceEntity = $sourceEnt;
            $this->entityResStart = $this->sourceEntity->getResourceLog();
            $this->entityResEnd="";
        }
        else
        {
            $this->sourceEntity = null;
            $this->entityResStart = "untouched";
            $this->entityResEnd = "untouched";
        }
        $this->sourceId=$sourceId;
        $this->status = 0;
        $this->severity = 0;
        $this->facility = 0;
        $this->launched=false;
        $this->fleetResStart = "0:0:0:0:0:0:0,f,0:0:0:0:0:0:0";
        $this->fleetShipStart = "0";

        $this->fleetId=0;
        $this->launchtime=0;
        $this->landtime=0;
        $this->targetId=0;
        $this->action="";
        $this->entityShipStart="";
        $this->entityShipEnd="";
        $this->fleetResEnd="";
        $this->fleetShipEnd="";
        $this->fuel=0;
        $this->food=0;
        $this->pilots=0;
    }


    function __destruct()
    {
        if ($this->launched)
        {
            $this->text = "Treibstoff: ".$this->fuel." Nahrung: ".$this->food." Piloten: ".$this->pilots;
            dbquery("
            INSERT DELAYED INTO
                logs_fleet_queue
            (
                `fleet_id`,
                `facility`,
                `timestamp`,
                `message`,
                `user_id`,
                `entity_user_id`,
                `entity_from`,
                `entity_to`,
                `launchtime`,
                `landtime`,
                `action`,
                `status`,
                `fleet_res_start`,
                `fleet_res_end`,
                `fleet_ships_start`,
                `fleet_ships_end`,
                `entity_res_start`,
                `entity_res_end`,
                `entity_ships_start`,
                `entity_ships_end`
            ) VALUES (
                '".$this->fleetId."',
                '".$this->facility."',
                '".time()."',
                '".$this->text."',
                '".$this->userId."',
                '".$this->userId."',
                '".$this->sourceId."',
                '".$this->targetId."',
                '".$this->launchtime."',
                '".$this->landtime."',
                '".$this->action."',
                '".$this->status."',
                '".$this->fleetResStart."',
                '".$this->fleetResEnd."',
                '".$this->fleetShipStart."',
                '".$this->fleetShipEnd."',
                '".$this->entityResStart."',
                '".$this->entityResEnd."',
                '".$this->entityShipStart."',
                '".$this->entityShipEnd."'
            );");
        }
    }


    public function __set($key, $val)
    {
        try
        {
            if (!property_exists($this,$key))
                throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
            else
                $this->$key = $val;

        }
        catch (EException $e)
        {
            echo $e;
        }
    }

    public function __get($key)
    {
        try
        {
            if (!property_exists($this,$key))
                throw new EException("Property $key existiert nicht in ".__CLASS__);

            return $this->$key;
        }
        catch (EException $e)
        {
            echo $e;
            return null;
        }
    }

    public function addFleetRes($res,$people,$fetch=null,$end=true)
    {
        $string = "";

        foreach ($res as $rid=>$rcnt)
            if ($rid)
                $string .= $rcnt.":";
        $string .= $people.":0,f,";

        if ($fetch)
            foreach ($fetch as $fid=>$fcnt)
                if ($fid)
                    $string .= $fcnt.":";
        if ($end)
            $this->fleetResEnd = $string;
        else
            $this->fleetResStart = $string;

    }

    public function launch()
    {
        $this->entityResEnd = $this->sourceEntity->getResourceLog();
        $this->facility = self::F_LAUNCH;
        $this->launched = true;
    }

    public function cancel($fleetId,$launchtime,$landtime,$targetId,$action,$status,$pilots)
    {
        $this->fleetId=0;
        $this->facility = self::F_CANCEL;
        $this->launchtime=$launchtime;
        $this->landtime=$landtime;
        $this->targetId=$targetId;
        $this->action=$action;
        $this->status = $status;
        $this->pilots = $pilots;
        $this->launched = true;
    }

    /**
    * Processes the log queue and stores
    * all items in the persistend log table
    */
    static function processQueue()	{
        dbquery("
        INSERT INTO
            logs_fleet
        (
            `fleet_id`,
            `facility`,
            `timestamp`,
            `message`,
            `user_id`,
            `entity_user_id`,
            `entity_from`,
            `entity_to`,
            `launchtime`,
            `landtime`,
            `action`,
            `status`,
            `fleet_res_start`,
            `fleet_res_end`,
            `fleet_ships_start`,
            `fleet_ships_end`,
            `entity_res_start`,
            `entity_res_end`,
            `entity_ships_start`,
            `entity_ships_end`
        )
        SELECT
            `fleet_id`,
            `facility`,
            `timestamp`,
            `message`,
            `user_id`,
            `entity_user_id`,
            `entity_from`,
            `entity_to`,
            `launchtime`,
            `landtime`,
            `action`,
            `status`,
            `fleet_res_start`,
            `fleet_res_end`,
            `fleet_ships_start`,
            `fleet_ships_end`,
            `entity_res_start`,
            `entity_res_end`,
            `entity_ships_start`,
            `entity_ships_end`
        FROM
            logs_fleet_queue
        ;");
        $numRecords = mysql_affected_rows();
        if ($numRecords > 0)	{
            dbquery("
            DELETE FROM
                logs_fleet_queue
            LIMIT
                ".$numRecords.";");
        }
        return $numRecords;
    }

    /**
    * Removes up old logs from the persistend log table
    *
    * @param int|string $threshold All items older than this time threshold will be deleted
    */
    static function cleanup($threshold)
    {
        dbquery("
            DELETE FROM
                logs_fleet
            WHERE
                timestamp<'".$threshold."'
        ");
        return mysql_affected_rows();
    }
}
