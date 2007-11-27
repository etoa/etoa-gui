<?PHP
	echo "<h1>XML-Import/Export</h1>";
	
	cache::checkPerm("user_xml","../");
	$path = "../".CACHE_ROOT."/user_xml";
	
	//
	// Import
	//
	if (isset($_GET['import']))
	{
		$file = $path."/".base64_decode($_GET['import']);
		if (is_file($file))
		{
			
		}
		echo "IN ARBEIT";
	}	
	
	//
	// Details
	//
	elseif (isset($_GET['file']))
	{
		$file = $path."/".base64_decode($_GET['file']);
		if (is_file($file))
		{
			$xml = simplexml_load_file($file);
			
			echo "<h2>Details ".base64_decode($_GET['file'])."</h2>";
			infobox_start("Allgemeines",1);
			if (isset($xml->export))
			{
				echo "<tr>
					<th class=\"tbltitle\" style=\"width:150px;\">Erstellt am:</th>
					<td class=\"tbldata\">".$xml->export['date']."</td>
				</tr>";
			}
			if (isset($xml->account))
			{
				echo "<tr>
					<th class=\"tbltitle\">ID:</th>
					<td class=\"tbldata\">".$xml->account->id."</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">Nick:</th>
					<td class=\"tbldata\">".$xml->account->nick."</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">Name:</th>
					<td class=\"tbldata\">".$xml->account->name."</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">E-Mail:</th>
					<td class=\"tbldata\">".$xml->account->email."</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">Punkte:</th>
					<td class=\"tbldata\">".$xml->account->points."</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">Rank:</th>
					<td class=\"tbldata\">".$xml->account->rank."</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">Online:</th>
					<td class=\"tbldata\">".$xml->account->online."</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">IP:</th>
					<td class=\"tbldata\">".$xml->account->ip."</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">Host:</th>
					<td class=\"tbldata\">".$xml->account->host."</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">Allianz:</th>
					<td class=\"tbldata\">[".$xml->account->alliance['tag']."] ".$xml->account->alliance." (Id: ".$xml->account->alliance['id'].")</td>
				</tr>";
				echo "<tr>
					<th class=\"tbltitle\">Rasse:</th>
					<td class=\"tbldata\">".$xml->account->race." (Id: ".$xml->account->race['id'].")</td>
				</tr>";
				
				
			}
			infobox_end(1);
	
			infobox_start("Planeten",1);
			if (isset($xml->planets))
			{
				if (isset($xml->planets->planet))
				{
					foreach ($xml->planets->planet as $p)
					{
						echo "<tr>
							<th class=\"tbltitle\" rowspan=\"7\" style=\"width:200px;\">".$p['name']."<br/> (Id: ".$p['id']."";
							if ($p['id']==1) echo " HAUPTPLANET";
							echo ")</th>
							<td class=\"tbldata\">Typ:</td>
							<td class=\"tbldata\">".$p->type." (Id: ".$p->type['id'].")</td>
						</tr>";
						echo "<tr>
							<td class=\"tbldata\">".RES_METAL.":</td>
							<td class=\"tbldata\"> ".nf($p->metal)."</td>
						</tr>";
						echo "<tr>
							<td class=\"tbldata\">".RES_CRYSTAL.":</td>
							<td class=\"tbldata\"> ".nf($p->crystal)."</td>
						</tr>";
						echo "<tr>
							<td class=\"tbldata\">".RES_PLASTIC.":</td>
							<td class=\"tbldata\"> ".nf($p->plastic)."</td>
						</tr>";
						echo "<tr>
							<td class=\"tbldata\">".RES_FUEL.":</td>
							<td class=\"tbldata\"> ".nf($p->fuel)."</td>
						</tr>";
						echo "<tr>
							<td class=\"tbldata\">".RES_FOOD.":</td>
							<td class=\"tbldata\"> ".nf($p->food)."</td>
						</tr>";
						echo "<tr>
							<td class=\"tbldata\">Bewohner:</td>
							<td class=\"tbldata\"> ".nf($p->people)."</td>
						</tr>";
					
					}
				}
			}
			infobox_end(1);	

			infobox_start("Gebäude",1);
			if (isset($xml->buildings))
			{
				if (isset($xml->buildings->building))
				{
					foreach ($xml->buildings->building as $p)
					{
						echo "<tr>
							<th class=\"tbltitle\" style=\"width:200px;\">".$p."</th>
							<td class=\"tbldata\">Stufe: ".$p['level']."</td>
							<td class=\"tbldata\">Planet: ".$p['planet']."</td>
							<td class=\"tbldata\">Objekt-Id: ".$p['id']."</td>
						</tr>";
					}
				}
			}
			infobox_end(1);	
	
			infobox_start("Technologien",1);
			if (isset($xml->technologies))
			{
				if (isset($xml->technologies->technology))
				{
					foreach ($xml->technologies->technology as $p)
					{
						echo "<tr>
							<th class=\"tbltitle\" style=\"width:200px;\">".$p."</th>
							<td class=\"tbldata\">Stufe: ".$p['level']."</td>
							<td class=\"tbldata\">Objekt-Id: ".$p['id']."</td>
						</tr>";
					}
				}
			}
			infobox_end(1);	
			
			infobox_start("Schiffe",1);
			if (isset($xml->ships))
			{
				if (isset($xml->ships->ship))
				{
					foreach ($xml->ships->ship as $p)
					{
						echo "<tr>
							<th class=\"tbltitle\" style=\"width:200px;\">".$p."</th>
							<td class=\"tbldata\">Anzahl: ".$p['count']."</td>
							<td class=\"tbldata\">Planet: ".$p['planet']."</td>
							<td class=\"tbldata\">Objekt-Id: ".$p['id']."</td>
						</tr>";
					}
				}
			}
			infobox_end(1);			
			
			infobox_start("Verteidigung",1);
			if (isset($xml->defenses))
			{
				if (isset($xml->defenses->defense))
				{
					foreach ($xml->defenses->defense as $p)
					{
						echo "<tr>
							<th class=\"tbltitle\" style=\"width:200px;\">".$p."</th>
							<td class=\"tbldata\">Anzahl: ".$p['count']."</td>
							<td class=\"tbldata\">Planet: ".$p['planet']."</td>
							<td class=\"tbldata\">Objekt-Id: ".$p['id']."</td>
						</tr>";
					}
				}
			}
			infobox_end(1);						
			
		}
		else
		{
			error_msg("Datei $file nicht gefunden!");
		}		
		echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub'\" value=\"Übersicht\" /> &nbsp;";
		echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub&import=".$_GET['file']."'\" value=\"Import\" />";
	}
	
	//
	// Overview
	//
	else
	{	
		echo "<h2>Export</h2>";
		if (isset($_POST['exportcache']))
		{
			$uti = new UserToXml($_POST['export_user_id']);
			$xmlfile = $uti->toCacheFile("../");
			if ($xmlfile)
			{
				success_msg("Die Userdaten wurden nach [b]".$xmlfile."[/b] exportiert.");
			}
		}
		if (isset($_POST['exportdl']))
		{
			echo "<script type=\"text/javascript\">window.open('misc/user_xml.php?id=". $_POST['export_user_id']."');</script>";
		}
	
		echo "Bei jeder Löschung eines Spielers werden automatisch seine Daten
		in ein XML-File geschrieben und dieses in einem Ordner abgelegt. Wenn du manuell von
		einem User ein Backup erstellen willst, kannst du das hier tun:<br/><br/>";
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "Spieler wählen: <select name=\"export_user_id\">";
		$res = dbquery("
		SELECT
			user_id,
			user_nick
		FROM
			users
		ORDER BY
			user_nick;		
		");
		if (mysql_num_rows($res)>0)
		{
			while($arr=mysql_fetch_array($res))
			{
				echo "<option value=\"".$arr['user_id']."\">".$arr['user_nick']."</option>";
			}		
		}
		echo "</select> 
		<input type=\"submit\" name=\"exportcache\" value=\"Exportieren\" /> 
		<input type=\"submit\" name=\"exportdl\" value=\"Herunterladen\" />";
		
		echo "<h2>Import</h2>";
		$d = opendir($path);
		echo "<table class=\"tb\">
		<tr><th>Datei (Userid_Datum_Zeit)</th>
		<th>Spieler</th>
		<th>Datum</th>
		<th>Optionen</th></tr>";
		while ($f = readdir($d))
		{
			$file = $path."/".$f;
			if (is_file($file) && stristr($f,".xml"))
			{			
				$dlink = "path=".base64_encode($file)."&hash=".md5($file);
				$xml = simplexml_load_file($file);
				echo "<tr>
				<td>$f</td>
				<td>".$xml->account->nick."</td>
				<td>".$xml->export['date']."</td>
				<td>
					<a href=\"?page=$page&amp;sub=$sub&amp;file=".base64_encode($f)."\">Details & Import</a> &nbsp;
					<a href=\"dl.php?".$dlink."\">Download</a></td>
				</tr>";
			}
		}
		echo "</table>";
		closedir($d);
		echo "</form>";
	}
?>