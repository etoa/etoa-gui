<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Backend\EventHandlerManager;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\BackendCheckTask;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Support\RuntimeDataStore;
use EtoA\Text\TextRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BackendCheckHandler implements MessageHandlerInterface
{
    private EventHandlerManager $eventHandlerManager;
    private RuntimeDataStore $runtimeDataStore;
    private TextRepository $textRepo;
    private ConfigurationService $config;
    private MailSenderService $mailSenderService;

    public function __construct(EventHandlerManager $eventHandlerManager, RuntimeDataStore $runtimeDataStore, TextRepository $textRepo, ConfigurationService $config, MailSenderService $mailSenderService)
    {
        $this->eventHandlerManager = $eventHandlerManager;
        $this->runtimeDataStore = $runtimeDataStore;
        $this->textRepo = $textRepo;
        $this->config = $config;
        $this->mailSenderService = $mailSenderService;
    }

    public function __invoke(BackendCheckTask $task): SuccessResult
    {
        $currentStatus = $this->eventHandlerManager->checkDaemonRunning() > 0;
        $lastStatus = $this->runtimeDataStore->get('backend_status') == 1;
        $change = $currentStatus != $lastStatus;
        if ($change) {
            $infoText = $this->textRepo->find('backend_offline_message');
            $mailText = $currentStatus ? "Funktioniert wieder" : $infoText->content;
            $sendTo = explode(";", $this->config->get('backend_offline_mail'));
            $this->mailSenderService->send("EtoA-Backend", $mailText, $sendTo);
        }
        $this->runtimeDataStore->set('backend_status', (string) ($currentStatus ? 1 : 0));

        return SuccessResult::create("Backend Check: " . ($currentStatus ? 'gestartet' : 'gestoppt') . " (" . ($change ? 'geändert' : 'keine Änderung') . ")");
    }
}
