<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use PeriodicTaskRunner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CronjobController extends AbstractController
{
    private ConfigurationService $config;

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/admin/cronjob", name="admin.cronjob")
     */
    public function run(): Response
    {
        // Cron configuration
        $cronjob = null;
        $crontabUser = null;
        if (isUnixOS()) {
            $scriptname = dirname(__DIR__, 3) . "/bin/cronjob.php";
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
        $time = time();
        foreach (PeriodicTaskRunner::getScheduleFromConfig() as $tc) {
            $klass = $tc['name'];
            $reflect = new \ReflectionClass($klass);
            if ($reflect->implementsInterface(\IPeriodicTask::class)) {
                $elements = preg_split('/\s+/', $tc['schedule']);
                $taskConfig = [
                    'desc' => $klass::getDescription(),
                    'min' => $elements[0],
                    'hour' => $elements[1],
                    'dayofmonth' => $elements[2],
                    'month' => $elements[3],
                    'dayofweek' => $elements[4],
                    'current' => PeriodicTaskRunner::shouldRun($tc['schedule'], $time),
                ];
                $periodictasks[$tc['name']] = $taskConfig;
            }
        }

        // Handle result message
        $updateResults = null;
        if (isset($_SESSION['update_results'])) {
            $updateResults = BBCodeUtils::toHTML($_SESSION['update_results']);
            unset($_SESSION['update_results']);
        }

        return $this->render('admin/cronjob.html.twig', [
            'periodicTasks' => $periodictasks,
            'crontabCheck' => $crontabCheck,
            'crontabUser' => $crontabUser,
            'crontab' => $crontab ?? null,
            'cronjob' => $cronjob,
            'updateResults' => $updateResults,
        ]);
    }

    /**
     * @Route("/admin/cronjob/setup", name="admin.cronjob.setup")
     */
    public function setup(): RedirectResponse
    {
        // Enable cronjob
        if (isUnixOS()) {
            $scriptname = dirname(__DIR__, 3) . "/bin/cronjob.php";
            $cronjob = '* * * * * ' . $scriptname;

            // Get current crontab
            $crontab = [];
            exec("crontab -l", $crontab);
            if (!in_array($cronjob, $crontab, true)) {
                $out = shell_exec('(crontab -l 2>/dev/null; echo "' . $cronjob . '") | crontab -');
                if ((bool) $out) {
                    $this->addFlash('error', 'Cronjob konnte nicht aktiviert werden: ' . $out);
                }
            }
        }

        return $this->redirectToRoute('admin.cronjob');
    }

    /**
     * @Route("/admin/cronjob/enable", name="admin.cronjob.enable")
     */
    public function enable(): RedirectResponse
    {
        $this->config->set("update_enabled", 1);
        $this->addFlash('success', 'Tasks aktiviert!');

        return $this->redirectToRoute('admin.cronjob');
    }
}
