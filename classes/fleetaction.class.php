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
		"colonize",
		"collectmetal",
		"collectcrystal",
		"collectfuel",
		"analyze",
		"explore",
		"flight"
		);

		// Colors for different attitudes
		static public $attitudeColor = array("#ff0","#0f0","#f90","#f00");
		
		// Status descriptions
		static public $statusCode = array("Hinflug","Rückflug","Abgebrochen");

		//
		// Class variables
		//
		
		protected $code;	// Flight code
		protected $name;	// Name 
		protected $desc; 	// Short description of the action
		
		protected $attitude;	// 0: Neutral, 1: Peacefull, 2: A bit hostile 3: Very hostile

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
		function __toString() { return $this->name; }
		function desc() { return $this->desc; }
		
		function attitude() { return $this->attitude; }
		
		function allowPlayerEntities() { return $this->allowPlayerEntities; }
		function allowOwnEntities() { return $this->allowOwnEntities; }
		function allowNpcEntities() { return $this->allowNpcEntities; }
		function allowSourceEntity() { return $this->allowSourceEntity; }


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
			return false;
		}
		
		static function getAll()
		{
			$arr = array();
			foreach (self::$sublist as $i)
			{
				$arr[] = self::createFactory($i);
			}
			return $arr;			
		}
		
	}
	
?>