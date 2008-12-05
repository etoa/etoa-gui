<?PHP

	class Form
	{
		private $target;
		private $name;
		private $validateElements;
		private $labelWidth;
		private $hash;
		
		function Form($name,$target,$labelWidth="200px;")
		{
			$this->target = $target;
			$this->name = $name;
			$this->labelWidth = $labelWidth;
			$this->validateElements = array();
			
			$rnd = mt_rand(10000,99999) ^ time();
			$this->hash = md5($rnd.$name);
			$_SESSION['formhashes'][$name] = $rnd;
		}
		
		static function validate($name)
		{
			if (isset($_SESSION['formhashes'][$name]) && isset($_POST['formhash']))
			{
				$sess = $_SESSION['formhashes'][$name];
				$_SESSION['formhashes'][$name] = null;
				if (md5($sess.$name) == $_POST['formhash'])
				{
					return true;
				}	
			}
			return false;	
		}
		
		function begin()
		{
			return "<form id=\"".$this->name."\" action=\"".$this->target."\" method=\"post\">
			<div>
			<input type=\"hidden\" name=\"formhash\" value=\"".$this->hash."\" />";
		}
		
		function close()
		{
			echo "</div></form>";
		}
	}

?>