<?PHP
echo "<h1>Eventhandler</h1>";
if (UNIX)
{
	$frm = new Form("bustn","?page=$page&amp;sub=$sub");
	echo $frm->begin();
	
	tableStart("Daemon-Infos");
	$un=posix_uname();
	echo "<tr><th>System</th><td>".$un['sysname']." ".$un['release']." ".$un['version']."</td></tr>";
	echo "<tr><th>Pfad</th><td>".$cfg->daemon_exe."</td></tr>";
	echo "<tr><th>Logfile</th><td>".$cfg->daemon_logfile."</td></tr>";
	echo "<tr><th>Pidfile</th><td>".$cfg->daemon_pidfile."</td></tr>";
	echo "<tr><th>Status</th>";
	if ($pid = checkDaemonRunning($cfg->daemon_pidfile))
		echo "<td style=\"color:#0f0;\">Online, PID $pid</td>";
	else
		echo "<td style=\"color:red;\">LÄUFT NICHT!</td>";
	echo "</tr>";	
	tableEnd();
	echo $frm->end();		


	echo "<h2>Log</h2>";
	// Warning: Open-Basedir restrictions may appply
	if (is_file($cfg->daemon_logfile))
	{
		echo "<div id=\"logtext\" style=\"border:1px solid white;background:black;padding:3px;overflow:scroll;height:400px\">";
		$lf = fopen($cfg->daemon_logfile,"r");
		while($l = fgets($lf))
		{
			if (stristr($l,"warning]"))
				echo "<span style=\"color:orange;\">";
			elseif (stristr($l,"err]"))
				echo "<span style=\"color:red;\">";
			elseif(stristr($l,"notice]"))
				echo "<span style=\"color:#afa;\">";
			else
				echo "<span>";
			echo $l."</span><br/>";
		}
		fclose($lf);
		echo "</div>";

		echo "<script type=\"text/javascript\">
		textareaelem = document.getElementById('logtext');
		textareaelem.scrollTop = textareaelem.scrollHeight;
		</script>";
	}
	else
	{
		echo "<div style=\"color:red;\">Die Logdatei ".$cfg->daemon_logfile." kann nicht geöffnet werden!</div>";
	}
}
else
{
	echo "Der Backend-Daemon wird nur auf UNIX-Systemen unterstüzt!";
}
?>