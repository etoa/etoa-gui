<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Form\Type\Admin\AllianceSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllianceController extends AbstractAdminController
{
    public function __construct(
        private AllianceRepository $allianceRepository,
        private AllianceService $allianceService
    ) {
    }

    #[Route('/admin/alliances/', name: 'admin.alliances')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function list(): Response
    {
        return $this->render('admin/alliance/list.html.twig', [
            'form' => $this->createForm(AllianceSearchType::class)->createView(),
            'total' => $this->allianceRepository->count(),
        ]);
    }

    #[Route('/admin/alliances/{id}/delete', name: 'admin.alliances.delete')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function delete(Request $request, int $id): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);
        if ($alliance === null) {
            $this->addFlash('error', 'Allianz nicht gefunden!');

            return $this->redirectToRoute('admin.alliances');
        }

        if ($request->isMethod('POST')) {
            if ($this->allianceService->delete($alliance)) {
                $this->addFlash('success', 'Die Allianz wurde gelöscht!');
            } else {
                $this->addFlash('error', 'Allianz konnte nicht gelöscht werden (ist sie in einem aktiven Krieg?)');
            }

            return $this->redirectToRoute('admin.alliances');
        }

        return $this->render('admin/alliance/delete.html.twig', [
            'alliance' => $alliance,
            'allianceUsers' => $this->allianceRepository->findUsers($alliance->id),
        ]);
    }
}
