<?PHP
	infobox_start("Tarnangriff");
	echo "Eine taktisch extrem effektive Methode ist der Tarnangriff. Mit dieser Option ist man in der Lage den Gegner anzugreiffen und den ganzen Flug unentdeckt zu bleiben!<br>
	Bedingt jedoch, dass keine anderen Schiffe mitfliegen. Bis heute gibt es noch keine M&ouml;glichkeit diese Schiffe ausfindig zu machen. Wenn man sie bemerkt ist es immer schon zu sp&auml;t.<br><br>";
	infobox_end();
	
	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_tarned']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);	
	
?>
