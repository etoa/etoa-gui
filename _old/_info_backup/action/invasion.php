<?PHP

	infobox_start("Invasieren");
	echo "Wenn die unbewohnten Planeten rar werden oder man keine Lust hat einen neuen Planeten m&uuml;hsam aufzubauen, gibt es die Option sich einen 		Planeten eines anderen Spielers unter den Nagel zu reissen.<br>
Dies kann man aber nur mit speziellen Schiffen, die f&uuml;r Invasionen ausgerichtet sind, machen.<br>
Eine Invasion kann nur erfolgreich sein, wenn mindestens ein invasionsf&auml;higes Schiff den Kampf &uuml;berlebt und auch dann liegt die Chance nur bei 		einem gewissen Prozentsatz. Je gr&ouml;sser der Punkteunterschied zwischen den beiden Spielern ist, desto h&ouml;her, beziehungsweise tiefer ist die Invasionschance. Die Grundchance betr&auml;gt ".(INVADE_POSSIBILITY*100)."% und sie kann ".(INVADE_MAX_POSSIBILITY*100)."% nicht &uuml;bersteigen und ".(INVADE_MIN_POSSIBILITY*100)."% nicht unterschreiten. Mit mehreren Invasionsschiffen erh&ouml;ht sich die Chance nicht.<br>
Ausserdem ist zu beachten, dass die Hauptplaneten (Die Planeten, die die Spieler bei ihrer Anmeldung als erste erhalten) nicht invasiert werden k&ouml;nnen.<br><br>";
	infobox_end();

	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_invade']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);

?>
