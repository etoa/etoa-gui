<?PHP
	$tpl->setView('config/check');
	$tpl->assign('subtitle', 'Integritätsprüfung');

	$cnt=0;
	if ($xml = simplexml_load_file(RELATIVE_ROOT."config/defaults.xml"))
	{
		foreach ($xml->items->item as $i)
		{
			if (!isset($cfg->{$i['name']}))
			{
				echo $i['name']." existiert in der Standardkonfiguration, aber nicht in der Datenbank! ";
				$cfg->add((string)$i['name'],(string)$i->v,(string)$i->p1,(string)$i->p2);
				echo "<b>Behoben</b><br/>";
			}
			$cnt++;
		}
	}
	echo "<p>$cnt Einträge in der Standardkonfiguration.</p>";

	$cnt=0;
	foreach ($cfg->getArray() as $cn => $ci)
	{
		$cnt++;
		$found = false;
		foreach ($xml->items->item as $i)
		{
			if ($i['name']==$cn)
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			echo $cn." existiert in der Datenbank, aber nicht in der Standardkonfiguration! ";
			$cfg->del($cn);
			echo "<b>Gelöscht</b><br/>";
		}
	}
	echo "<p>$cnt Datensätze in der Datenbank.</p>";
	echo "<p>Prüfung abgeschlossen!</p>";
?>