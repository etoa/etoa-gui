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
			if (GameStats::generateAndSave(GAMESTATS_FILE))
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