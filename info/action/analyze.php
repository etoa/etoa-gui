<?PHP
	infobox_start("Analysieren");
	echo "Du hast die M&ouml;glichkeit Staub- und Gasvorkommen im All zu analysieren und festzustellen, ob sich deren Abbau lohnt.<br><br>";
	infobox_end();
	
	infobox_start("Schiffe",1,0);
	while($arr=mysql_fetch_array($res))
	{

		if($arr['ship_analyze']=='1')
		{
			echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$arr[ship_id]."\">".$arr['ship_name']."</a></td></tr> ";
		}
	}
	infobox_end(1);
?>