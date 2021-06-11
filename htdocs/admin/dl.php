<?PHP
require("inc/includer.inc.php");

if ($s->user_id) {
	if ($file = parseDownloadLink($_GET)) {
	
		// Check path
		$file = realpath($file);
		$allowedDirs = array(
			realpath(CACHE_ROOT),
			realpath(ADMIN_FILESHARING_DIR),
		);
		$backupDir = DBManager::getBackupDir();
		if (!empty($backupDir)) {
			$allowedDirs[] = realpath($backupDir);
		}
		$allow = false;
		foreach ($allowedDirs as $ad) {
			if (substr($file, 0, strlen($ad)) == $ad) {
				$allow = true;
				break;
			}			
		}
		if ($allow) {
			if (is_file($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Length: ' . filesize($file));
				header('Content-Disposition: attachment; filename="' . basename($file).'"');
				readfile($file);
				exit;
			} else {
				echo "Datei nicht vorhanden!";
			}		
		} else {
			echo "Ungültiger Pfad!";
		}
	} else {
		echo "Datei nicht angegeben oder falscher Hash-Wert!";
	}
} else {
	echo "Nicht eingeloggt!";
}
?>
