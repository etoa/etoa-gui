<?PHP
	$changelogFile = "../Changelog_public.md";
	if (is_file($changelogFile)) {
		$Parsedown = new Parsedown();
		echo $Parsedown->text(file_get_contents($changelogFile)); 
	} else {
		echo "<h1>Changelog</h1>";
		error_msg("Changelog nicht verfügbar!",1);
	}
?>
