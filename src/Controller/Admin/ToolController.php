<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\AccessLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ToolController extends AbstractController
{
    private HttpClientInterface $client;
    private NetworkNameService$networkNameService;
    private AccessLogRepository$accessLogRepository;
    private ConfigurationService $config;

    public function __construct(HttpClientInterface $client, NetworkNameService $networkNameService, AccessLogRepository $accessLogRepository, ConfigurationService $config)
    {
        $this->client = $client;
        $this->networkNameService = $networkNameService;
        $this->accessLogRepository = $accessLogRepository;
        $this->config = $config;
    }

    /**
     * @Route("/admin/tools/", name="admin.tools.index")
     */
    public function index(): Response
    {
        return $this->render('admin/tools/index.html.twig');
    }

    /**
     * @Route("/admin/tools/accesslog/", name="admin.tools.accesslog")
     */
    public function accessLog(): Response
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
            'accessLogEnabled' => $this->config->getBoolean('accesslog')
        ]);
    }

    /**
     * @Route("/admin/tools/accesslog/toggle", methods={"POST"}, name="admin.tools.accesslog.toggle")
     */
    public function toggleAccessLog(): Response
    {
        $this->config->set("accesslog", !$this->config->getBoolean('accesslog'));
        $this->addFlash('success', "Einstellungen gespeichert");

        return $this->redirectToRoute('admin.tools.accesslog');
    }

    /**
     * @Route("/admin/tools/accesslog/truncate", methods={"POST"}, name="admin.tools.accesslog.truncate")
     */
    public function truncateAccessLog(): Response
    {
        $this->accessLogRepository->deleteAll();
        $this->addFlash('success', "Aufzeichnungen gelöscht");

        return $this->redirectToRoute('admin.tools.accesslog');
    }

    /**
     * @Route("/admin/tools/ip-resolver", name="admin.tools.ipresolver")
     */
    public function ipResolver(Request $request): Response
    {
        $ip = null;
        $host = null;
        $ipInfoResult = null;
        if ($request->isMethod('POST')) {
            if ($request->request->get('ip')) {
                $ip = $request->request->get('ip');
                $host = $this->networkNameService->getHost($ip);
            } elseif ($request->request->get('hostname')) {
                $host = $request->request->get('hostname');
                $ip = gethostbyname($host);
            } else {
                return $this->render('admin/tools/ipresolver.html.twig', [
                    'ip' => $ip,
                    'host' => $host,
                    'ipInfoResult' => $ipInfoResult,
                ]);
            }

            try {
                $ipInfoResult = $this->client->request('GET', 'https://ipinfo.io/' . $ip, ['headers' => ['Accept: application/json']])->getContent();
            } catch (\Throwable $e) {
                $this->addFlash('error', $e->getMessage());
                $ipInfoResult = "Die IP <b>" . $ip . "</b> hat den Hostnamen <b>" . $host . "</b>";
            }
        }

        return $this->render('admin/tools/ipresolver.html.twig', [
            'ip' => $ip,
            'host' => $request->request->get('host'),
            'ipInfoResult' => $ipInfoResult,
        ]);
    }
}
