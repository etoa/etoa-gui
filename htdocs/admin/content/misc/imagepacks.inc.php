<?PHP
	echo "<h1>Bildpakete verwalten</h1>";

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
					"abuildings" => array("building", DBManager::getInstance()->getArrayFromTable("alliance_buildings","alliance_building_id")),
					"atechnologies" => array("technology", DBManager::getInstance()->getArrayFromTable("alliance_technologies","alliance_tech_id")),
					"buildings" => array("building", DBManager::getInstance()->getArrayFromTable("buildings","building_id")),
					"defense" => array("def", DBManager::getInstance()->getArrayFromTable("defense","def_id")),
					"missiles" => array("missile", DBManager::getInstance()->getArrayFromTable("missiles","missile_id")),
					"ships" => array("ship", DBManager::getInstance()->getArrayFromTable("ships","ship_id")),
					"stars" => array("star", DBManager::getInstance()->getArrayFromTable("sol_types","sol_type_id")),
					"technologies" => array("technology", DBManager::getInstance()->getArrayFromTable("technologies","tech_id")),
					"nebulas" => array("nebula",range(1,$cfg->value('num_nebula_images'))),
					"asteroids" => array("asteroids",range(1,$cfg->value('num_asteroid_images'))),
					"space" => array("space",range(1,$cfg->value('num_space_images'))),
					"wormholes" => array("wormhole",range(1,$cfg->value('num_wormhole_images'))),
					"races" => array("race", DBManager::getInstance()->getArrayFromTable("races","race_id")),
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
?>