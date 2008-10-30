<?PHP
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: shoutbox.php													//
	// Topic: Shoutbox			 									//
	// Version: 1.0																	//
	// Letzte îderung: 17.05.2007									//
	//////////////////////////////////////////////////
	$header  = "HTTP/1.0 200 OK";
	$header .= "Content-Type: text/html; charset=UTF-8";
	$header .= "Cache-Control: no-store, no-cache, must-revalidate";
	$header .= "Cache-Control: post-check=0, pre-check=0";
	$header .= "Pragma: no-cache";
	$header .= "Content-Transfer-Encoding: 8bit";
	header($header);
	
	// Maximal angezeigte Nachrichten //
	$ajax_maxmessage = 5;

	// Anzeige der Nachrichten //
	if($_GET['action'] == "fetch")
	{
		
		// Daten holen //
		$res=dbquery("
			SELECT
				sb.shoutbox_timestamp,
				sb.shoutbox_message,
				u.user_nick
			FROM
				(
					alliance_shoutbox AS sb
				INNER JOIN
					users AS u
				ON
					sb.shoutbox_user_id = u.user_id
				)
				AND u.user_alliance_id = '".intval($_GET['alliance_id'])."'
			ORDER BY
				sb.shoutbox_timestamp DESC 
			LIMIT
				0,".$ajax_maxmessage.";
		");
		
		// In eine Schleife alle Nachrichten formattieren und ausgeben //
		while($row = mysql_fetch_assoc($res))
		{
			$user_nick= str_replace("|||", "<||>", $row['user_nick']);
			$shoutbox_message = str_replace("|||", "<||>", $row['shoutbox_message']);
			$user_nick= htmlentities(stripslashes(htmlspecialchars($user_nick)));
			$shoutbox_message = htmlentities(stripslashes(htmlspecialchars($shoutbox_message)));
			echo $row['user_nick']."|||".date('d.m.Y H:i:s', $row['shoutbox_timestamp'])."|||".$row['shoutbox_message']."||||";
		}
	}
	
	// Schreiben einer neue Nachricht //
	if($_GET['action'] == "write")
	{
		// Eingabe bereinigen... //
		$message = htmlentities(stripslashes(htmlspecialchars($_GET['shoutbox_message'])));
		// ...und einf�/
		mysql_query("
			INSERT INTO
				alliance_shoutbox
				(
					shoutbox_timestamp,
					shoutbox_user_id, 
					shoutbox_message, 
					shoutbox_ip
				)
			VALUES
				(
					'".time()."',
					'".$_SESSION[ROUNDID]['user']['id']."',
					'".$message."', 
					'".$_SERVER['REMOTE_ADDR']."'
				);
		");
	}
?>
