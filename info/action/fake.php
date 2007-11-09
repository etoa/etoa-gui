<?PHP
	infobox_start("Fakeangriff");
	echo "Eine weitere taktische Aktion ist der Fakeangriff. Mit dieser Option kann man den Gegner verwirren, indem man ihm eine Flotte aus Schiffen vorgaukelt die gar nicht existiert!
	Die imagin&auml;ren Schiffstypenauswahl h&auml;ngt von der Anzahl Schiffe ab die einen Fakeangriff durchf&uuml;hren k&ouml;nnen.<br><br>";
	infobox_end();
	
	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_fake']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);	
	
?>
