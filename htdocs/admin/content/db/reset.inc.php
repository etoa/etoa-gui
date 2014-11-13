<?PHP
	$tpl->setView('db/reset');
	$tpl->assign('subtitle', 'Reset');

	$persistentTables = fetchJsonConfig("definition-tables.conf");

	$action = isset($_POST['action']) ? $_POST['action'] : null;
	if (isset($_POST['submit']))
	{
		// Create a list of all tables
		$res = dbquery("SHOW TABLES;");
		$tbls = array();
		while ($arr = mysql_fetch_row($res))
		{
			$tbls[] = $arr[0];
		}
	
		try
		{
			// Do the backup
			$dir = DBManager::getBackupDir();
			$gzip = Config::getInstance()->backup_use_gzip=="1";
		
			// Acquire mutex
			$mtx = new Mutex();
			$mtx->acquire();
		
			// Do the backup
			$log = DBManager::getInstance()->backupDB($dir, $gzip);
			
			// Release mutex
			$mtx->release();
	
			// Truncate tables
			if ($action == "truncate")
			{
				$mtx = new Mutex();
				$mtx->acquire();

				// Empty tables
				dbquery("SET FOREIGN_KEY_CHECKS=0;");
				$tc = 0;
				foreach ($tbls as $t)
				{
					if (!in_array($t, $persistentTables))
					{
						dbquery("TRUNCATE $t;");
						echo "Leere Tabelle <b>$t</b><br/>";
						$tc++;
					}
				}
				dbquery("SET FOREIGN_KEY_CHECKS=1;");

				// Restore default config
				$cr = $cfg->restoreDefaults();
				
				$mtx->release();
				
				$tpl->setView('db/reset_done');
				$tpl->assign("msg", "$tc Tabellen geleert, $cr Einstellungen auf Standard zurückgesetzt!");
			
			}
			// Drop tables
			else if ($action == "drop")
			{
				$schemaFile = RELATIVE_ROOT.'../db/schema-'.getSchemaVersion().'.sql';
				if (!is_file($schemaFile))
				{
					throw new Exception("Schema-Datei $schemaFile existiert nicht!");
				}
			
				$mtx = new Mutex();
				$mtx->acquire();
			
				// Empty tables
				dbquery("SET FOREIGN_KEY_CHECKS=0;");
				$tc = 0;
				foreach ($tbls as $t)
				{
					dbquery("TRUNCATE $t;");
					$tc++;
				}
				dbquery("SET FOREIGN_KEY_CHECKS=1;");
				
				DBManager::getInstance()->restoreDBFromFile($schemaFile);
				
				$mtx->release();

				$tpl->setView('db/reset_done');
				$tpl->assign("msg", "$tc Tabellen gelöscht, Datenbankschema $schemaFile neu initialisiert!");
				
			}
		}
		catch (Exception $e)
		{
			// Release mutex
			$mtx->release();
		
			// Write log
			Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Reset fehlgeschlagen[/b]\nFehler: ".$e->getMessage());
		
			// Show message
			$tpl->assign("errmsg", "Beim Ausf&uuml;hren des Resaet-Befehls trat ein Fehler auf: ".$e->getMessage());
		}			
	}
	
?>