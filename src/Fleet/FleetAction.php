<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Fleet\Action\FleetActionAlliance;
use EtoA\Fleet\Action\FleetActionAnalyze;
use EtoA\Fleet\Action\FleetActionAntrax;
use EtoA\Fleet\Action\FleetActionAttack;
use EtoA\Fleet\Action\FleetActionBombard;
use EtoA\Fleet\Action\FleetActionCollectCrystal;
use EtoA\Fleet\Action\FleetActionCollectDebris;
use EtoA\Fleet\Action\FleetActionCollectFuel;
use EtoA\Fleet\Action\FleetActionCollectMetal;
use EtoA\Fleet\Action\FleetActionColonize;
use EtoA\Fleet\Action\FleetActionCreateDebris;
use EtoA\Fleet\Action\FleetActionDelivery;
use EtoA\Fleet\Action\FleetActionEmp;
use EtoA\Fleet\Action\FleetActionExplore;
use EtoA\Fleet\Action\FleetActionFakeattack;
use EtoA\Fleet\Action\FleetActionFetch;
use EtoA\Fleet\Action\FleetActionFlight;
use EtoA\Fleet\Action\FleetActionGasAttack;
use EtoA\Fleet\Action\FleetActionInvade;
use EtoA\Fleet\Action\FleetActionMarket;
use EtoA\Fleet\Action\FleetActionPosition;
use EtoA\Fleet\Action\FleetActionSpy;
use EtoA\Fleet\Action\FleetActionSpyattack;
use EtoA\Fleet\Action\FleetActionStealthattack;
use EtoA\Fleet\Action\FleetActionSupport;
use EtoA\Fleet\Action\FleetActionTransport;

abstract class FleetAction
{
    public const ALLIANCE = 'alliance';
    public const SUPPORT = 'support';
    public const FLIGHT = 'flight';
    public const SPY = 'spy';
    public const DELIVERY = 'delivery';
    public const MARKET = 'market';
    public const EXPLORE = 'explore';
    public const CREATE_DEBRIS = 'createdebris';
    public const COLLECT_DEBRIS = 'collectdebris';
    public const TRANSPORT = 'transport';
    public const FETCH = 'fetch';
    public const POSITION = 'position';
    public const ATTACK = 'attack';
    public const INVADE = 'invade';
    public const SPY_ATTACK = 'spyattack';
    public const STEALTH_ATTACK = 'stealthattack';
    public const FAKE_ATTACK = 'fakeattack';
    public const GAS_ATTACK = 'gasattack';
    public const ANTRAX = 'antrax';
    public const BOMBARD = 'bombard';
    public const EMP = 'emp';
    public const COLLECT_FUEL = 'collectfuel';
    public const ANALYZE = 'analyze';
    public const COLONIZE = 'colonize';
    public const COLLECT_CRYSTAL = 'collectcrystal';
    public const COLLECT_METAL = 'collectmetal';
    // TODO more to be added

    //
    // Static variables
    //

    // Update this list when adding a new class. This makes the getList() faster
    static private array $sublist = array(
        "transport",
        "fetch",
        "collectdebris",
        "position",
        "attack",
        "spy",
        "invade",
        "spyattack",
        "stealthattack",
        "fakeattack",
        "bombard",
        "emp",
        "antrax",
        "gasattack",
        "createdebris",
        "colonize",
        "collectmetal",
        "collectcrystal",
        "collectfuel",
        "analyze",
        "explore",
        "market",
        "flight",
        "support",
        "alliance",
        "delivery"/*,
		"hijack"*/
    );

    // Colors for different attitudes
    static public array $attitudeColor = array("#ff0", "#0f0", "#f90", "#f00", "#999");
    static public array $attitudeString = array("Neutral", "Friedlich", "Aggressiv", "Feindlich", "Unbekannt");

    // Status descriptions
    static public array $statusCode = array("Hinflug", "R&uuml;ckflug", "Abgebrochen", "Allianz");

    //
    // Class variables
    //

    protected string $code;    // Flight code
    protected string $name;    // Name
    protected string $desc;     // Short description of the action
    protected string $longDesc;     // Long description of the action

    protected int $attitude;    // 0: Neutral, 1: Peacefull, 2: A bit hostile 3: Very hostile
    protected bool $visible;    // True: Visible to other players, False: Hidden for other players
    protected bool $exclusive; // True: Only ships with this action can take part in the fleet except special ships

    protected bool $allowPlayerEntities;
    protected bool $allowOwnEntities;
    protected bool $allowNpcEntities;
    protected bool $allowSourceEntity;
    protected bool $allowActivePlayerEntities;
    protected bool $allowAllianceEntities;

