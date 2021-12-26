<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\Alliance;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceImageStorage;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Form\Type\Admin\AllianceCreateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllianceMiscController extends AbstractAdminController
{
    public function __construct(
        private AllianceService $allianceService,
        private AllianceRankRepository $allianceRankRepository,
        private AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private AllianceRepository $allianceRepository,
        private AllianceImageStorage $allianceImageStorage,
    ) {
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

    #[Route('/admin/alliances/imagecheck', name: 'admin.alliances.imagecheck')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function imageCheck(Request $request): Response
    {
        if ($request->request->has('validate_submit')) {
            foreach ($request->request->all('validate') as $allianceId => $value) {
                if ($value == 0) {
                    $picture = $this->allianceRepository->getPicture($allianceId);
                    if ($picture != '') {
                        $this->allianceImageStorage->delete($picture);
                        if ($this->allianceRepository->clearPicture($allianceId)) {
                            $this->addFlash('success', 'Bild entfernt!');
                        }
                    }
                } else {
                    $this->allianceRepository->markPictureChecked($allianceId);
                }
            }
        }

        $alliances = $this->allianceRepository->findAllWithPictures();
        $paths = [];
        foreach ($alliances as $alliance) {
            $paths[$alliance['alliance_id']] = $alliance['alliance_img'];
        }

        $files = $this->allianceImageStorage->getAllImages();
        $orphaned = [];
        foreach ($files as $file) {
            if (!in_array($file, $paths, true)) {
                $orphaned[] = $file;
            }
        }

        if ($request->request->has('deleteOrphaned')) {
            foreach ($orphaned as $image) {
                $this->allianceImageStorage->delete($image);
            }

            $this->addFlash('success', 'Verwaiste Bilder gelöscht!');
            $orphaned = [];
        }

        $uncheckedImages = [];
        $alliancesWithUncheckedPictures = $this->allianceRepository->findAllWithUncheckedPictures();
        foreach ($alliancesWithUncheckedPictures as $alliance) {
            $uncheckedImages[$alliance['alliance_img']] = $this->allianceImageStorage->exists($alliance['alliance_img']);
        }

        return $this->render('admin/alliance/imagecheck.html.twig', [
            'webroot' => Alliance::PROFILE_PICTURE_PATH,
            'alliancesWithUncheckedPictures' => $alliancesWithUncheckedPictures,
            'uncheckedImages' => $uncheckedImages,
            'orphaned' => $orphaned,
        ]);
    }
}
