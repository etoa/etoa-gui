<?PHP

	/**
	* Abstract base class for all fleet actions
	*
	* @author Nicolas Perrenoud<mrcage@etoa.ch>
	*/	
	abstract class FleetAction
	{
		//
		// Static variables
		//

		// Update this list when adding a new class. This makes the getList() faster
		static private $sublist = array(
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
		"alliance"
		);

		// Colors for different attitudes
		static public $attitudeColor = array("#ff0","#0f0","#f90","#f00","#999");
		static public $attitudeString = array("Neutral","Friedlich","Agressiv","Feindlich","Unbekannt");
		
		// Status descriptions
		static public $statusCode = array("Hinflug","Rückflug","Abgebrochen","Allianz");

		//
		// Class variables
		//
		
		protected $code;	// Flight code
		protected $name;	// Name 
		protected $desc; 	// Short description of the action
		protected $longDesc; 	// Long description of the action
		
		protected $attitude;	// 0: Neutral, 1: Peacefull, 2: A bit hostile 3: Very hostile
		protected $visible;	// True: Visible to other players, False: Hidden for other players
		protected $exclusive; // True: Only ships with this action can take part in the fleet

		protected $allowPlayerEntities;
		protected $allowOwnEntities;
		protected $allowNpcEntities;
		protected $allowSourceEntity;
		
		//
		// Abstract methods
		//
		
		abstract function startAction();
		abstract function targetAction();
		abstract function cancelAction();
		abstract function returningAction();
		
		//
		// Getters
		//
		
		function code() { return $this->code; }
		function name() { return $this->name; }
		function color() { return self::$attitudeColor[$this->attitude]; }
		function __toString() { return "<span style=\"color:".self::$attitudeColor[$this->attitude]."\">".$this->name."</span>"; }
		function desc() { return $this->desc; }
		function longDesc() { return $this->longDesc; }
		function attitude() { return $this->attitude; }
		function visible() { return $this->visible; }
		function exclusive() { return $this->exclusive; }

		// Overwritable functions
		function displayName() { return $this->name; }
		
		// Other functions		
		function allowPlayerEntities() { return $this->allowPlayerEntities; }
		function allowOwnEntities() { return $this->allowOwnEntities; }
		function allowNpcEntities() { return $this->allowNpcEntities; }
		function allowSourceEntity() { return $this->allowSourceEntity; }
		function allowAllianceEntities() { return $this->allowAllianceEntities; }
		function allowOnHoliday() { return false; }



		//
		// Other general methods
		//

		static function createFactory($code)
		{
			$className = "fleetAction".ucfirst($code);
			$classFile = CLASS_ROOT."/fleetaction/".strtolower($className).".class.php";
			if (file_exists($classFile))
			{
				include_once($classFile);
				return new $className();			
			}
			echo "Problem mit Flottenaktion $code ($classFile)!<br/>";
			return false;
		}
		
		static function getAll()
		{
			$arr = array();
			foreach (self::$sublist as $i)
			{
				if ($tmp = self::createFactory($i))
				{
					$arr[$i] = $tmp; 
				}
			}
			return $arr;			
		}
		
	}
	
?>