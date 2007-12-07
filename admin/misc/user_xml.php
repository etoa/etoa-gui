<?PHP
	session_start();
	if (count($_SESSION)>0)
	{
		if ($_GET['id']>0)
		{
			// Funktionen und Config einlesen
			require("../../conf.inc.php");
			require("../../functions.php");
		
			// Mit der DB verbinden
			dbconnect();
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			//header('Content-Length: ' . filesize($file));
			header('Content-Disposition: attachment; filename="user'.$_GET['id'].'.xml"');
			$utx = new userToXml($_GET['id']);
			echo $utx;
			dbclose();
		}
		else
		{
			echo "User-ID nicht vorhanden!";
		}
	}
	else
	{
		echo "Nicht eingeloggt!";
	}
?>