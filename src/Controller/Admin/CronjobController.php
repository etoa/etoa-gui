<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Cron\CronExpression;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\PeriodicTask\EnvelopResultExtractor;
use EtoA\PeriodicTask\PeriodicTaskCollection;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\PeriodicTaskInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CronjobController extends AbstractAdminController
{
    public function __construct(
        private readonly ConfigurationService   $config,
        private readonly PeriodicTaskCollection $taskCollection
    )
    {
    }

    #[Route("/admin/cronjob/", name: "admin.cronjob")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function view(): Response
    {
        // Cron configuration
        $cronjob = null;
        $crontabUser = null;
        if (isUnixOS()) {
            $scriptname = dirname(__DIR__, 3) . "/bin/console cron:run";
            $cronjob = '* * * * * ' . $scriptname;
            $crontabUser = trim(shell_exec('id'));

            // Get current crontab
            $crontab = [];
            exec("crontab -l", $crontab);

            $crontabCheck = in_array($cronjob, $crontab, true);
            $crontab = implode("\n", $crontab);
        } else {
            $crontabCheck = false;
        }

        $periodictasks = [];
        $time = new \DateTimeImmutable();
        foreach ($this->taskCollection->getAllTasks() as $task) {
            $reflection = new \ReflectionClass($task);
            $cron = new CronExpression($task->getSchedule());
            $elements = $cron->getParts();
            $taskConfig = [
                'desc' => $task->getDescription(),
                'min' => $elements[0],
                'hour' => $elements[1],
                'dayofmonth' => $elements[2],
                'month' => $elements[3],
                'dayofweek' => $elements[4],
                'current' => $cron->isDue($time),
                'nextrun' => $cron->getNextRunDate($time),
            ];
            $periodictasks[$reflection->getShortName()] = $taskConfig;
        }

        uasort($periodictasks, function (array $a, array $b): int {
            if ($a['current'] === $b['current']) {
                return $a['nextrun'] <=> $b['nextrun'];
            }

            return $b['current'] <=> $a['current'];
        });

        return $this->render('admin/cronjob.html.twig', [
            'periodicTasks' => $periodictasks,
            'crontabCheck' => $crontabCheck,
            'crontabUser' => $crontabUser,
            'crontab' => $crontab ?? null,
            'cronjob' => $cronjob,
        ]);
    }

    #[Route("/admin/cronjob/tasks/run", name: "admin.cron.tasks.run")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function runTasks(Request $request, MessageBusInterface $messageBus): RedirectResponse
    {
        $tasks = $request->query->has('all') ? $this->taskCollection->getAllTasks() : $this->taskCollection->getScheduledTasks(time());
        foreach ($tasks as $task) {
            $taskName = (new \ReflectionClass($task))->getShortName();

            $this->dispatchTask($messageBus, $task, $taskName);
        }

        return $this->redirectToRoute('admin.cronjob');
    }

    #[Route("/admin/cronjob/tasks/{taskName}/run", name: "admin.cron.task.run")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function runTask(string $taskName, MessageBusInterface $messageBus): RedirectResponse
    {
        $task = $this->taskCollection->getTask($taskName);
        if ($task === null) {
            $this->addFlash('error', $taskName . ' existiert nicht.');

            return $this->redirectToRoute('admin.cronjob');
        }

        $this->dispatchTask($messageBus, $task, $taskName);

        return $this->redirectToRoute('admin.cronjob');
    }

    private function dispatchTask(MessageBusInterface $messageBus, PeriodicTaskInterface $task, string $taskName): void
    {
        try {
            $result = EnvelopResultExtractor::extract($messageBus->dispatch($task));
            $takLog = $taskName . ': ' . $result->getMessage();
            $this->addFlash($result instanceof SuccessResult ? 'success' : 'info', $takLog);
        } catch (\Throwable $e) {
            $this->addFlash('error', $taskName . ": " . $e->getMessage());
        }
    }

    #[Route("/admin/cronjob/setup", name: "admin.cronjob.setup")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function setup(): RedirectResponse
    {
        // Enable cronjob
        if (isUnixOS()) {
            $scriptname = dirname(__DIR__, 3) . "/bin/console cron:run";
            $cronjob = '* * * * * ' . $scriptname;

            // Get current crontab
            $crontab = [];
            exec("crontab -l", $crontab);
            if (!in_array($cronjob, $crontab, true)) {
                $out = shell_exec('(crontab -l 2>/dev/null; echo "' . $cronjob . '") | crontab -');
                if ((bool)$out) {
                    $this->addFlash('error', 'Cronjob konnte nicht aktiviert werden: ' . $out);
                }
            }
        }

        return $this->redirectToRoute('admin.cronjob');
    }

    #[Route("/admin/cronjob/enable", name: "admin.cronjob.enable")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function enable(): RedirectResponse
    {
        $this->config->set("update_enabled", 1);
        $this->addFlash('success', 'Tasks aktiviert!');

        return $this->redirectToRoute('admin.cronjob');
    }
}
