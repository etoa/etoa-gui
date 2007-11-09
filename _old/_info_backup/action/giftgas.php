<?PHP
	infobox_start("Giftgas");
	echo "Diese F&auml;higkeit erm&ouml;glicht dem Angreiffer bei Gelingen der Aktion, Nahrung eines Planeten zu vernichten. Die Schadensh&ouml;he wird zuf&auml;llig entschieden. Einsetzbar, wenn man dem Gegner nach gewonnenem Kampf noch die restliche Nahrung vernichten will.<br>
	Die Chance einen erfolgreichen Antrax-Angriff durchzuf&uuml;hren erh&ouml;ht sich, in dem man \"Giftgas-Technologie\" weiter erforscht!<br><br>";
	infobox_end();
	
	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_antrax']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);	
	
?>
