<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Backend\BackendMessageRepository;
use EtoA\Backend\EventHandlerManager;
use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventHandlerController extends AbstractAdminController
{
    public function __construct(
        private EventHandlerManager $eventHandlerManager,
        private BackendMessageRepository $backendMessageRepository,
        private ConfigurationService $config
    ) {
    }

    /**
     * @Route("/admin/eventhandler/start", name="admin.eventhandler.start")
     */
    public function start(): Response
    {
        try {
            $this->eventHandlerManager->start();
            $this->addFlash('success', 'Dienst gestartet!');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }


        return $this->redirectToRoute('admin.eventhandler');
    }

    /**
     * @Route("/admin/eventhandler/stop", name="admin.eventhandler.stop")
     */
    public function stop(): Response
    {
        try {
            $this->eventHandlerManager->stop();
            $this->addFlash('success', 'Dienst gestoppt!');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin.eventhandler');
    }

    /**
     * @Route("/admin/eventhandler/", name="admin.eventhandler")
     */
    public function view(): Response
    {
        $eventHandlerPid = null;
        $messageQueueSize = null;
        $sysId = null;
        $log = null;
        if (isUnixOS()) {
            $messageQueueSize = $this->backendMessageRepository->getMessageQueueSize();
            $eventHandlerPid = $this->eventHandlerManager->checkDaemonRunning();

            if (function_exists('posix_uname')) {
                $un = posix_uname();
                $sysId = $un['sysname'] . " " . $un['release'] . " " . $un['version'];
            }

            // Warning: Open-Basedir restrictions may appply
            $logfile = $this->config->get('daemon_logfile');
            if (!preg_match('#^/#', $logfile)) {
                $logfile = '../' . $logfile;
            }
            if (is_file($logfile)) {
                $lf = fopen($logfile, "r");
                $log = [];
                while ($l = fgets($lf)) {
                    $log[] = $l;
                }
                fclose($lf);
                $log = array_reverse($log);
            } else {
                $log = "<em>Die Logdatei " . $this->config->get('daemon_logfile') . " kann nicht geöffnet werden!</em>";
            }
        }

        return $this->render('admin/eventhandler.html.twig', [
            'log' => $log,
            'eventHandlerPid' => $eventHandlerPid,
            'sysId' => $sysId,
            'messageQueueSize' => $messageQueueSize,
        ]);
    }
}
