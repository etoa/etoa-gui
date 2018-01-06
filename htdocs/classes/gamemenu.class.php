<?PHP
class GameMenu
{
	private $topNav = array();
	private $mainNav = array();

    public function __construct($configFile)
	{
		$data = fetchJsonConfig($configFile);
		foreach($data['top'] as $e)
		{
			$this->topNav[] = self::parseItem($e);
		}

		foreach($data['main'] as $c)
		{
			$cat = $c['cat'];
			$this->mainNav[$cat] = array();
			foreach($c['items'] as $e)
			{
				$this->mainNav[$cat][] = self::parseItem($e);
			}
		}
	}

	private static function parseItem($e)
	{
		$name = $e['name'];
		$url = $e['url'];
		$onclick = isset($e['onclick']) ? $e['onclick'] : null;

		if (preg_match('/^%([A-Z0-9_]+)%$/', $url, $m) && defined($m[1]))
		{
			$url = constant($m[1]);
		}
		if (preg_match('/^%([A-Z0-9_]+)%$/', $onclick, $m) && defined($m[1]))
		{
			$onclick = constant($m[1]);
		}

		return new GameMenuItem($name, $url, $onclick);
	}

	public function getTopNav()
	{
		return $this->topNav;
	}

	public function getMainNav()
	{
		return $this->mainNav;
	}
}
?>
