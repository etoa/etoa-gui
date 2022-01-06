<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\PeriodicTask\EnvelopResultExtractor;
use EtoA\PeriodicTask\Task\MarketRateUpdateTask;
use EtoA\Support\RuntimeDataStore;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MarketController extends AbstractController
{
    public function __construct(
        private RuntimeDataStore $runtimeDataStore
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
}
