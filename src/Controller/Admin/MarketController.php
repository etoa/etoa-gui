<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Market\MarketResourceRepository;
use EtoA\PeriodicTask\EnvelopResultExtractor;
use EtoA\PeriodicTask\Task\MarketRateUpdateTask;
use EtoA\Support\RuntimeDataStore;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MarketController extends AbstractController
{
    public function __construct(
        private RuntimeDataStore $runtimeDataStore,
        private UserRepository $userRepository,
        private MarketResourceRepository $marketResourceRepository,
    ) {
    }

    #[Route('/admin/market/', name: 'admin.market')]
    public function index(Request $request, MessageBusInterface $messageBus): Response
    {
        if ($request->query->has('updaterates')) {
            $result = EnvelopResultExtractor::extract($messageBus->dispatch(new MarketRateUpdateTask()));
            $this->addFlash('success', $result->getMessage());
        }

        $marketRates = [];
        foreach (ResourceNames::NAMES as $index => $resourceName) {
            $marketRates[$resourceName] = $this->runtimeDataStore->get('market_rate_' . $index, '1');
        }

        return $this->render('admin/market/index.html.twig', [
            'marketRates' => $marketRates,
        ]);
    }

    #[Route('/admin/market/resources', name: 'admin.market.resources')]
    public function resources(): Response
    {
        return $this->render('admin/market/resources.html.twig', [
            'offers' => $this->marketResourceRepository->getAll(),
            'userNicknames' => $this->userRepository->searchUserNicknames(),
            'resourceNames' => ResourceNames::NAMES,
        ]);
    }

    #[Route('/admin/market/resources/{id}', name: 'admin.market.resources.delete', methods: ['POST'])]
    public function deleteResources(int $id): RedirectResponse
    {
        $this->marketResourceRepository->delete($id);
        $this->addFlash('success', "Angebot gelöscht!");

        return $this->redirectToRoute('admin.market.resources');
    }
}
