<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\AccessLogRepository;
use EtoA\Support\StringUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ToolController extends AbstractAdminController
{
    public function __construct(
        private HttpClientInterface $client,
        private NetworkNameService $networkNameService,
        private AccessLogRepository $accessLogRepository,
        private ConfigurationService $config,
        private string $adminFileSharingDir
    ) {
    }

    #[Route("/admin/tools/", name: "admin.tools.index")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/tools/index.html.twig');
    }

    #[Route("/admin/tools/filesharing", name: "admin.tools.filesharing")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function filesharing(): Response
    {
        $files = [];
        $finder = (new Finder())->files()->in($this->adminFileSharingDir);
        foreach ($finder->getIterator() as $file) {
            $files[] = [
                'name' => $file->getBasename(),
                'link' => "file=" . base64_encode($file->getPathname()) . "&h=" . md5($file->getPathname()),
                'downloadLink' => createDownloadLink($file->getPathname()),
                'size' => StringUtils::formatBytes($file->getSize()),
                'time' => StringUtils::formatDate($file->getMTime()),
            ];
        }

        return $this->render('admin/tools/filesharing.html.twig', [
            'files' => $files,
        ]);
    }

    #[Route("/admin/tools/filesharing/upload", name: "admin.tools.filesharing.upload", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function fileUpload(Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('datei');

        try {
            $file->move($this->adminFileSharingDir, $file->getClientOriginalName());
            $this->addFlash('success', sprintf('Die Datei %s wurde heraufgeladen!', $file->getClientOriginalName()));
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Fehler beim Upload! ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin.tools.filesharing');
    }

    #[Route("/admin/tools/filesharing/rename", name: "admin.tools.filesharing.rename")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function fileRename(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            rename($this->adminFileSharingDir . "/" . $request->request->get('rename_old'), $this->adminFileSharingDir . "/" . $request->request->get('rename'));
            $this->addFlash('success', "Datei wurde umbenannt!");

            return $this->redirectToRoute('admin.tools.filesharing');
        }

        $f = base64_decode($request->query->get('file'), true);
        if (md5($f) !== $request->query->get('h')) {
            $this->addFlash('error', "Fehler im Dateinamen!");

            return $this->redirectToRoute('admin.tools.filesharing');
        }

        return $this->render('admin/tools/filesharing-rename.html.twig', [
            'name' => basename($f),
        ]);
    }

    #[Route("/admin/tools/filesharing/delete", name: "admin.tools.filesharing.delete")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function deleteFile(Request $request): Response
    {
        $f = base64_decode($request->query->get('file'), true);
        if (md5($f) === $request->query->get('h')) {
            @unlink($this->adminFileSharingDir . "/" . basename($f));
            $this->addFlash('success', "Datei wurde gelöscht!");
        } else {
            $this->addFlash('error', "Fehler im Dateinamen!");
        }

        return $this->redirectToRoute('admin.tools.filesharing');
    }

    #[Route("/admin/tools/accesslog/", name: "admin.tools.accesslog")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
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
            'accessLogEnabled' => $this->config->getBoolean('accesslog'),
        ]);
    }

    #[Route("/admin/tools/accesslog/toggle", name: "admin.tools.accesslog.toggle", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function toggleAccessLog(): Response
    {
        $this->config->set("accesslog", !$this->config->getBoolean('accesslog'));
        $this->addFlash('success', "Einstellungen gespeichert");

        return $this->redirectToRoute('admin.tools.accesslog');
    }

    #[Route("/admin/tools/accesslog/truncate", name: "admin.tools.accesslog.truncate", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function truncateAccessLog(): Response
    {
        $this->accessLogRepository->deleteAll();
        $this->addFlash('success', "Aufzeichnungen gelöscht");

        return $this->redirectToRoute('admin.tools.accesslog');
    }

    #[Route("/admin/tools/ip-resolver", name: "admin.tools.ipresolver")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
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
