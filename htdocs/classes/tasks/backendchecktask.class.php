<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\RuntimeDataStore;
use EtoA\Text\TextRepository;
use Pimple\Container;

/**
 * Check Backend
 */
class BackendCheckTask implements IPeriodicTask
{
    private TextRepository $textRepo;
    private RuntimeDataStore $runtimeDataStore;
    private ConfigurationService $config;

    function __construct(Container $app)
    {
        $this->textRepo = $app[TextRepository::class];
        $this->runtimeDataStore = $app[RuntimeDataStore::class];
        $this->config = $app[ConfigurationService::class];
    }

    function run()
    {
        $currentStatus = EventHandlerManager::checkDaemonRunning(getAbsPath($this->config->get('daemon_pidfile'))) > 0;
        $lastStatus = $this->runtimeDataStore->get('backend_status') == 1;
        $change = $currentStatus != $lastStatus;
        if ($change) {
            $infoText = $this->textRepo->find('backend_offline_message');
            $mailText = $currentStatus == 0 ? "Funktioniert wieder" : $infoText->content;
            $mail = new Mail("EtoA-Backend", $mailText);
            $sendTo = explode(";", $this->config->get('backend_offline_mail'));
            foreach ($sendTo as $sendMail) {
                $mail->send($sendMail);
            }
        }
        $this->runtimeDataStore->set('backend_status', (string) ($currentStatus ? 1 : 0));
        return "Backend Check: " . ($currentStatus ? 'gestartet' : 'gestoppt') . " (" . ($change ? 'geändert' : 'keine Änderung') . ")";
    }

    function getDescription()
    {
        return "Backend-Check";
    }
}
