<?PHP

use EtoA\Text\TextRepository;
use Pimple\Container;

/**
	* Check Backend
	*/
	class BackendCheckTask implements IPeriodicTask
	{
		private TextRepository $textRepo;

		function __construct(Container $app)
		{
			$this->textRepo = $app['etoa.text.repository'];
		}

		function run()
		{
			$cfg = Config::getInstance();

			$currentStatus = EventHandlerManager::checkDaemonRunning(getAbsPath($cfg->daemon_pidfile))>0 ? true : false;
			$lastStatus = RuntimeDataStore::get('backend_status') == 1;
			$change = $currentStatus != $lastStatus;
			if ($change)
			{
				$infoText = $this->textRepo->find('backend_offline_message');
				$mailText = $currentStatus == 0 ? "Funktioniert wieder" : $infoText->content;
				$mail = new Mail("EtoA-Backend", $mailText);
				$sendTo = explode(";",$cfg->value("backend_offline_mail"));
				foreach ($sendTo as $sendMail)	{
					$mail->send($sendMail);
				}
			}
			RuntimeDataStore::set('backend_status', $currentStatus ? 1 : 0);
			return "Backend Check: ".($currentStatus ? 'gestartet' : 'gestoppt')." (".($change ? 'geändert' : 'keine Änderung').")";
		}

		function getDescription() {
			return "Backend-Check";
		}
	}
?>
