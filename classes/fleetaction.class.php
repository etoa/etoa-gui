<?PHP
	/**
	*
	* @author Nicolas Perrenoud<mrcage@etoa.ch>
	*/	
	abstract class fleetAction
	{
		protected $isHostile;
		protected $isSelfOnly;
		protected $code;
		protected $name;
		
		// Update this list when adding a new class. This makes the getList() faster
		static private $sublist = array(
		"attack"
		
		);
		
		abstract function targetAction();
		abstract function returningAction();
		
		function code() { return $this->code; }
		function name() { return $this->name; }
		function isHostile() { return $this->isHostile; }
		function isSelfOnly() { return $this->isSelfOnly; }
		
		static function createFactory($code)
		{
			$className = "fleetAction".ucfirst($code);
			$classFile = CLASS_ROOT."/fleetaction/".strtolower($className).".class.php";
			include_once($classFile);
			return new $$className();			
		}
		
		static function getList()
		{

			$className = "fleetAction".ucfirst($code);
			$classFile = CLASS_ROOT."/fleetaction/".strtolower($className).".class.php";
			include_once($classFile);
			return new $$className();			

			
		}
		
	}
?>