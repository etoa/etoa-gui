<?php

declare(strict_types=1);

namespace EtoA\Core\Log;

class FleetLogEntry
{
    public $fleetId;
    public $userId;
    public $launchtime;
    public $landtime;
    public $sourceEntity;
    public $sourceId;
    public $targetId;
    public $action;
    public $status;
    public $entityResStart;
    public $entityResEnd;
    public $entityShipStart;
    public $entityShipEnd;
    public $fleetResStart;
    public $fleetResEnd;
    public $fleetShipStart;
    public $fleetShipEnd;
    public $launched;
    public $fuel;
    public $food;
    public $pilots;
    public $text;
    public $severity;
    public $facility;

    public function __construct($userId = 0, $sourceId, &$sourceEnt = null)
    {
        $this->userId = $userId;
        if ($sourceEnt) {
            $this->sourceEntity = $sourceEnt;
            $this->entityResStart = $this->sourceEntity->getResourceLog();
            $this->entityResEnd = "";
        } else {
            $this->sourceEntity = null;
            $this->entityResStart = "untouched";
            $this->entityResEnd = "untouched";
        }
        $this->sourceId = $sourceId;
        $this->status = 0;
        $this->severity = 0;
        $this->facility = 0;
        $this->launched = false;
        $this->fleetResStart = "0:0:0:0:0:0:0,f,0:0:0:0:0:0:0";
        $this->fleetShipStart = "0";

        $this->fleetId = 0;
        $this->launchtime = 0;
        $this->landtime = 0;
        $this->targetId = 0;
        $this->action = "";
        $this->entityShipStart = "";
        $this->entityShipEnd = "";
        $this->fleetResEnd = "";
        $this->fleetShipEnd = "";
        $this->fuel = 0;
        $this->food = 0;
        $this->pilots = 0;
    }

    public function addFleetRes($res, $people, $fetch = null, $end = true)
    {
        $string = "";
        foreach ($res as $rid => $rcnt) {
            if ($rid) {
                $string .= $rcnt . ":";
            }
        }
        $string .= $people . ":0,f,";
        if ($fetch) {
            foreach ($fetch as $fid => $fcnt) {
                if ($fid) {
                    $string .= $fcnt . ":";
                }
            }
        }
        if ($end) {
            $this->fleetResEnd = $string;
        } else {
            $this->fleetResStart = $string;
        }
    }

    public function launch()
    {
        $this->entityResEnd = $this->sourceEntity->getResourceLog();
        $this->facility = FleetLog::F_LAUNCH;
        $this->launched = true;
    }

    public function cancel($fleetId, $launchtime, $landtime, $targetId, $action, $status, $pilots)
    {
        $this->fleetId = 0;
        $this->facility = FleetLog::F_CANCEL;
        $this->launchtime = $launchtime;
        $this->landtime = $landtime;
        $this->targetId = $targetId;
        $this->action = $action;
        $this->status = $status;
        $this->pilots = $pilots;
        $this->launched = true;
    }
}
