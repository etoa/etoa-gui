<?PHP
	$tpl->setView('db/migrations');
	$tpl->assign('subtitle', 'Schema-Migrationen');

	if (isset($_POST['migrate'])) {
		try 
		{
			$mtx = new Mutex();
			$mtx->acquire();

			// Migrate schema
			$cnt = DBManager::getInstance()->migrate();
			if ($cnt == 0) {
				$tpl->assign("msg", "Datenbankschema ist bereits aktuell!");
			} else {
				$tpl->assign("msg", "Datenbankschema wurde aktualisiert!");
			}
			
			$mtx->release();
		}
		catch (Exception $e)
		{
			// Release mutex
			$mtx->release();
		
			// Write log
			Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Migration fehlgeschlagen[/b]\nFehler: ".$e->getMessage());
		
			// Show message
			$tpl->assign("errmsg", "Beim Ausf&uuml;hren des Migration-Befehls trat ein Fehler auf: ".$e->getMessage());
		}
	}
	
	$data = DBManager::getInstance()->getArrayFromTable(DBManager::SCHEMA_MIGRATIONS_TABLE,["version", "date"],"version");
	$tpl->assign("data", $data);
	
	$pending = DBManager::getInstance()->getPendingMigrations();
	$tpl->assign("pending", $pending);

?>