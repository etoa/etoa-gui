<?PHP
	echo '<h1>Spielstatistiken</h1>';
	if (is_file(USERSTATS_OUTFILE)) {
		echo '<p><img src="'.USERSTATS_OUTFILE.'" alt="Userstats" /></p>';
	}
	if (is_file(GAMESTATS_FILE)) {
		echo file_get_contents(GAMESTATS_FILE);
	} else {
		echo "<p>Statistiken noch nicht vorhanden!</p>";
	}
?>