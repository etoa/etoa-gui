<?PHP
	$tpl->setView('config/restoredefaults');
	$tpl->assign('subtitle', 'Konfiguration auf Standardwerte zurücksetzen');

	if (isset($_POST['restoresubmit']))
	{
		if ($cnt = $cfg->restoreDefaults())
		{
			ok_msg("$cnt Einstellungen wurden wiederhergestellt!");
			BackendMessage::reloadConfig();
		}
	}

	echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	echo "<p>Soll die Konfigurationstabelle wirklich auf ihre Standardwerte zurückgesetzt werden?</p>";
	echo "<p><input type=\"submit\" name=\"restoresubmit\" value=\"Ja, Einstellungen zurücksetzen\" /></p>";
	echo "</form>";
?>