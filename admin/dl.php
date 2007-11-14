<?PHP
	if ($_GET['dl']!="")
	{
		$file = base64_decode($_GET['dl']);
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . filesize($file));
		header('Content-Disposition: attachment; filename="' . basename($file).'"');
		readfile($file);
		exit;
	}

?>