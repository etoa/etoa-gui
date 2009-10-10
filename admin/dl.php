<?PHP
	session_start();
	if (true || count($_SESSION)>0)
	{
		if ($_GET['path']!="" && $_GET['hash']!="")
		{
			$file = base64_decode($_GET['path']); 
			if (md5($file) == $_GET['hash'])
			{		
				if (is_file($file))
				{
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Length: ' . filesize($file));
					header('Content-Disposition: attachment; filename="' . basename($file).'"');
					readfile($file);
					exit;
				}
				else
				{
					echo "Datei nicht vorhanden!";
				}
			}
			else
			{
				echo "Falscher Hash-Wert";
			}
		}
		else
		{
			echo "Datei nicht angegeben!";
		}
	}
	else
	{
		echo "Nicht eingeloggt!";
	}

?>
