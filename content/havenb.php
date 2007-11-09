<?PHP


	// Load people
	$parr= mysql_fetch_row(dbquery("SELECT planet_people FROM ".$db_table['planets']." WHERE planet_id='".$c->id."';"));
	$pbarr= mysql_fetch_row(dbquery("SELECT SUM(buildlist_people_working) FROM ".$db_table['buildlist']." WHERE buildlist_planet_id='".$c->id."';"));
	
	$_SESSION['haven'] = array();
	$_SESSION['haven']['pilots_free'] = 30; //floor($parr[0]-$pbarr[0]);
	$_SESSION['haven']['pilots_reserved'] = 0; 
	$_SESSION['haven']['start_planet_id'] = $c->id;
	$_SESSION['haven']['fleet_ships']=array();
	$_SESSION['haven']['fleet_ships_sel']=0;
	$_SESSION['haven']['ships']=array();	
	$_SESSION['haven']['source_sx']=$c->sx;
	$_SESSION['haven']['source_sy']=$c->sy;
	$_SESSION['haven']['source_cx']=$c->cx;
	$_SESSION['haven']['source_cy']=$c->cy;
	$_SESSION['haven']['source_pp']=$c->solsys_pos;

	if ($_GET['c']!='' && $_GET['h']!='' && md5(base64_decode($_GET['c'])) == $_GET['h'])
	{
		$coords = explode(":",base64_decode($_GET['c']));
		$_SESSION['haven']['target_sx']=$coords[0];
		$_SESSION['haven']['target_sy']=$coords[1];
		$_SESSION['haven']['target_cx']=$coords[2];
		$_SESSION['haven']['target_cy']=$coords[3];
		$_SESSION['haven']['target_pp']=$coords[4];
	}
	else
	{
		$_SESSION['haven']['target_sx']=$c->sx;
		$_SESSION['haven']['target_sy']=$c->sy;
		$_SESSION['haven']['target_cx']=$c->cx;
		$_SESSION['haven']['target_cy']=$c->cy;
		$_SESSION['haven']['target_pp']=$c->solsys_pos;
	}
	
 ?>
		<script type="text/javascript">
			function selectText(item)
			{
				item.select();
			}
		</script>
		<?PHP 
			echo '<h1>Raumhafen</h1>';
			echo '<table class="tab"><tr>';
			echo "<td class=\"havenTab\" id=\"shipTab\" onclick=\"xajax_havenDisplayShipBox();\">1. Schiffe</td>";
			echo "<td class=\"havenTab\" id=\"targetTab\" onclick=\"xajax_havenDisplayTargetBox();\">2. Ziel</td>";
			echo "<td class=\"havenTab\" id=\"actionTab\" onclick=\"xajax_havenDisplayActionBox();\">3. Aktion</td>";
			echo "<td class=\"havenTab\" id=\"startTab\" onclick=\"xajax_havenDisplayActionBox();\">4. Start</td>";
			echo '</tr></table><div id="havenBox"></div>';
			//echo '<br/><div id="havenInfoBox">';
 			//echo '</div><br/>';
 			//echo '<input type="button" value="Flotte starten" onclick="" id="fleetStartButton" disabled="disabled" /> &nbsp; <input type="button" value="Reset" onclick="" id="havenReset" />';
			echo "<script type=\"text/javascript\">
			xajax_havenDisplayShipBox();
			//xajax_havenUpdateInfoBox();
			</script>";		

		?>
