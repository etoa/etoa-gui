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
  		echo "<h2>Urknall</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "Neues Universum erstellen? (Alle Einstellungen werden von der <a href=\"?page=config&cid=3\">Konfiguration</a> &uuml;bernommen!)<br/><br/>";
			
			echo "<table class=\"tb\" style=\"width:400px;\">";
			echo "<tr><th>Anzahl Sektoren X:</th><td>".$cfg->param1('num_of_sectors')."</td></tr>";
			echo "<tr><th>Anzahl Sektoren Y:</th><td>".$cfg->param2('num_of_sectors')."</td></tr>";
			echo "<tr><th>Anzahl Zellen X:</th><td>".$cfg->param1('num_of_cells')."</td></tr>";
			echo "<tr><th>Anzahl Zellen Y:</th><td>".$cfg->param2('num_of_cells')."</td></tr>";
			echo "<tr><th>Minimale Felder pro Planet:</th><td>".$cfg->param1('planet_fields')."</td></tr>";
			echo "<tr><th>Maximale Felder pro Planet:</th><td>".$cfg->param2('planet_fields')."</td></tr>";
			echo "<tr><th>Minimale Planetentemparatur:</th><td>".$cfg->param1('planet_temp')."</td></tr>";
			echo "<tr><th>Maximale Planetentemparatur:</th><td>".$cfg->param2('planet_temp')."</td></tr>";
			echo "<tr><th>Planetentemperaturdifferent:</th><td>".$cfg->value('planet_temp')."</td></tr>";
			echo "<tr><th>Anzahl Sternensysteme %:</th><td>".$cfg->value('space_percent_solsys')."</td></tr>";
			echo "<tr><th>Anzahl Asteroidenfelder %:</th><td>".$cfg->value('space_percent_asteroids')."</td></tr>";
			echo "<tr><th>Anzahl Nebelwolken %:</th><td>".$cfg->value('space_percent_nebulas')."</td></tr>";
			echo "<tr><th>Anzahl Wurmlöcher %:</th><td>".$cfg->value('space_percent_wormholes')."</td></tr>";
			echo "<tr><th>Maximale Anzahl Planeten/Sternensystem:</th><td>".$cfg->param1('num_planets')."</td></tr>";
			echo "<tr><th>Minimale Anzahl Planeten/Sternensystem:</th><td>".$cfg->param2('num_planets')."</td></tr>";
			echo "<tr><th>Anzahl verschiedener Planetenbilder / Typ:</th><td>".$cfg->value('num_planet_images')."</td></tr>";
			echo "</table><br/>";

			$imgpath = "../images/galaxylayouts/".($cfg->param1('num_of_sectors')*$cfg->param1('num_of_cells'))."_".($cfg->param2('num_of_sectors')*$cfg->param2('num_of_cells')).".png";
			if (is_file($imgpath))	
			{
				echo "Bildvorlage gefunden, verwende diese: <img src=\"".$imgpath."\" /><br/><br/>";
			}
			
			echo "<input onclick=\"return confirm('Universum wirklich erstellen?')\" type=\"submit\" name=\"submit_create_universe2\" value=\"Ja, ein neues Universum erstellen\" >";
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
				Universe::create();
				echo "<br/><br/>
				<img src=\"../misc/map.image.php\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/><br/><br/>
				<input type=\"button\" value=\"Weiter\" onclick=\"document.location='?page=config&sub=uni'\" />";
			}
			else
			{
	
				// Check if universe exists
				$res = dbquery("SELECT COUNT(id) FROM cells;");
				$arr = mysql_fetch_row($res);
				if ($arr[0]==0)
				{
	        echo "<h2>Urknall</h2>";
	        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	        echo "Neues Universum erstellen<br/><br/>";
	        echo "<input type=\"submit\" name=\"submit_create_universe\" value=\"Start\" >";
	        echo "</form><br/>";
		  	}
				else
				{
          echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";

					$res = dbquery("SELECT COUNT(id) FROM planets WHERE planet_user_id>0;");
					$arr = mysql_fetch_row($res);					
					if ($arr[0]==0)
					{
						echo "<h2>Universum löschen</h2>";
	          echo "Es sind noch keine Planeten im Besitz von Spielern. Das Universum kann ohne Probleme gelöscht werden.<br/><br/>
	          <input type=\"submit\" name=\"submit_galaxy_reset\" value=\"Universum zurücksetzen\" ><br/>";
					}
	
	        // Reset
	        echo "<h2>Runde zur&uuml;cksetzen</h2>";
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