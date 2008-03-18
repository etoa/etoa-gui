<?PHP

	//INFO: es darf keine aktion mit "c" oder "r" anfangen! (keine aktion wie "co" oder "rc")

	$fa = array();
	$fa['a'] = "Angreifen";
	$fa['b'] = "Bombardieren";
	$fa['d'] = "EMP-Angriff";
	$fa['e'] = "Fake-Angriff";
	$fa['f'] = "Waren abholen";
	$fa['g'] = "Gas saugen";
	$fa['h'] = "Antrax-Angriff";
	$fa['i'] = "Invasieren";
	$fa['j'] = "Nebel/Asteroidenfelder erkunden";
	$fa['k'] = "Kolonie errichten";
	$fa['m'] = "Marktanlieferung";
	$fa['n'] = "Nebel erkunden";
	$fa['l'] = "Technologieklau";
	$fa['p'] = "Stationieren";
	$fa['s'] = "Spionieren";
	$fa['t'] = "Transport";
	$fa['v'] = "Tarnangriff";
	$fa['w'] = "Trümmer sammeln";
	$fa['x'] = "Giftgas-Angriff";
	$fa['y'] = "Asteroiden sammeln";
	$fa['z'] = "Trümmerfeld erstellen";
	
	function fa($str)
	{
		global $fa;
		$r = '';
		$a = substr($str,0,1);
		$r = isset($fa[$a]) ? $fa[$a] : 'Flug';
		if (substr($str,1,1)=='r')
		{
				$r.=' (Rückflug)';
		}
		if (substr($str,1,2)=='oc')
		{
				$r.=' (Abgebrochen)';
		}		
		return $r;
	}
	
	function fa_key($str)
	{
		return substr($str,0,1);
	}
?>
