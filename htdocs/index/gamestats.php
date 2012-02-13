<?PHP
	echo '<h1>Spielstatistiken</h1>';
	
	if (!@include(CACHE_ROOT."/out/gamestats.html"))
	{
		error_msg("Run scripts/gamestats.php periodically to update gamestats!",1);			
	}		

	echo "<br/><br/>";
?>