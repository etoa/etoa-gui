<?PHP

	echo "<h1>Setup</h1>";
	
	//
	// Start-Items
	//
	if ($sub=="defaultitems")
	{
		include("config/defaultitems.inc.php");
	}
	
	//
	// Cronjob
	//
	elseif ($sub=="cronjob")
	{
		echo "<h2>Update-Skript</h2>";
		
		if (UNIX)
		{
			echo "
			<h3>Unix-Cronjob einrichten</h3>
			<ol>
			<li>Auf den Server einloggen (z.B. via SSH) resp. eine Shell/Kommandozeile öffnen</li>
			<li>Folgenden Befehl eingeben: <i>crontab -e</i>
			<li>Diese Zeile einfügen: ";
			$dname = dirname(realpath("../conf.inc.php"));
			echo "<p><span style=\"border:1px solid #fff;background:#000;padding:5px;\">";
			echo "* * * * * php ".$dname."/scripts/update.php";
			echo "</span></p></li>
			<li>Die Datei speichern und den Editor beenden
			<ul><li>Falls der Editor Vim ist: <i>ESC</i> drücken, <i>:wq</i> eingeben</li>
			<li>Falls der Editor Nano ist: <i>CTRL+X</i> drücken und Speichern mit <i>Y</i> bestätigen</li></ul>
			</li>
			<li>Resultat mit <i>crontab -l</i> prüfen</li>
			</ol>";
			echo "<h3>Aktuelle Crontab</h3>
			<p><div style=\"border:1px solid #fff;background:#000;padding:5px;\">";
			ob_start();
			echo "Crontab-User: ";
			passthru("id");
			echo "\n\n";
			passthru("crontab -l");
			echo nl2br(ob_get_clean());
			echo "</div></p>";
		}
		else
		{
			echo "Cronjobs sind nur auf UNIX-Systemen verfügbar!";
		}
		
	}
		
	//
	// Bildpakete
	//
	elseif ($sub=="imagepacks")
	{
		echo "<h2>Bildpakete verwalten</h2>";

		$imPackDir = "../images/imagepacks";
		$baseType = "png";

		if (isset($_GET['manage']))
		{
			if (is_dir($imPackDir."/".$_GET['manage']))
			{
				$cdir = $imPackDir."/".$_GET['manage'];
				if ($xml = simplexml_load_file($cdir."/imagepack.xml"))
				{
					echo "<h3>".$xml->name."</h3>";
					echo "Autor: ".$xml->author." (".$xml->email.")<br/><br/>";

					$tmpexts = explode(",",$xml->extensions);
					$exts = array();
					foreach ($tmpexts as $tmpext)
					{
						if ($tmpext=="png") $exts[] = "png";
						if ($tmpext=="jpeg") $exts[] = "jpg";
						if ($tmpext=="jpg") $exts[] = "jpg";
						if ($tmpext=="gif") $exts[] = "gif";
					}
					if (count($exts) == 0) $exts[] = $baseType;

					$sizes = array("" => $cfg->value('imagesize'),"_middle" => $cfg->p1('imagesize'),"_small" => $cfg->p2('imagesize'));

					$dira = array(
						"abuildings" => array("building",getArrayFromTable("alliance_buildings","alliance_building_id")),
						"atechnologies" => array("technology",getArrayFromTable("alliance_technologies","alliance_tech_id")),
						"buildings" => array("building",getArrayFromTable("buildings","building_id")),
						"defense" => array("def",getArrayFromTable("defense","def_id")),
						"missiles" => array("missile",getArrayFromTable("missiles","missile_id")),
						"ships" => array("ship",getArrayFromTable("ships","ship_id")),
						"stars" => array("star",getArrayFromTable("sol_types","sol_type_id")),
						"technologies" => array("technology",getArrayFromTable("technologies","tech_id")),
						"nebulas" => array("nebula",range(1,$cfg->value('num_nebula_images'))),
						"asteroids" => array("asteroids",range(1,$cfg->value('num_asteroid_images'))),
						"space" => array("space",range(1,$cfg->value('num_space_images'))),
						"wormholes" => array("wormhole",range(1,$cfg->value('num_wormhole_images'))),
						"races" => array("race",getArrayFromTable("races","race_id")),
					);

					foreach ($dira as $sdir => $sd)
					{
						$sprefix = $sd[0];
						if (is_dir($cdir."/".$sdir))
						{
							foreach ($sd[1] as $idx)
							{
								$baseFileStr = $sdir."/".$sprefix.$idx.".".$baseType;
								$baseFile = $cdir."/".$baseFileStr;
								if (!is_file($baseFile))
								{
									echo "<i>Basisbild fehlt: $baseFile</i><br/>";
								}
								else
								{
									foreach ($exts as $ext)
									{
										foreach ($sizes as $sizep => $sizew)
										{
											$filestr = $sdir."/".$sprefix.$idx.$sizep.".".$ext;
											$file = $cdir."/".$filestr;
											if (is_file($file))
											{
												$sa = getimagesize($file);
												if ($sa[0] != $sizew)
												{
													echo "Falsche Grösse: <i>$filestr</i> (".$sa[0]." statt $sizew).";
													if (resizeImage($file, $file, $sizew,$sizew, $ext))
														echo "<span style=\"color:#0f0;\">KORRIGIERT!</span>";
													echo "<br/>";
												}
											}
											else
											{
												echo "<i>Fehlt: $filestr</i>";
												if (resizeImage($baseFile, $file, $sizew,$sizew, $ext))
													echo "<span style=\"color:#0f0;\">KORRIGIERT!</span>";
												echo "<br/>";
											}
										}
									}
								}
							}
						}		
						else
						{
							echo "Verzeichnis fehlt: $sdir<br/>";
						}				
					}


					echo button("Zurück","?page=$page&amp;sub=$sub");
					
				}
			}
			
		}
		else
		{
	

			if ($d = opendir($imPackDir))
			{
				tableStart("Vorhandene Bildpakete");
				while ($f = readdir($d))
				{
					if (substr($f,0,1)!="." && is_dir($imPackDir."/".$f))
					{
						$cdir = $imPackDir."/".$f;
						if ($xml = simplexml_load_file($cdir."/imagepack.xml"))
						{
							echo "<tr>
							<td><a href=\"?page=$page&amp;sub=$sub&amp;manage=".$f."\">".$xml->name."</a></td>
							<td>".$xml->author."</td>
							<td>".$xml->email."</td>
							<td>".$xml->extensions."</td>
							</tr>";
						}					
					}
				}			
				tableEnd();
				closedir($d);
			}
	
	
			echo "<h2>Downloadbare Bildpakete erzeugen</h2>";
	
			$pkg = new ImagePacker("../images/imagepacks","../cache/imagepacks");
	
			if (isset($_GET['gen']))
			{
				echo "Erstelle Pakate...<br/><div style=\"border:1px solid #fff;\">";
				$pkg->pack();
				echo "</div><br/>";
			}
	
			if ($pkg->checkPacked())
			{
			 echo "<div style=\"color:#0f0\">Bildpakete sind vorhanden!</div>";
			}
			else
			{
			 echo "<br/><div style=\"color:#f00\">Bildpakete sind NICHT vollständig vorhanden!</div>";
			}
			echo "<br/><br/>";
	
			if (UNIX)
			{
				echo "<a href=\"?page=$page&amp;sub=$sub&amp;gen=1\">Neu erstellen</a>";
			}
			else
			{
				error_msg("Bildpakete können nur auf einem Unix System erstellt werden!");
			}		
		}	
	}

	//
	// Universe Maintenance
	//
	elseif ($sub=="uni")
	{

		//
		// Universum erstellen
		//
		if (isset($_POST['submit_create_universe']))
		{
			echo "<h2>Urknall - Schritt 2/3</h2>";
			$cfg->set("num_of_sectors","",$_POST['num_of_sectors_p1'],$_POST['num_of_sectors_p2']);
			$cfg->set("num_of_cells","",$_POST['num_of_cells_p1'],$_POST['num_of_cells_p2']);
			$cfg->set("space_percent_solsys",intval($_POST['space_percent_solsys']));
			$cfg->set("space_percent_asteroids",intval($_POST['space_percent_asteroids']));
			$cfg->set("space_percent_nebulas",intval($_POST['space_percent_nebulas']));
			$cfg->set("space_percent_wormholes",intval($_POST['space_percent_wormholes']));
			$cfg->set("num_planets","",$_POST['num_planets_p1'],$_POST['num_planets_p2']);
			$cfg->set("solsys_percent_planet",intval($_POST['solsys_percent_planet']));
			$cfg->set("solsys_percent_asteroids",intval($_POST['solsys_percent_asteroids']));
			$cfg->set("planet_fields","",$_POST['planet_fields_p1'],$_POST['planet_fields_p2']);

			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			tableStart("Systfemanordnung",400);
			$xdim = ($cfg->num_of_sectors->p1*$cfg->num_of_cells->p1);
			$ydim = ($cfg->num_of_sectors->p2*$cfg->num_of_cells->p2);
			
			echo "<tr>
				<th>Dimension:</th>
				<td>".$xdim."x".$ydim." Zellen</td>
			</tr>";
			echo "<tr>
				<th>Karte:</th>
				<td>";
				echo "<input type=\"radio\" name=\"map_image\" value=\"\" checked=\"checked\" /> <img style=\"width:".$xdim."px;height:".$ydim."px;\" src=\"../images/galaxylayout_random.png\" /> Zufällig";
				$dir = "../images/galaxylayouts";
				$d = opendir($dir);
				while ($f = readdir($d))
				{
					if (is_file($dir.DIRECTORY_SEPARATOR.$f) && substr($f,strrpos($f,".png"))==".png" && $ims = getimagesize($dir.DIRECTORY_SEPARATOR.$f))
					{
						if ($ims[0]==$xdim && $ims[1]==$ydim)
						{
							echo "<div><input type=\"radio\" name=\"map_image\" value=\"$f\" /> <img src=\"".$dir."/".$f."\" alt=\"".$dir."/".$f."\" /> ".basename($f,".png")."	</div>";							
						}
					}
				}			
				echo "</td>
			</tr>";
			echo "<tr>
				<th>Genauigkeit:</th>
				<td><input type=\"text\" name=\"map_precision\" value=\"95\" size=\"2\" maxlength=\"3\"/>%</td>
			</tr>";
			tableEnd();
			
			echo button("Zurück","?page=$page&amp;sub=$sub")." &nbsp; <input onclick=\"return confirm('Universum wirklich erstellen?')\" type=\"submit\" name=\"submit_create_universe2\" value=\"Weiter\" >";
			echo "</form>";
		}
		// Erweitern
		elseif(isset($_POST['submit_expansion_universe']))
		{
			echo "<h2>Universum erweitern</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<b>Universum (".$conf['num_of_sectors']['p1']."x".$conf['num_of_sectors']['p2'].") erweitern</b><br><br>";
			echo "Erweitere das Universum. Es werden dabei die bereits gespeicherten Daten &uuml;bernommen bez&uuml;glich der der Aufteilung von Planeten, Sonnensystemen, Gasplaneten, Wurml&ouml;chern etc. &Auml;ndere allenfals die Daten unter dem Link \"Universum\".<br><br>";

			echo "Gr&ouml;sse nach dem Ausbau: ";
			//erstellt 2 auswahllisten für die ausbaugrösse
  	      echo "<select name=\"expansion_sector_x\">";
  	      for ($x=($conf['num_of_sectors']['p1']+1);10>=$x;$x++)
  	      {
  	              echo "<option value=\"$x\">$x</option>";
  	      }
  	      echo "</select>";
  	      echo " x ";
  	      echo "<select name=\"expansion_sector_y\">";
  	      for ($x=($conf['num_of_sectors']['p2']+1);10>=$x;$x++)
  	      {
  	              echo "<option value=\"$x\">$x</option>";
  	      }
  	      echo "</select>";
  	      echo "<br>";

			echo "<input onclick=\"return confirm('Universum wirklich erweitern?')\" type=\"submit\" name=\"submit_expansion_universe2\" value=\"Erweitern\" >";
			echo "</form>";
		}
		// Reset
		elseif (isset($_POST['submit_reset']))
		{
			echo "<h2>Runde zur&uuml;cksetzen</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "Runde wirklich zur&uuml;cksetzen?<br/><br/>";
			echo "<input onclick=\"return confirm('Reset wirklich durchf&uuml;hren?')\" type=\"submit\" name=\"submit_reset2\" value=\"Ja, die gesamte Runde zur&uuml;cksetzen\" >";
			echo "</form>";
		}
		
		elseif (isset($_POST['submit_galaxy_reset']))
		{
			$res = dbquery("SELECT COUNT(id) FROM planets WHERE planet_user_id>0;");
			$arr = mysql_fetch_row($res);					
			if ($arr[0]==0)
			{			
				Universe::reset(false);
				echo "Das Universum wurde zurückgesetzt!<br/><br/>".button("Weiter","?page=$page&amp;sub=$sub");
			}
			else
			{
				error_msg("Es sind bereits Planetenbesitzer vorhanden!");
			}
		}
		
		elseif(isset($_POST['submit_reset2']))
		{
      Universe::reset();
			echo "Die Runde wurde zurückgesetzt!<br/><br/>".button("Weiter","?page=$page&amp;sub=$sub");
		}

		// Uni-Optionen
		else
		{
			if(isset($_POST['submit_create_universe2']))
			{
				echo "<h2>Urknall - Schritt 3/3</h2>";
				Universe::create($_POST['map_image'],$_POST['map_precision']);
				echo "<br/><br/>
				<img src=\"../misc/map.image.php\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/><br/><br/>
				<input type=\"button\" value=\"Weiter\" onclick=\"document.location='?page=$page&sub=uni'\" />";
			}
			else
			{
	
				// Check if universe exists
				$res = dbquery("SELECT COUNT(id) FROM cells;");
				$arr = mysql_fetch_row($res);
				if ($arr[0]==0)
				{
	        echo "<h2>Urknall - Schritt 1/3</h2>";
 	        echo "Das Universum existiert noch nicht, bitte prüfe die Einstellungen und klicke auf 'Weiter':<br/><br/>";

					?>
					<script type="text/javascript">
						function alignSystemPercentage()
						{
							sum = parseInt(document.getElementById('space_percent_solsys').value)+
							parseInt(document.getElementById('space_percent_asteroids').value)+
							parseInt(document.getElementById('space_percent_nebulas').value)+
							parseInt(document.getElementById('space_percent_wormholes').value);
							res = 100 - sum
							document.getElementById('space_percent_empty').value = res.toString();
							if (res < 0 || res > 100)
								document.getElementById('space_percent_empty').style.color = "red";
							else
								document.getElementById('space_percent_empty').style.color = "";
						}
						function alignObjectsInSystemPercentage()
						{
							sum = parseInt(document.getElementById('solsys_percent_planet').value)+
							parseInt(document.getElementById('solsys_percent_asteroids').value);
							res = 100 - sum
							document.getElementById('solsys_percent_empty').value = res.toString();
							if (res < 0 || res > 100)
								document.getElementById('solsys_percent_empty').style.color = "red";
							else
								document.getElementById('solsys_percent_empty').style.color = "";
						}				
					</script>						
					<?PHP
					
					echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
					echo "Alle Einstellungen werden von der <a href=\"?page=config&cid=3\">Konfiguration</a> &uuml;bernommen!<br/><br/>";
					
					tableStart("Galaxie",400);			
					echo "<tr>
						<th>Sektoren:</th>
						<td>
							<input type=\"text\" name=\"num_of_sectors_p1\" value=\"".$cfg->num_of_sectors->p1."\" size=\"2\" maxlength=\"2\" />x
							<input type=\"text\" name=\"num_of_sectors_p2\" value=\"".$cfg->num_of_sectors->p2."\" size=\"2\" maxlength=\"2\" />
						</td></tr>";
					echo "<tr>
						<th>Anzahl Zellen pro Sektor:</th>
						<td>
							<input type=\"text\" name=\"num_of_cells_p1\" value=\"".$cfg->num_of_cells->p1."\" size=\"2\" maxlength=\"2\" />x
							<input type=\"text\" name=\"num_of_cells_p2\" value=\"".$cfg->num_of_cells->p2."\" size=\"2\" maxlength=\"2\" />
						</td></tr>";
					echo "</table>";
					
					tableStart("Verteilung der Systeme",300);				
					echo "<tr>
						<th>Sternensysteme:</th>
						<td><input type=\"text\" name=\"space_percent_solsys\" id=\"space_percent_solsys\" value=\"".$cfg->space_percent_solsys."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
					</tr>";
					echo "<tr>
						<th>Asteroidenfelder:</th>
						<td><input type=\"text\" name=\"space_percent_asteroids\" id=\"space_percent_asteroids\" value=\"".$cfg->space_percent_asteroids."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
					</tr>";
					echo "<tr>
						<th>Nebelwolken:</th>
						<td><input type=\"text\" name=\"space_percent_nebulas\" id=\"space_percent_nebulas\" value=\"".$cfg->space_percent_nebulas."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
					</tr>";
					echo "<tr>
						<th>Wurmlöcher:</th>
						<td><input type=\"text\" name=\"space_percent_wormholes\" id=\"space_percent_wormholes\" value=\"".$cfg->space_percent_wormholes."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignSystemPercentage()\" />%</td>
					</tr>";
					echo "<tr>
						<th>Leerer Raum:</th>
						<td><input type=\"text\" id=\"space_percent_empty\" value=\"\" size=\"2\" maxlength=\"2\" readonly=\"readonly\"/>%</td>
					</tr>";
					echo "</table>";			
					
					tableStart("Sternensystem",400);				
					echo "<tr>
						<th>Objekte pro Sternensystem:</th>
						<td><input type=\"text\" name=\"num_planets_p1\" value=\"".$cfg->num_planets->p1."\" size=\"2\" maxlength=\"2\" /> min, 
						    <input type=\"text\" name=\"num_planets_p2\" value=\"".$cfg->num_planets->p2."\" size=\"2\" maxlength=\"2\" /> max
						</td></tr>";
					echo "<tr>
						<th>Planeten:</th>
						<td><input type=\"text\" name=\"solsys_percent_planet\" id=\"solsys_percent_planet\" value=\"".$cfg->solsys_percent_planet."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignObjectsInSystemPercentage()\" />%</td>
					</tr>";
					echo "<tr>
						<th>Asteroidenfelder:</th>
						<td><input type=\"text\" name=\"solsys_percent_asteroids\" id=\"solsys_percent_asteroids\" value=\"".$cfg->solsys_percent_asteroids."\" size=\"2\" maxlength=\"2\" onkeyup=\"alignObjectsInSystemPercentage()\" />%</td>
					</tr>";
					echo "<tr>
						<th>Leerer Raum:</th>
						<td><input type=\"text\" id=\"solsys_percent_empty\" value=\"\" size=\"2\" maxlength=\"2\" readonly=\"readonly\" />%</td>
					</tr>";				
						
					echo "</table>";
		
					tableStart("Planeten",400);				
					echo "<tr>
						<th>Felder pro Planet:</th>
						<td>
							<input type=\"text\" name=\"planet_fields_p1\" value=\"".$cfg->planet_fields->p1."\" size=\"2\" maxlength=\"2\" /> min, 
							<input type=\"text\" name=\"planet_fields_p2\" value=\"".$cfg->planet_fields->p2."\" size=\"2\" maxlength=\"2\" /> max
						</td>
					</tr>";
					echo "</table>";
		
					echo "<script type=\"text/javascript\">
						alignSystemPercentage();
						alignObjectsInSystemPercentage();
					</script>";	        
	        
	        echo "<br/><input type=\"submit\" name=\"submit_create_universe\" value=\"Weiter\" >";
	        echo "</form><br/>";
		  	}
				else
				{
					echo "<h2>Universum</h2>";
					echo "<div style=\"float:right;width:500px\"><img src=\"../misc/map.image.php\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/></div>";

					tableStart("Informationen");
					$res = dbquery("SELECT MAX(sx),MAX(sy) FROM cells;");
					$arr = mysql_fetch_row($res);
					echo "<tr><th>Sektoren</th><td>".$arr[0]." x ".$arr[1]."</td></tr>";
					$res = dbquery("SELECT MAX(cx),MAX(cy) FROM cells;");
					$arr = mysql_fetch_row($res);
					echo "<tr><th>Zellen pro Sektor</th><td>".$arr[0]." x ".$arr[1]."</td></tr>";

					$res = dbquery("SELECT COUNT(*) FROM stars;");
					$arr = mysql_fetch_row($res);
					echo "<tr><th>Sterne</th><td>".nf($arr[0])."</td></tr>";
					$res = dbquery("SELECT COUNT(*) FROM planets;");
					$arr = mysql_fetch_row($res);
					echo "<tr><th>Planeten</th><td>".nf($arr[0])."</td></tr>";
					$res = dbquery("SELECT COUNT(*) FROM asteroids;");
					$arr = mysql_fetch_row($res);
					echo "<tr><th>Asteroidenfelder</th><td>".nf($arr[0])."</td></tr>";
					$res = dbquery("SELECT COUNT(*) FROM nebulas;");
					$arr = mysql_fetch_row($res);
					echo "<tr><th>Nebel</th><td>".nf($arr[0])."</td></tr>";
					$res = dbquery("SELECT COUNT(*) FROM wormholes;");
					$arr = mysql_fetch_row($res);
					echo "<tr><th>Wurmlöcher</th><td>".nf($arr[0])."</td></tr>";
					$res = dbquery("SELECT COUNT(*) FROM space;");
					$arr = mysql_fetch_row($res);
					echo "<tr><th>Leerer Raum</th><td>".nf($arr[0])."</td></tr>";
					
					tableEnd();
					
          echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";

					$res = dbquery("SELECT COUNT(id) FROM planets WHERE planet_user_id>0;");
					$arr = mysql_fetch_row($res);					
					if ($arr[0]!=0)
					{
						echo "<h3>Universum löschen</h3>";
	          echo "Es sind noch keine Planeten im Besitz von Spielern. Das Universum kann ohne Probleme gelöscht werden.<br/><br/>
	          <input type=\"submit\" name=\"submit_galaxy_reset\" value=\"Universum zurücksetzen\" ><br/>";
					}
					else
					{
						echo "<h3>Universum löschen</h3>";
	          echo "Es sind bereits Planeten im Besitz von Spielern. Du kannst das Universum zurücksetzen, jedoch werden 
	          sämtliche Gebäude, Schiffe, Forschungen etc von den Spielern gelöscht.<br/><br/>
	          <input type=\"submit\" name=\"submit_galaxy_reset\" value=\"Universum zurücksetzen\" onclick=\"return confirm('Universum wirklich zurücksetzen? ALLE Einheiten der Spieler werden gelöscht, jedoch keine Spieleraccounts!')\"><br/>";
					}
	
	        // Reset
	        echo "<h3>Runde komplett zur&uuml;cksetzen</h3>";
          echo "Hiermit kann die gesamte Runde zurückgesetzt werden (User, Allianzen, Planeten).<br/><br/>";
          echo "<input type=\"submit\" name=\"submit_reset\" value=\"Ja, die gesamte Runde zur&uuml;cksetzen\" ><br><br>";

          echo "</form>";
	    	}
	    }
		}
	}
	
	else
	{
		echo "<h2>Übersicht</h2>";
		echo "Wähle eine Unterseite aus dem Menü links.";
		//Cache::checkPerm();

	}

?>