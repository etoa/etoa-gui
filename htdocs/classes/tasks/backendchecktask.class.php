<?PHP
	/**
	* Check Backend
	*/
	class BackendCheckTask implements IPeriodicTask 
	{		
		function run()
		{
			$cfg = Config::getInstance();
		
			$backend = checkDaemonRunning($cfg->daemon_pidfile)>0 ? true : false;
			$change = $cfg->value("backend_status") != $backend;
			if ($change)
			{
				$status = $cfg->value("backend_status") == 0 ? 1 : 0;
				$cfg->set("backend_status", $status);

				$tm = new TextManager();
				$infoText = $tm->getText('backend_offline_message');
				$mailText = $cfg->value("backend_status") == 0 ? "Funktioniert wieder" : $infoText->content;
				$mail = new Mail("EtoA-Backend", $mailText);
				$sendTo = explode(";",$cfg->value("backend_offline_mail"));
				foreach ($sendTo as $sendMail)	{
					$mail->send($sendMail);
				}
			}
			return "Backend Check: ".($backend ? 'gestartet' : 'gestoppt')." (".($change ? 'geändert' : 'keine Änderung').")";
		}
		
		function getDescription() {
			return "Backend-Check";
		}
	}
?>