<?PHP
	$tpl->setView('db_operation');

	$action = isset($_GET['action']) ? $_GET['action'] : null;
	
	// Datenbanktabellen optimieren
	if ($action == "optimize")
	{
		$tpl->assign('subtitle', 'Optimierungsbericht');
		$ores = DBManager::getInstance()->optimizeTables(true);
	}
	// Datenbanktabellen analysieren
	elseif ($action == "analyze")
	{
		$tpl->assign('subtitle', 'Analysebericht');
		$tpl->assign('msg', 'Tabellen deren Analysestatus bereits aktuell ist werden nicht angezeigt!');
		$ores = DBManager::getInstance()->analyzeTables(true);
	}
	// Datenbanktabellen prüfen
	elseif ($action == "check")
	{
		$tpl->assign('subtitle', 'Überprüfungsbericht');
		$tpl->assign('msg', 'Es werden nur Tabellen mit einem Status != OK angezeigt!');
		$ores = DBManager::getInstance()->checkTables(true);
	}
	// Datenbanktabellen reparieren
	elseif ($action == "repair")
	{
		$tpl->assign('subtitle', 'Reparaturbericht');	
		$ores = DBManager::getInstance()->repairTables(true);		
	}		
	
	// Fields
	$fields = array();
	while ($fo = mysql_fetch_field($ores))
	{
		$fields[] = $fo->name;
	}
	$tpl->assign('fields', $fields);

	// Records
	$rows = array();
	while ($arr = mysql_fetch_assoc($ores))
	{
		// When checking, filter all rows with OK status
		if ($action == "check" && isset($arr['Msg_text']) && $arr['Msg_text'] == "OK") {
			continue;
		}
		
		// Filter all rows which are already up do date
		if ($action == "analyze" && isset($arr['Msg_text']) && $arr['Msg_text'] == "Table is already up to date") {
			continue;
		}
	
		$rows[] = $arr;
	}
	$tpl->assign('rows', $rows);
?>