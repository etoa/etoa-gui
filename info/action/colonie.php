<?PHP
	infobox_start("Kolonialisieren");
	echo "Am Anfang jeder Spielerkarriere hat man einen Planet zum Verwalten. Im ganzen Universum hat es jedoch noch unz&auml;hlige andere Planeten, die unbewohnt sind. Um dies zu &auml;ndern gibt es spezielle Schiffe, welche diese \"freien\" Planeten besiedeln k&ouml;nnen.<br>
	Ein solches Schiff kann meist nicht grosse Mengen an Ressourcen mitnehmen, aber f&uuml;r diesen Zweck hat man die M&ouml;glichkeit andere Schiffe mitzuschicken.<br>
	Es ist zu beachten, dass man maximal ".USER_MAX_PLANETS." Planeten kontrollieren kann!<br>";
	infobox_end();

	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_colonialize']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);

?>