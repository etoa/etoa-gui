<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Fleet\FleetRepository;
use EtoA\Form\Type\Admin\FleetSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FleetController extends AbstractAdminController
{
    public function __construct(
        private FleetRepository $fleetRepository
    ) {
    }

    #[Route('/admin/fleets/', name: 'admin.fleets')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function fleets(Request $request): Response
    {
        return $this->render('admin/fleets/search.html.twig', [
            'form' => $this->createForm(FleetSearchType::class, $request->query->all())->createView(),
            'total' => $this->fleetRepository->count(),
        ]);
    }
}
