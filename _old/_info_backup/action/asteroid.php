<?PHP
	infobox_start("Asteroiden sammeln");
	echo "Im Weltraum tummeln sich viele kleinere Asteroidenfelder. Viele Jahre lang waren sie nur eine Bedrohung f&uuml;r die Zivilisation doch heute hat man gelernt einen Nutzen daraus zu ziehen. Mit speziell gebauten Schiffen ist es m&ouml;glich Ressourcen aus den Asteroidenfelder zu sch&ouml;pfen und zu verwerten!<br>
Diese Moderne Form von Ressourcengewinnung birgt aber noch ein grosses Risiko. In den Asteroidenfelder kann es vorkommen, dass die Schiffe von den Gesteinsbrocken getroffen und zerst&ouml;rt werden. In diesem Fall sind die Schiffe kaputt und werden nie mehr wieder gesehen!<br><br>
Asteroidenfelder sind aber nicht unbegrenzt verf&uuml;gbar. Wenn man sie \"aufgebraucht\" hat verschwinden sie, aber keine Angst, es werden immer wieder neue erscheinen.<br><br>";
	infobox_end();

	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_asteroid']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);

?>
