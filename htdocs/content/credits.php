<?PHP
	echo '<h1>Credits</h1>';

	$tm = new TextManager();
	$credits = $tm->getText('credits');
	if ($credits->enabled && !empty($credits->content))
	{
		iBoxStart();
		echo text2html($credits->content);
		iBoxEnd();
	}
	
	echo '<p>Wir danken den folgenden Open-Source Projekten für ihre tolle und wertvolle Arbeit:</p>';
	$thirdparty = fetchJsonConfig("thirdparty.conf");
	tableStart();
	echo '<tr>
		<th>Projekt</th>
		<th>Beschreibung</th>
		<th>Website</th>
	</tr>';
	foreach ($thirdparty as $tp) {
		echo '<tr>
			<td>'.$tp['name'].'</td>
			<td>'.$tp['description'].'</td>
			<td><a href="'.$tp['url'].'" target="_blank">'.$tp['url'].'</a></td>
		</tr>';
	}
	tableEnd();
?>
