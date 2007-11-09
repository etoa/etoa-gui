<?PHP
	$root = "upload";
	
	echo "<h1>Filesharing</h1>";
	
	if ($_GET['action']=="rename")
	{
		$f = base64_decode($_GET['file']);
		if (md5($f) == $_GET['h'])
		{
			echo "<h2>Umbenennen</h2>
			<form action=\"?page=$page\" method=\"post\">";
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
		
		if ($_GET['action']=="delete")
		{
			$f = base64_decode($_GET['file']);
			if (md5($f) == $_GET['h'])
			{
		  	unlink($root."/".$f);
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
				if (is_file($file))
				{
					$dlink = "dl=".base64_encode($file)."&h=".md5($file);
					$link = "file=".base64_encode($f)."&h=".md5($f);
					echo "<tr>
						<td><a href=\"dl.php?".$dlink."\">$f</a></td>
						<td>".byte_format(filesize($file))."</td>
						<td>".df(filemtime($file))."</td>
						<td>
							<a href=\"?page=$page&amp;action=rename&".$link."\">Umbenennen</a>
							<a href=\"?page=$page&amp;action=delete&".$link."\" onclick=\"return confirm('Soll diese Datei wirklich gelöscht werden?')\">Löschen</a>
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
			<form method=\"post\" action=\"?page=$page\" enctype=\"multipart/form-data\">
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


?>