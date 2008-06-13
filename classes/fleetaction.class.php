<?PHP
	/**
	* Abstract base class for all fleet actions
	*
	* @author Nicolas Perrenoud<mrcage@etoa.ch>
	*/	
	abstract class FleetAction
	{
		//
		// Class variables
		//
		
		protected $code;	// Flight code
		protected $name;	// Name 
		protected $desc; 	// Short description of the action
		
		protected $attitude;	// 0: Neutral, 1: Peacefull, -1: Hostile

		protected $targetPlayerEntities;
		protected $targetOwnEntities;
		protected $targetNpcEntities;
		
		// Update this list when adding a new class. This makes the getList() faster
		static private $sublist = array(
		"attack",
		"flight"
		);
		
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
		function desc() { return $this->desc; }
		
		function attitude() { return $this->attitude; }
		
		function targetPlayerEntities() { return $this->targetPlayerEntities; }
		function targetOwnEntities() { return $this->targetOwnEntities; }
		function targetNpcEntities() { return $this->targetNpcEntities; }

		//
		// Other general methods
		//

		static function createFactory($code)
		{
			$className = "fleetAction".ucfirst($code);
			$classFile = CLASS_ROOT."/fleetaction/".strtolower($className).".class.php";
			include_once($classFile);
			return new $className();			
		}
		
		static function getAll()
		{
			$arr = array();
			foreach (self::$sublist as $i)
			{
				$className = "fleetAction".ucfirst($i);
				$classFile = CLASS_ROOT."/fleetaction/".strtolower($className).".class.php";
				include_once($classFile);
				$arr[] = new $className();		
			}
			return $arr;			
		}
		
	}
?>