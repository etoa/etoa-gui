<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\AllianceImage;
use EtoA\Alliance\AllianceImageStorage;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AllianceMiscController extends AbstractAdminController
{
    public function __construct(
        private readonly AllianceService      $allianceService,
        private readonly AllianceRepository   $allianceRepository,
        private readonly AllianceImageStorage $allianceImageStorage,
    )
    {
    }

    #[Route('/admin/alliances/crap', name: 'admin.alliances.crap', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function crap(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if ($request->query->has('cleanupEmptyAlliances')) {
                $alliances = $this->allianceRepository->findAllWithoutUsers();
                $cnt = 0;
                if (count($alliances) > 0) {
                    foreach ($alliances as $alliance) {
                        if ($this->allianceRepository->countUsers((int)$alliance['alliance_id']) === 0) {
                            $alliance = $this->allianceRepository->findOneBy(['id'=>$alliance['alliance_id']]);
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
            'alliancesWithoutFounder' => $this->allianceRepository->findAllWithoutFounder(),
            'alliancesWithoutUsers' => $this->allianceRepository->findAllWithoutUsers(),
            'usersWithInvalidAlliances' => $this->allianceRepository->findAllSoloUsers(),
        ]);
    }

    #[Route('/admin/alliances/imagecheck', name: 'admin.alliances.imagecheck', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function imageCheck(Request $request): Response
    {
        if ($request->request->has('validate_submit')) {
            foreach ($request->request->all('validate') as $allianceId => $value) {
                $alliance = $this->allianceRepository->find((int) $allianceId);
                if ($alliance === null) {
                    continue;
                }

                if ($value == 0) {
                    $picture = $alliance->getImage();
                    if ($picture !== null && $picture !== '') {
                        $this->allianceImageStorage->delete($picture);
                        $this->allianceRepository->clearPicture($alliance);
                        $this->addFlash('success', 'Bild entfernt!');
                    }
                } else {
                    $this->allianceRepository->markPictureChecked($alliance);
                }
            }
        }

        $alliances = $this->allianceRepository->findAllWithPictures();
        $paths = [];
        foreach ($alliances as $alliance) {
            $paths[$alliance['id']] = $alliance['image'];
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
            $uncheckedImages[$alliance['image']] = $this->allianceImageStorage->exists($alliance['image']);
        }

        return $this->render('admin/alliance/imagecheck.html.twig', [
            'webroot' => AllianceImage::IMAGE_PATH,
            'alliancesWithUncheckedPictures' => $alliancesWithUncheckedPictures,
            'uncheckedImages' => $uncheckedImages,
            'orphaned' => $orphaned,
        ]);
    }
}
