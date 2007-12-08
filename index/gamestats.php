<?PHP
	showTitle('Spielstatistiken');
	if (!@include(CACHE_ROOT."/out/gamestats.html"))
	{
		error_msg("Run scripts/gamestats.php periodically to update gamestats!",1);			
	}		

	echo "<br/><br/>";
?>