    protected bool $cancelable = true;
    protected bool $visibleSource = true;
    protected string $sourceCode = 'u';
    protected bool $allianceAction;

    //
    // Abstract methods
    //

    abstract function startAction();

    abstract function targetAction();

    abstract function cancelAction();

    abstract function returningAction();

    abstract public function getAttitudeString();
    abstract public function getAttitudeColor();

    //
    // Getters
    //

    function code(): string
    {
        return $this->code;
    }

    function name(): string
    {
        return $this->name;
    }

    function color(): string
    {
        return self::$attitudeColor[$this->attitude];
    }

    function __toString()
    {
        return "<span style=\"color:" . self::$attitudeColor[$this->attitude] . "\">" . $this->name . "</span>";
    }

    function desc(): string
    {
        return $this->desc;
    }

    function longDesc(): string
    {
        return $this->longDesc;
    }

    function attitude(): int
    {
        return $this->attitude;
    }

    function visible(): bool
    {
        return $this->visible;
    }

    function exclusive(): bool
    {
        return $this->exclusive;
    }

    function allianceAction(): bool
    {
        return $this->allianceAction;
    }

    function cancelable(): bool
    {
        return $this->cancelable;
    }

    function visibleSource(): bool
    {
        return $this->visibleSource;
    }

    function sourceCode(): string
    {
        return $this->sourceCode;
    }

    // Overwritable functions
    function displayName(): string
    {
        return $this->name;
    }

    // Other functions
    function allowPlayerEntities(): bool
    {
        return $this->allowPlayerEntities;
    }

    function allowActivePlayerEntities(): bool
    {
        return $this->allowActivePlayerEntities;
    }

    function allowOwnEntities(): bool
    {
        return $this->allowOwnEntities;
    }

    function allowNpcEntities(): bool
    {
        return $this->allowNpcEntities;
    }

    function allowSourceEntity(): bool
    {
        return $this->allowSourceEntity;
    }

    function allowAllianceEntities(): bool
    {
        return $this->allowAllianceEntities;
    }

    function allowOnHoliday(): bool
    {
        return false;
    }



    //
    // Other general methods
    //

    static function createFactory($code)
    {
        if ($code != "" && ctype_alpha($code)) {
            $className = match ($code) {
                "transport" => FleetActionTransport::class,
                "fetch" => FleetActionFetch::class,
                "collectdebris" => FleetActionCollectDebris::class,
                "position" => FleetActionPosition::class,
                "attack" => FleetActionAttack::class,
                "spy" => FleetActionSpy::class,
                "invade" => FleetActionInvade::class,
                "spyattack" => FleetActionSpyattack::class,
                "stealthattack" => FleetActionStealthattack::class,
                "fakeattack" => FleetActionFakeattack::class,
                "bombard" => FleetActionBombard::class,
                "emp" => FleetActionEmp::class,
                "antrax" => FleetActionAntrax::class,
                "gasattack" => FleetActionGasAttack::class,
                "createdebris" => FleetActionCreateDebris::class,
                "colonize" => FleetActionColonize::class,
                "collectmetal" => FleetActionCollectMetal::class,
                "collectcrystal" => FleetActionCollectCrystal::class,
                "collectfuel" => FleetActionCollectFuel::class,
                "analyze" => FleetActionAnalyze::class,
                "explore" => FleetActionExplore::class,
                "market" => FleetActionMarket::class,
                "flight" => FleetActionFlight::class,
                "support" => FleetActionSupport::class,
                "alliance" => FleetActionAlliance::class,
                "delivery" => FleetActionDelivery::class,
                default => 'EtoA\Fleet\Action\FleetAction' . ucfirst($code),
            };
            try {
                if(class_exists($className))
                    return new $className();
            } catch (\Exception $e) {
                echo "Problem mit Flottenaktion $code ($className)!<br/>";
            }
        }
        return false;
    }

    static function getAll($sorted = false): array
    {
        // cleanup once reworked
        if (!defined('INVADE_ACTIVE_USER')) {
            define("INVADE_ACTIVE_USER", 0);
        }

        $arr = array();
        foreach (self::$sublist as $i) {
            if ($tmp = self::createFactory($i)) {
                $arr[$i] = $tmp;
            }
        }
        if ($sorted) {
            uasort($arr, array(__CLASS__, 'fleetActionCompare'));
        }
        return $arr;
    }

    static function fleetActionCompare($a, $b): int
    {
        return strcmp($a->name(), $b->name());
    }

}
