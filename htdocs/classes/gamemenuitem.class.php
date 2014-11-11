<?PHP
class GameMenuItem
{
	private $name;
	private $url;
	private $onclick;
	
	function __construct($name, $url, $onclick = null)
	{
		$this->name = $name;
		$this->url = $url;
		$this->onclick = $onclick;
	}
	
	public function __get($var)
	{
		if (isset($this->$var))
		{
			return $this->$var;
		}
		return null;
	}
}
?>