<?PHP

	echo "<h1>User-Bilder pr&uuml;fen</h1>";

	echo "Die Bilder wurden zuletzt an folgendem Datum geprüft: ".df($conf['profileimagecheck_done']['v'])."<br/>";
	dbquery("UPDATE config SET config_value='".time()."' WHERE config_name='profileimagecheck_done'");		


	$dir = '../'.PROFILE_IMG_DIR."/";

	// Selektiertes Bild löschen
	if (isset($_GET['remove_profile']) && $_GET['remove_profile']>0)
	{
		$res = dbquery("SELECT user_profile_img FROM users WHERE user_id=".$_GET['remove_profile'].";");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
      if (file_exists('../'.PROFILE_IMG_DIR."/".$arr['user_profile_img']))
      {
 	    	unlink('../'.PROFILE_IMG_DIR."/".$arr['user_profile_img']);
  	  }
			dbquery("UPDATE users SET user_profile_img='' WHERE user_id=".$_GET['remove_profile'].";");
			if (mysql_affected_rows()>0)
			{
				echo "Bild entfernt!<br/><br/>";
			}
		}
	}

		$res=dbquery("
		SELECT 
			user_id,
			user_nick,
			user_profile_img 
		FROM 
			users 
		WHERE 
			user_profile_img!='' 
		");
		$nr = mysql_num_rows($res);
		$paths = array();
		$nicks = array();
		if ($nr>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				$paths[$arr['user_id']] = $arr['user_profile_img'];
				$nicks[$arr['user_id']] = $arr['user_nick'];
			}
		}
		$files = array();
		$d = opendir($dir);
		while ($f = readdir($d))
		{
			if (is_file($dir.$f))
			{
				array_push($files,$f);
			}
		}

		$overhead = array();
		while(count($files)>0)
		{
			$k = array_pop($files);
			if (!in_array($k,$paths))	
				array_push($overhead,$k);
		}		

		if ($_GET['action']=="clearoverhead")
		{
			while(count($overhead)>0)
			{
				unlink($dir.array_pop($overhead));
			}
			echo "Verwaiste Bilder gelöscht!<br/><bt/>";
		}

		$co = count($overhead);


		if ($co>10)
		{
			echo "[<a href=\"#saved\">Gehe direkt zu den korrekt gespeicherten Bildern</a>]<br/>";
		}

		echo "<h2>Verwaiste Bilder</h2>";
		if ($co>0)
		{
				echo "Diese Bilder gehören zu Spielern, die nicht mehr in unserer Datenbank vorhanden sind.<br/>
				Es sind $co Bilder vorhanden. <a href=\"?page=$page&amp;sub=$sub&amp;action=clearoverhead\">L&ouml;sche alle verwaisten Bilder</a><br/><br/>";
				echo "<table class=\"tb\">
				<tr><th>Datei</th><th>Bild</th></tr>";
				foreach($overhead as $v)
				{				
					echo "<tr><td>".$v."</td>";
					echo '<td><img src="'.$dir.$v.'" alt="Profil" /></td></tr>';
				}
				echo "</table><br/>";
		}
		else
		{
			echo "<i>Keine vorhanden!</i>";
		}

		echo "<a name=\"saved\"></a>";
		echo "<h2>Gespeicherte Bilder</h2>";
		echo "Diese Bilder gehören zu aktiven Spielern. Bitte prüfe regelmässig, ob sie nicht gegen unsere Regeln verstossen!<br/>";
			if ($nr>0)
			{
				echo "Es sind $nr Bilder gespeichert!<br/><br/>";
				echo "<table class=\"tb\"><tr><th>User</th><th>Fehler</th><th>Aktionen</th></tr>";
				foreach($paths as $uid => $upath)
				{				
					echo "<tr><td>".$uid."</td><td>";
					if (file_exists($dir.$upath))
					{
						echo '<img src="'.$dir.$upath.'" alt="Profil" />';
					}
					else
					{
						echo '<span style=\"color:red\">Bild existiert nicht!</span>';
					}
					echo "</td><td><a href=\"?page=$page&amp;sub=$sub&amp;remove_profile=".$uid."\">Entfernen</a> ";
					echo "<a href=\"?page=$page&amp;sub=edit&amp;user_id=".$uid."\">Profil</a></td></tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Keine Bilder vorhanden!</i>";				
			}
?>