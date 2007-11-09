<?PHP
	define(GALAXY_MAP_DOT_RADIUS,3);
	define(GALAXY_MAP_WIDTH,500);
	define(GALAXY_MAP_LEGEND_HEIGHT,40);

	$sx_num=$conf['num_of_sectors']['p1'];
	$sy_num=$conf['num_of_sectors']['p2'];
	$cx_num=$conf['num_of_cells']['p1'];
	$cy_num=$conf['num_of_cells']['p2'];

	echo "<h2>Galaxie-Grafik</h2>";
	echo "<input type=\"button\" onclick=\"document.location='?page=space'\" value=\"Raumkarte des aktuellen Sektors\" /><br/><br/>";
	echo "Anzeigen: <select onchange=\"document.getElementById('img').src='misc/map.image.php'+this.options[this.selectedIndex].value;\">
	<option value=\"?t=".time()."\">Normale Galaxieansicht</option
	<option value=\"?type=populated&t=".time()."\">Bev&ouml;lkerte Systeme</option
	<option value=\"?type=own&t=".time()."\">Systeme mit eigenen Planeten</option
	<option value=\"?type=alliance&t=".time()."\">Systeme mit Allianzplaneten</option
	
	</select><br/><br/>";
	echo "<img src=\"misc/map.image.php\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/>";
	
	echo "<map name=\"Galaxy\">\n";
	$sec_x_size=GALAXY_MAP_WIDTH/$sx_num;
	$sec_y_size=GALAXY_MAP_WIDTH/$sy_num;
	$xcnt=1;
	$ycnt=1;
	for ($x=0;$x<GALAXY_MAP_WIDTH;$x+=$sec_x_size)
	{
	 	$ycnt=1;
		for ($y=0;$y<GALAXY_MAP_WIDTH;$y+=$sec_y_size)
		{
	  	echo "<area shape=\"rect\" coords=\"$x,".(GALAXY_MAP_WIDTH-$y).",".($x+$sec_x_size).",".(GALAXY_MAP_WIDTH-$y-$sec_y_size)."\" href=\"?page=space&sx=$xcnt&sy=$ycnt\" alt=\"Sektor $xcnt / $ycnt\" ".tm("Sektor $xcnt / $ycnt","Klicken um Karte anzuzeigen").">\n";
	  	$ycnt++;
	  }
	  $xcnt++;
	}
	echo "</map>\n";
?>