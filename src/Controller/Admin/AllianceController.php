<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Form\Type\Admin\AllianceCreateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllianceController extends AbstractAdminController
{
    public function __construct(
        private AllianceService $allianceService,
        private AllianceRankRepository $allianceRankRepository,
        private AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private AllianceRepository $allianceRepository
    ) {
    }

    #[Route('/admin/alliances/', name: 'admin.alliances')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function list(): Response
    {
        return $this->render('admin/alliances/list.html.twig');
    }

    #[Route('/admin/alliances/new', name: 'admin.alliances.new')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(AllianceCreateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $alliance = $this->allianceService->create(
                    $data['tag'],
                    $data['name'],
                    (int)$data['founder'],
                );

                $this->addFlash('success', sprintf('Alliance %s erstellt', $alliance->nameWithTag));

                return $this->redirectToRoute('admin.alliances');
            } catch (InvalidAllianceParametersException $ex) {
                $this->addFlash('error', "Allianz konnte nicht erstellt werden!\n\n" . $ex->getMessage() . "");
            }
        }

        return $this->render('admin/alliance/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/alliances/crap', name: 'admin.alliances.crap')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function crap(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if ($request->request->has('cleanupRanks')) {
                if ($this->allianceRankRepository->deleteOrphanedRanks() > 0) {
                    $this->addFlash('success', "Fehlerhafte Daten gelöscht.");
                }
            } elseif ($request->request->has('cleanupDiplomacy')) {
                if ($this->allianceDiplomacyRepository->deleteOrphanedDiplomacies() > 0) {
                    $this->addFlash('success', "Fehlerhafte Daten gelöscht.");
                }
            } elseif ($request->query->has('cleanupEmptyAlliances')) {
                $alliances = $this->allianceRepository->findAllWithoutUsers();
                $cnt = 0;
                if (count($alliances) > 0) {
                    foreach ($alliances as $alliance) {
                        if ($this->allianceRepository->countUsers((int) $alliance['alliance_id']) === 0) {
                            $alliance = $this->allianceRepository->getAlliance((int) $alliance['alliance_id']);
                            if ($this->allianceService->delete($alliance)) {
                                $cnt++;
                            }
                        }
                    }
                }

                $this->addFlash('success', "$cnt leere Allianzen wurden gelöscht.");
            }
        }

        return $this->render('admin/alliance/crap.html.twig', [
            'ranksWithoutAlliance' => $this->allianceRankRepository->countOrphanedRanks(),
            'bndWithoutAlliance' => $this->allianceDiplomacyRepository->countOrphanedDiplomacies(),
            'alliancesWithoutFounder' => $this->allianceRepository->findAllWithoutFounder(),
            'alliancesWithoutUsers' => $this->allianceRepository->findAllWithoutUsers(),
            'usersWithInvalidAlliances' => $this->allianceRepository->findAllSoloUsers(),
        ]);
    }
}
