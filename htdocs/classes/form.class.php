<?PHP

	class Form
	{
		private $token = null;
		private $name;
		private $action;
		public $post = array();
		
		public $labels = array();
		
		function __construct($name,$action)
		{
			$this->name = $name;
			$this->action = $action;
		}
		
		function genSeed()
		{
			$rnd = mt_rand(1000000000,9999999999) ^ time();
			$this->token = sha1($rnd.$this->name.$_SERVER['REMOTE_ADDR']);
			if (!isset($_SESSION['formtokens']))
				$_SESSION['formtokens'] = array();
			$_SESSION['formtokens'][$this->name] = array();
			$_SESSION['formtokens'][$this->name]['fields'] = array();
			$_SESSION['formtokens'][$this->name]['token'] = $this->token;
		}
	
		function begin()
		{
			$str = '<form id="'.$this->name.'" action="'.$this->action.'" method="post">';
			return $str;			
		}	
		
		function checkSubmit($buttonName)
		{
			if(isset($_POST[$this->getEName($buttonName)]))
			{
				$this->post = $this->getPosted();
				$this->genSeed();
				return true;
			}
			$this->genSeed();
			return false;
		}
		
		private function getEName($name)
		{
			if (!isset($_SESSION['formtokens']) || !isset($_SESSION['formtokens'][$this->name]))
				$this->genSeed();
			return sha1($_SESSION['formtokens'][$this->name]['token'].$name);
		}
		
		private function getEId($name)
		{
			if (!isset($_SESSION['formtokens']) || !isset($_SESSION['formtokens'][$this->name]))
				$this->genSeed();
			return sha1($name.$_SESSION['formtokens'][$this->name]['token']);
		}		
		
		function end()
		{
			$str = "</form>";
			return $str;
		}		
		
		private function getPosted()
		{
			$rtn = array();
			foreach ($_SESSION['formtokens'][$this->name]['fields'] as $v)
			{
				$rtn[$v] = $_POST[$this->getEName($v)];
			}
			return $rtn;
		}
		
		function label($name,$content)
		{
			$lid = md5(microtime(true).$this->token.$name);
			return '<label id="'.$lid.'" for="'.$this->getEId($name).'"> '.$content.'</label>';
		}
		
		function input($name,$args=array())
		{
			$_SESSION['formtokens'][$this->name]['fields'][] = $name;
			
			$value = isset($args['value']) ? $args['value'] : '';
			return '<input type="text" id="'.$this->getEId($name).'" name="'.$this->getEName($name).'" value="'.$value.'" />';
		}
		
		function submit($name,$label,$args=array())
		{			
			return '<input type="submit" id="'.$this->getEId($name).'" name="'.$this->getEName($name).'" value="'.$label.'" />';
		}		
		
	}

?>