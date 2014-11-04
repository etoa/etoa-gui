<?PHP

	//
	// Start-Items
	//
	if ($sub=="defaultitems")
	{
		include("config/defaultitems.inc.php");
	}
  
	//
	// Tipps
	//
	elseif ($sub=="tipps")
	{
		advanced_form("tipps", $tpl);
	}

	//
	// Ticket-Cat
	//
	elseif ($sub=="ticketcat")
	{
		advanced_form("ticketcat", $tpl);
	}
  
	//
	// Designs
	//
	elseif ($sub=="designs")
	{
		$tpl->assign('title', 'Designs');
		
		$designs = get_designs();

		$customDesignDir = RELATIVE_ROOT.DESIGN_DIRECTORY.'/custom';
		
		// Design upload
		if (isset($_POST['submit']))
		{
			if (isset($_FILES["design"])) 
			{		
				// Check MIME type
				if ($_FILES["design"]['type'] == 'application/zip')
				{
					// Test if ZIP file can be read
					$zip = new ZipArchive();
					if ($zip->open($_FILES["design"]['tmp_name']) === TRUE)
					{
						// Iterate over files and detect design info file
						$uploadedDesignDir = null;
						for( $i = 0; $i < $zip->numFiles; $i++ ){ 
							$stat = $zip->statIndex($i); 
							if (basename($stat['name']) == DESIGN_CONFIG_FILE_NAME)
							{
								$uploadedDesignDir = dirname($stat['name']);
							}
						}
						$zip->close();
						
						// Check if design directory exits
						if ($uploadedDesignDir != null)
						{
							// Test naming pattern of design directory
							if (preg_match('/^[a-z0-9_-]+$/i', $uploadedDesignDir))
							{
								// Move uploaded file
								$target = $customDesignDir.'/'.$_FILES["design"]['name'];
								if (move_uploaded_file($_FILES["design"]['tmp_name'], $target))
								{
									$zip = new ZipArchive();
									if ($zip->open($target) === TRUE)
									{
										// Remove existing design, if it exists
										$existingDesign = $customDesignDir.'/'.$uploadedDesignDir;
										if (is_dir($existingDesign))
										{
											rrmdir($existingDesign);
										}
										
										// Extract design
										$zip->extractTo($customDesignDir);
										$zip->close();
										
										// Reload list of designs
										$designs = get_designs();
									}
									
									// Remove uploaded design archive
									unlink($target);
									$tpl->assign('msg', "Design hochgeladen");
								}
								else
								{
									$tpl->assign('errmsg', "Fehler beim Upload des Designs!");
								}
							}
							else
							{
								$tpl->assign('errmsg', "Ungültiges Design, Verzeichnis-Name enthält ungültige Zeichen (nur a-z, 0-9 sowie _ und - sind erlaubt)!");
							}
						}
						else
						{
							$tpl->assign('errmsg', "Ungültiges Design, Datei ".DESIGN_CONFIG_FILE_NAME." nicht vorhanden!");
						}
					}
					else
					{
						$tpl->assign('errmsg', "Kann ZIP-Datei nicht öffnen!");
					}
				}
				else
				{
					$tpl->assign('errmsg', "Keine ZIP-Datei!");
				}
			}
		}
		// Design download
		else if (!empty($_GET['download']))
		{
			$design = $_GET['download'];
			if (isset($designs[$design])) 
			{
				$zipFile = tempnam('sys_get_temp_dir', $design);
				$dir = $designs[$design]['dir'];
			
				try {
					createZipFromDirectory($dir, $zipFile);
					header('Content-Type: application/zip');
					header('Content-disposition: attachment; filename='.$design.'.zip');
					header('Content-Length: ' . filesize($zipFile));
					readfile($zipFile);
					unlink($zipFile);
					exit();
				} catch (Exception $e) {
					$tpl->assign('errmsg', $e->getMessage());
				}
			}
		}
		// Removal of custom design
		else if (!empty($_GET['remove']))
		{
			$design = $_GET['remove'];
			if (isset($designs[$design]) && $designs[$design]['custom']) 
			{
				$dir = $designs[$design]['dir'];
				rrmdir($dir);
				$tpl->assign('msg', 'Design gelöscht');
				$designs = get_designs();
			}
		}
		
		// Show all designs
		foreach ($designs as $k => $v) 
		{
			$res = dbQuerySave("
			SELECT 
				COUNT(id) as cnt 
			FROM 
				user_properties
			WHERE 
				css_style=?;", 
			array(
				$k
			));
			$arr = mysql_fetch_row($res);
			$designs[$k]['users'] = $arr[0];
		}
		
		$tpl->assign('designs', $designs);
		
		$tpl->setView('designs');
	}
	
	//
	// Bildpakete
	//
	elseif ($sub=="imagepacks")
	{
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
	}

	else
	{
		echo "<h1>Diverses</h1>";
		echo "Wähle eine Unterseite aus dem Menü!";

	}

?>