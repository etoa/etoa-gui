<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\HostCache\NetworkNameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ToolController extends AbstractController
{
    private HttpClientInterface $client;
    private NetworkNameService$networkNameService;

    public function __construct(HttpClientInterface $client, NetworkNameService $networkNameService)
    {
        $this->client = $client;
        $this->networkNameService = $networkNameService;
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
