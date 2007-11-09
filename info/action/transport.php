<?PHP
	infobox_start("Transport");
	echo "Die Grundlegendste Aktion &uuml;berhaupt, bei der Ressourcen und Bewohner transportiert werden.<br>
So gut wie jedes Schiff kann Sachen transportieren, die frage ist nur immer wie viel!<br>
Baue spezielle Transporter um kosteng&uuml;nstig zu transportieren.<br><br>";
	infobox_end();

	infobox_start("Schiffe Ressourcen Transport",1,0);
	echo "<tr><td class=\"tbldata\" width=\"250\">Alle</td></tr> ";
	infobox_end(1);

	infobox_start("Schiffe Bewohner Transport",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_people_capacity']>'0')
		{
			echo "<tr><td class=\"tbldata\" width=\"250\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);
?>
