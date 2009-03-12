<?php

echo "<h1>Tools</h1>";

	//
	// Time Tester
	//
	if ($sub=="timetester")
	{
			
		echo "<a href=\"?page=$page&amp;sub=$sub\">Nochmal</a><br>";
		
		echo "<br>Test welches echo schneller ist, mit \"text\" oder 'text'<br><br>";
		$start1 = microtime();
		for ($i = 0; $i < 10000; $i++) { $test = "Dies ist ein Test $i"; }
		$ende1 = microtime();
		echo "Verbrauchte Zeit mit \" : ".($ende1 - $start1);
		
		$start2 = microtime();
		for ($i = 0; $i < 10000; $i++) { $test = 'Dies ist ein Test'.$i; }
		$ende2 = microtime();
		echo "<br>Verbrauchte Zeit mit ' : ".($ende2 - $start2);
		
		
		echo "<br><br><br>Mysql Test<br>";
		$start3 = microtime();
		for ($i = 0; $i < 1; $i++)
		{
			$res = mysql_query("SELECT planet_name, user_nick FROM planets, users ORDER BY planet_name;");
		}
		$ende3 = microtime();
		
		echo "<br>Verbrauchte Zeit mit radikaler Auslesung (SELECT * FROM): ".($ende3 - $start3);
		$start4 = microtime();
		for ($i = 0; $i < 10; $i++)
		{
			$res = mysql_query("SELECT id FROM planets;");
		}
		$ende4 = microtime();
		echo "<br>Verbrauchte Zeit mit rationioneller Auslesung ( ".$i."x SELECT xy FROM): ".($ende4 - $start4);
	}

	//
	// Filesharing
	//
	elseif ($sub=="filesharing")
	{
		$root = ADMIN_FILESHARING_DIR; 
	
	echo "<h2>Filesharing</h2>";
	
	if (isset($_GET['action']) && $_GET['action']=="rename")
	{
		$f = base64_decode($_GET['file']);
		if (md5($f) == $_GET['h'])
		{
			echo "<h2>Umbenennen</h2>
			<form action=\"?page=$page&sub=$sub\" method=\"post\">";
			echo "Dateiname: 
			<input type=\"text\" name=\"rename\" value=\"".$f."\" /> 
			<input type=\"hidden\" name=\"rename_old\" value=\"".$f."\" /> 
			&nbsp; <input type=\"submit\" name=\"rename_submit\" value=\"Umbenennen\" /> &nbsp; 
			</form>";
		}
		else
		{
			echo "Fehler im Dateinamen!";
		}		
	}
	else
	{
		if (isset($_FILES["datei"])) 
		{
		 	if(move_uploaded_file($_FILES["datei"]['tmp_name'],$root."/".$_FILES["datei"]['name']))
		 	{
	  		echo "Die Datei <b>".$_FILES["datei"]['name']."</b> wurde heraufgeladen!<br/><br/>";
	  	}
	  	else
	  	{
	  		echo "Fehler beim Upload!<br/><br/>";
	  	}
	  }
	  
	  if (isset($_POST['rename_submit']) && $_POST['rename']!="")
	  {
	  	rename($root."/".$_POST['rename_old'],$root."/".$_POST['rename']);
	  	echo "Datei wurde umbenannt!<br/><br/>";
	  }	  
		
		if (isset($_GET['action']) && $_GET['action']=="delete")
		{
			$f = base64_decode($_GET['file']);
			if (md5($f) == $_GET['h'])
			{
		  	@unlink($root."/".$f);
		  	echo "Datei wurde gelöscht!<br/><br/>";
			}
			else
			{
				echo "Fehler im Dateinamen!";
			}				
		}
		
		if ($d = opendir($root))
		{
			$cnt = 0;
			echo "<table class=\"tb\">
			<tr>
				<th>Datei</th>
				<th>Grösse</th>
				<th>Datum</th>
				<th style=\"width:150px;\">Optionen</th>
			</tr>";
			while ($f = readdir($d))
			{
				$file = $root."/".$f;
				if (is_file($file) && substr($f,0,1)!=".")
				{
					$dlink = "path=".base64_encode($file)."&hash=".md5($file);
					$link = "file=".base64_encode($f)."&h=".md5($f);
					echo "<tr>
						<td><a href=\"dl.php?".$dlink."\">$f</a></td>
						<td>".byte_format(filesize($file))."</td>
						<td>".df(filemtime($file))."</td>
						<td>
							<a href=\"?page=$page&amp;sub=$sub&amp;action=rename&".$link."\">Umbenennen</a>
							<a href=\"?page=$page&amp;sub=$sub&amp;action=delete&".$link."\" onclick=\"return confirm('Soll diese Datei wirklich gelöscht werden?')\">Löschen</a>
						</td>
					</tr>";				
					$cnt++;
				}			
			}
			if ($cnt==0)
			{
				echo "<tr><td colspan=\"4\"><i>Keine Dateien vorhanden!</i></td></tr>";
			}
			echo "</table>";
			closedir($d);
			
			echo "<h2>Upload</h2>
			<form method=\"post\" action=\"?page=$page&sub=$sub\" enctype=\"multipart/form-data\">
	    	<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"10000000\" />
	  		<input type=\"file\" name=\"datei\" size=\"40\" maxlength=\"10000000\" />
	  		<input type=\"submit\" name=\"submit\" value=\"Datei heraufladen\" />
			</form>		
			";		
		}
		else
		{
			echo "Verzeichnis $root kann nicht gefunden werden!";
		}
	}
	}

	//
	// IP-Resolver
	//
	elseif ($sub=="ipresolver")
	{
		$ip = "";
		$host = "";
		
		if (isset($_POST['resolve']))
		{
			if ($_POST['address']!="")
			{
				$ip = $_POST['address'];
				$host = resolveIp($_POST['address']);
				echo "Die IP <b>".$ip."</b> hat den Hostnamen <b>".$host."</b><br/>";
				
			}
			elseif ($_POST['hostname']!="")
			{
				$ip = gethostbyname($_POST['hostname']);
				$host = $_POST['hostname'];
				echo "Die Host <b>".$host."</b> hat die IP <b>".$ip."</b><br/>";
			}			
		}
		if (isset($_POST['whois']))
		{
			echo "<div style=\"border:1px solid #fff;background:#000;padding:3px;\">";
			$cmd = "whois ".$_POST['hostname'];
			$out = array();
			exec($cmd,$out);
			foreach ($out as $o)
			{
				echo "$o <br/>";
			}
			echo "</div>";
		}		
		echo "<h2>IP-Resolver</h2>";
		echo '<form action="?page='.$page.'&amp;sub='.$sub.'" method="post">';
		echo "IP-Adresse: <input type=\"text\" name=\"address\" value=\"$ip\" /><br/>";
		echo "oder Hostname: <input type=\"text\" name=\"hostname\" value=\"$host\" /><br/><br/>";
		echo "<input type=\"submit\" name=\"resolve\" value=\"Auflösen\" /> &nbsp; ";
		echo "<input type=\"submit\" name=\"whois\" value=\"WHOIS\" /><br/>";		
		echo "</form>";
	}

	//
	// PHP
	//
	elseif ($sub=="php")
	{
		echo "<h2>PHP-Infos</h2>";
		echo '<iframe src="phpinfo.php" style="width:850px;height:650px;" ></iframe>';
	}


	//
	// gamestats
	//
	elseif ($sub=="gamestats")
	{
		echo "<h2>Spielstatistiken</h2>";
		if (isset($_GET['regen']))
		{
			if (GameStats::generateAndSave())
			{
				ok_msg("Statistiken erneuert!");				
			}
		}
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;regen=1\">Erneuern</a><br/><br/>";		
		echo readfile(GAMESTATS_FILE);
	}

		
	else
	{
		echo "Wähle ein Tool aus dem Menü!";
	}

?>