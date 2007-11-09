<?PHP
	infobox_start("Antrax");
	echo "Diese F&auml;higkeit erm&ouml;glicht dem Angreiffer bei Gelingen der Aktion, Bewohner und Nahrung eines Planeten zu vernichten. Die Schadensh&ouml;he wird in beiden F&auml;llen zuf&auml;llig entschieden. Taktisch sinnvoll, wenn man dem Gegner nach gewonnenem Kampf noch zus&auml;tzlich Schaden will!<br><br>";
	infobox_end();

	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_antrax_food']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);

?>
