<?PHP
	infobox_start("Gas/Nebel saugen");
	echo "In Andromeda gibt es Planeten die sich aus verschiedenen Gasen zusammensetzen. Diese Planeten sind nicht bewohnbar!<br>
Nichts desto trotz, hat man sich deren Eigenschaft zu Nutze gemacht und Schiffe entwickelt, die deren Gase absaugen und sie in verwendbares Tritium umwandeln k&ouml;nnen!<br>Die Gasplaneten bilden je nach Gr&ouml;sse ihre Gase wieder her. Sie sind immer bestrebt daran ihr ganzes Volumen mit den Gasen zu f&uuml;llen. Durch die Entstehung neuer Gase entsteht W&auml;rme. Es ist nicht selten, dass sich Gasexplosionen entfesseln. Es kommt nicht selten vor, dass einige Schiffe in der Flotte von solchen Explosionen zerst&ouml;rt werden!<br>
Es l&auml;sst sich streiten, ob sich das Gassaugen lohnt, denn es ist ausserdem so, dass nur speziel daf&uuml;r gebaute Schiffe das Gas in Tritium umwandel k&ouml;nnen. So ist es also nicht m&ouml;glich normale Transporter auf die Mission mitzuschicken, in der Hoffnung, dass mehr Tritium abgebaut werden kann. Der Gasbezug erfolgt nur durch die Gassauger.
<br><br><br>Die Option \"Nebel erkunden\", ist der vom \"Asteroiden sammeln\" sehr &auml;hnlich. Ebenso wie die Asteroiden und die Gasplaneten waren die Intergalaktischen Nebelfelder lange Zeit ein unerfoschtes Mysterium. Doch heute bezieht man auch aus ihnen einen Nutzen. Man hat herausgefunden, dass diese Nebelfelder eine extrem siliziumreiche Atmosph&auml;re haben und so ist es nach ein paar Jahren intensiver Forschung gelungen, dieses Silizium zu bergen!<br>
Doch wie auch beim Asteroiden sammeln gibt es hier ein gewisses Gefahrenrisiko. Es ist schonmal vorgekommen, dass die starken Magnetfelder, welche das Nebelfeld ausstrahlt, die Bordelektronik der Schiffe lahmgelegt hat und diese dann im unentlichen Weltall verschollen geblieben sind!<br><br>";
	infobox_end();

	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_nebula']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);

?>
