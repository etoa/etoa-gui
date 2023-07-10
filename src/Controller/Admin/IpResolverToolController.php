<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\HostCache\NetworkNameService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IpResolverToolController extends AbstractAdminController
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly NetworkNameService  $networkNameService,
    )
    {
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
