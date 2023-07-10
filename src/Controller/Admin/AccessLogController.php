<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\AccessLogRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccessLogController extends AbstractAdminController
{
    public function __construct(
        private readonly AccessLogRepository  $accessLogRepository,
        private readonly ConfigurationService $config,
    )
    {
    }

    #[Route("/admin/tools/accesslog/", name: "admin.tools.accesslog")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function index(): Response
    {
        $logs = [];
        $domains = ['ingame', 'public', 'admin'];
        foreach ($domains as $domain) {
            $logs[$domain] = [];
            $counts = $this->accessLogRepository->getCountsForDomain($domain);
            foreach ($counts as $target => $targetCount) {
                $logs[$domain][$target]['count'] = $targetCount;
                $subCounts = $this->accessLogRepository->getCountsForTarget($domain, $target);
                foreach ($subCounts as $subLabel => $count) {
                    $logs[$domain][$target]['sub'][$subLabel] = $count;
                }
            }
        }

        return $this->render('admin/tools/accesslog.html.twig', [
            'logs' => $logs,
            'domains' => $domains,
            'accessLogEnabled' => $this->config->getBoolean('accesslog'),
        ]);
    }

    #[Route("/admin/tools/accesslog/toggle", name: "admin.tools.accesslog.toggle", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function toggle(): Response
    {
        $this->config->set("accesslog", !$this->config->getBoolean('accesslog'));
        $this->addFlash('success', "Einstellungen gespeichert");

        return $this->redirectToRoute('admin.tools.accesslog');
    }

    #[Route("/admin/tools/accesslog/truncate", name: "admin.tools.accesslog.truncate", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function truncate(): Response
    {
        $this->accessLogRepository->deleteAll();
        $this->addFlash('success', "Aufzeichnungen gelöscht");

        return $this->redirectToRoute('admin.tools.accesslog');
    }
}
