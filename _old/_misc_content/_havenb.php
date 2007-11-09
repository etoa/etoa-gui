<?PHP
	
	session_start();

	$c->sx=2;
	$c->sy=3;
	$c->cx=1;
	$c->cy=5;
	$c->p=4;
	$c->people=30;
	
	$_SESSION['haven']['target']['sx']=0;
	$_SESSION['haven']['target']['sy']=0;
	$_SESSION['haven']['target']['cx']=0;
	$_SESSION['haven']['target']['cy']=0;
	$_SESSION['haven']['target']['p']=0;
	
	$pid=$c->id;
	$uid=$_SESSION[ROUNDID]['user']['id'];
	
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
			echo "<td class=\"havenTab\" id=\"shipTab\" onclick=\"xajax_havenDisplayShipBox(".$pid.",".$uid.");xajax_havenUpdateInfoBox(".$pid.",".$uid.");\">1. Schiffe</td>";
			echo "<td class=\"havenTab\" id=\"targetTab\" onclick=\"xajax_havenDisplayTargetBox(".$pid.",".$uid.");xajax_havenUpdateInfoBox(".$pid.",".$uid.");\">2. Ziel</td>";
			echo "<td class=\"havenTab\" id=\"actionTab\" onclick=\"xajax_havenDisplayActionBox(".$pid.",".$uid.");xajax_havenUpdateInfoBox(".$pid.",".$uid.");\">3. Aktion</td>";
			echo '</tr></table><div id="havenBox">';
 			echo '</div><br/><div id="havenInfoBox">';
 			echo '</div><br/>';
 			echo '<input type="button" value="Flotte starten" onclick="" id="fleetStartButton" disabled="disabled" /> &nbsp; <input type="button" value="Reset" onclick="" id="havenReset" />';
			echo "<script type=\"text/javascript\">xajax_havenDisplayShipBox(".$pid.",".$uid.");xajax_havenUpdateInfoBox(".$pid.",".$uid.");</script>";		

		?>
