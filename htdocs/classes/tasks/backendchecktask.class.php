<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\Mail\MailSenderService;
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
    private MailSenderService $mailSenderService;

    function __construct(Container $app)
    {
        $this->textRepo = $app[TextRepository::class];
        $this->runtimeDataStore = $app[RuntimeDataStore::class];
        $this->config = $app[ConfigurationService::class];
        $this->mailSenderService = $app[MailSenderService::class];
    }

    function run()
    {
        $currentStatus = EventHandlerManager::checkDaemonRunning(getAbsPath($this->config->get('daemon_pidfile'))) > 0;
        $lastStatus = $this->runtimeDataStore->get('backend_status') == 1;
        $change = $currentStatus != $lastStatus;
        if ($change) {
            $infoText = $this->textRepo->find('backend_offline_message');
            $mailText = $currentStatus == 0 ? "Funktioniert wieder" : $infoText->content;
            $sendTo = explode(";", $this->config->get('backend_offline_mail'));
            $this->mailSenderService->send("EtoA-Backend", $mailText, $sendTo);
        }
        $this->runtimeDataStore->set('backend_status', (string) ($currentStatus ? 1 : 0));
        return "Backend Check: " . ($currentStatus ? 'gestartet' : 'gestoppt') . " (" . ($change ? 'geändert' : 'keine Änderung') . ")";
    }

    function getDescription()
    {
        return "Backend-Check";
    }
}
