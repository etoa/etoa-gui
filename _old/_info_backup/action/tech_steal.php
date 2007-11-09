<?PHP
	infobox_start("Spionageangriff");
	echo "Mit der Option \"Spionagenagriff\" hat man die M&ouml;glichkeit einem anderen User eine spezielle Technologie (welche durch Zufallsprinzip bestimmt wird) abzuschauen.<br>
Bei Gelingen hat man sofort die gleiche Stufe der Technologie, wie sie der Spieler, dem ihr sie abgeschaut habt, hat.<br>
Die Chance f&uuml;r ein Gelingen ist relativ klein, kann aber durch erforschen der \"Spionagetechnologie\" erh&ouml;ht werden.<br><br>";
	infobox_end();

	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_forsteal']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);

?>
