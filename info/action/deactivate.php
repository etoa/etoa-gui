<?PHP
	infobox_start("Deaktivieren (EMP)");
	echo "Diese F&auml;higkeit erm&ouml;glicht dem Angreiffer bei Gelingen der Aktion, ein Geb&auml;ude des Opfers nach Zufallsprinzip zu deaktivieren. F&uuml;r eine bestimmte Zeit (ebenfalls zufallsm&auml;ssig) kann das Opfer dieses Geb&auml;ude nicht mehr aktiv nutzen!<br>
	Die Chance ein Geb&auml;ude erfolgreich zu deaktivieren erh&ouml;ht sich, in dem man \"EMP-Technologie\" weiter erforscht! (Pro Stufe +".SHIP_BOMB_FACTOR."%<br><br>";
	infobox_end();

	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_deactivade']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);

?>
