<?PHP
	infobox_start("Bombardieren");
	echo "Bombardieren, eine der grausamsten Waffen in diesem Universum.<br>
	Bei erfolgreicher Aktion, wird dem Gegner ein Geb&auml;ude um ein Level gesenkt. Das Geb&auml;ude wird durch Zufall ausgew&auml;hlt.<br>
	Um die Chance auf eine erfolgreiche Bombardierung zu erh&ouml;hen erforsche \"Bombentechnik\" (Pro Stufe +".SHIP_BOMB_FACTOR."%)<br><br>";
	infobox_end();

	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_build_destroy']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);

?>
