<?PHP
	infobox_start("Tr&uuml;mmer recyclen");
	echo "Bei K&auml;mpfen wo Schiffe/Verteidigungen zerst&ouml;rt werden, entstehen zum Teils massive Tr&uuml;mmerfelder (TF`s)<br>
	Sammle sie mit speziell daf&uuml;r vorgesehenen Schiffen ein und gewinne dadurch viele Ressourcen wieder.<br>Beim Sammeln von Tr&uuml;mmerfeldern k&ouml;nnen normale Schiffe mitgeschickt werden.<br>";
	infobox_end();

	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_recycle']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);
?>