<?PHP

	echo "<h2>: Technologien :</h2>";

if ($_GET['id']!="")
{
	if ($b_level==0) $b_level==1;
	$res = dbquery("SELECT tech_id,tech_name,tech_longcomment FROM ".$db_table['technologies']." WHERE tech_id='".$_GET['id']."';");
	if ($arr = @mysql_fetch_array($res))
	{
		infobox_start($arr['tech_name'],1);
		echo "<tr><th class=\"tbltitle\" style=\"width:150px;\"><img src=\"".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id'].".".IMAGE_EXT."\" style=\"width:150px;height:150px;\" alt=\"Bild ".$arr['tech_name']."\" /></td>";
		echo "<td class=\"tbldata\">".text2html($arr['tech_longcomment'])."</td></tr>";
		infobox_end(1);
	}	
	else
	  echo "Technologiedaten nicht gefunden!";	
	echo "<input type=\"button\" value=\"Technologie&uuml;bersicht\" onclick=\"document.location='?page=$page&site=$site'\" /> &nbsp; ";	    
	echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=tech'\" /> &nbsp; ";
}
else
{
	$res = dbquery("SELECT tech_name,tech_id,tech_shortcomment FROM ".$db_table['technologies']." WHERE tech_show=1 ORDER BY tech_order,tech_name;");
	if (mysql_num_rows($res)>0)
	{
		infobox_start("&Uuml;bersicht",1);  
		while ($arr = mysql_fetch_array($res))
		{
			echo "<tr><td class=\"tbldata\"><img src=\"".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id']."_small.".IMAGE_EXT."\" width=\"50\" height=\"50\" alt=\"Bild ".$arr['tech_name']."\" /></td>";
			echo "<td class=\"tbltitle\">".$arr['tech_name']."</td>";
			echo "<td class=\"tbldata\">".$arr['tech_shortcomment']."</td>";
			echo "<td class=\"tbldata\"><a href=\"?page=$page&site=$site&id=".$arr['tech_id']."\">Details</a></td></tr>";
		}
		infobox_end(1);
	}
	else
		echo "<i>Keine Daten vorhanden!</i>";		
}

?>