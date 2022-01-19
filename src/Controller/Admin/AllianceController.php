<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceBuildListItem;
use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceImageStorage;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\AllianceTechnologyListItem;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Form\Type\Admin\AllianceBuildingAddType;
use EtoA\Form\Type\Admin\AllianceDepositSearchType;
use EtoA\Form\Type\Admin\AllianceSearchType;
use EtoA\Form\Type\Admin\AllianceTechnologyAddType;
use EtoA\Form\Type\Admin\AllianceEditType;
use EtoA\Support\StringUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllianceController extends AbstractAdminController
{
    public function __construct(
        private AllianceRepository $allianceRepository,
        private AllianceService $allianceService,
        private AllianceHistoryRepository $allianceHistoryRepository,
        private AllianceTechnologyRepository $allianceTechnologyRepository,
        private AllianceBuildingRepository $allianceBuildingRepository,
        private AllianceImageStorage $allianceImageStorage,
        private AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private AllianceRankRepository $allianceRankRepository,
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

    #[Route('/admin/alliances/{id}/edit', name: 'admin.alliances.edit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function edit(Request $request, int $id): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);
        if ($alliance === null) {
            $this->addFlash('error', 'Allianz nicht gefunden!');

            return $this->redirectToRoute('admin.alliances');
        }

        $form = $this->createForm(AllianceEditType::class, $alliance);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('deleteImage') && (bool) $form->get('deleteImage')->getData()) {
                if ((bool) $alliance->image) {
                    $this->allianceImageStorage->delete($alliance->image);
                    $this->allianceRepository->clearPicture($alliance->id);

                    $this->addFlash('success', 'Bild entfernt!');
                }
            }

            $this->allianceRepository->update(
                $alliance->id,
                $alliance->tag,
                $alliance->name,
                $alliance->text,
                $alliance->applicationTemplate,
                $alliance->url,
                $alliance->founderId
            );

            $this->addFlash('success', 'Allianzdaten aktualisiert!');
        }

        return $this->render('admin/alliance/edit/edit.html.twig', [
            'alliance' => $alliance,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/alliances/{id}/members', name: 'admin.alliances.members')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function members(int $id, Request $request): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);

        if ($request->isMethod('POST')) {
            // Change alliance memberships
            if ($request->request->has('member_kick') && count($request->request->all('member_kick')) > 0) {
                foreach (array_keys($request->request->all('member_kick')) as $userId) {
                    $this->allianceRepository->removeUser($userId);
                }
            }

            if (count($request->request->all('member_rank')) > 0) {
                foreach ($request->request->all('member_rank') as $userId => $rankId) {
                    $this->allianceRepository->assignRankToUser((int) $rankId, (int) $userId);
                }
            }

            // Update rank changes
            if ($request->request->has('rank_del') && count($request->request->all('rank_del')) > 0) {
                foreach (array_keys($request->request->all('rank_del')) as $rankId) {
                    $this->allianceRankRepository->removeRank($rankId);
                }
            }

            if ($request->request->has('rank_name') && count($request->request->all('rank_name')) > 0) {
                foreach ($request->request->all('rank_name') as $rankId => $name) {
                    $this->allianceRankRepository->updateRank($rankId, $name, $request->request->all('rank_level')[$rankId]);
                }
            }

            $this->addFlash('success', 'Mitglieder aktualisiert!');
        }

        return $this->render('admin/alliance/edit/members.html.twig', [
            'alliance' => $alliance,
            'members' => $this->allianceRepository->findUsers($id),
            'ranks' => $this->allianceRankRepository->getRanks($id),
        ]);
    }

    #[Route('/admin/alliances/{id}/diplomacy', name: 'admin.alliances.diplomacy')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function diplomacy(int $id, Request $request): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);

        if ($request->request->has('alliance_bnd_del') && count($request->request->all('alliance_bnd_del')) > 0) {
            foreach (array_keys($request->request->all('alliance_bnd_del')) as $diplomacyId) {
                $this->allianceDiplomacyRepository->deleteDiplomacy($diplomacyId);
            }
        }

        if (count($request->request->all('alliance_bnd_level')) > 0) {
            foreach (array_keys($request->request->all('alliance_bnd_level')) as $diplomacyId) {
                $this->allianceDiplomacyRepository->updateDiplomacy(
                    $diplomacyId,
                    (int) $request->request->all('alliance_bnd_level')[$diplomacyId],
                    $request->request->all('alliance_bnd_name')[$diplomacyId]
                );
            }
        }

        $this->addFlash('success', 'Diplomatie aktualisiert!');

        return $this->render('admin/alliance/edit/diplomacy.html.twig', [
            'alliance' => $alliance,
            'diplomacies' => $this->allianceDiplomacyRepository->getDiplomacies($alliance->id),
            'levels' => AllianceDiplomacyLevel::all(),
        ]);
    }

    #[Route('/admin/alliances/{id}/history', name: 'admin.alliances.history')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function history(int $id): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);

        return $this->render('admin/alliance/edit/history.html.twig', [
            'alliance' => $alliance,
            'history' => $this->allianceHistoryRepository->findForAlliance($alliance->id),
        ]);
    }

    #[Route('/admin/alliances/{id}/resources', name: 'admin.alliances.resources')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function resources(int $id, Request $request): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);

        if ($request->isMethod('POST')) {
            $this->allianceRepository->updateResources(
                $id,
                StringUtils::parseFormattedNumber($request->request->get('res_metal')),
                StringUtils::parseFormattedNumber($request->request->get('res_crystal')),
                StringUtils::parseFormattedNumber($request->request->get('res_plastic')),
                StringUtils::parseFormattedNumber($request->request->get('res_fuel')),
                StringUtils::parseFormattedNumber($request->request->get('res_food')),
            );

            $this->allianceRepository->addResources(
                $id,
                StringUtils::parseFormattedNumber($request->request->get('res_metal_add')),
                StringUtils::parseFormattedNumber($request->request->get('res_crystal_add')),
                StringUtils::parseFormattedNumber($request->request->get('res_plastic_add')),
                StringUtils::parseFormattedNumber($request->request->get('res_fuel_add')),
                StringUtils::parseFormattedNumber($request->request->get('res_food_add')),
            );

            $this->addFlash('success', 'Ressourcen aktualisiert!');
            $alliance = $this->allianceRepository->getAlliance($id);
        }

        return $this->render('admin/alliance/edit/resources.html.twig', [
            'alliance' => $alliance,
        ]);
    }

    #[Route('/admin/alliances/{id}/deposit', name: 'admin.alliances.deposit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function deposit(int $id, Request $request): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);

        return $this->render('admin/alliance/edit/deposit.html.twig', [
            'alliance' => $alliance,
            'form' => $this->createForm(AllianceDepositSearchType::class, $request->query->all(), ['allianceId' => $alliance->id])->createView(),
        ]);
    }

    #[Route('/admin/alliances/{id}/buildings', name: 'admin.alliances.buildings')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function buildings(Request $request, int $id): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);

        $form = $this->createForm(AllianceBuildingAddType::class, AllianceBuildListItem::createFromAlliance($alliance));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AllianceBuildListItem $data */
            $data = $form->getData();
            if ($this->allianceBuildingRepository->existsInAlliance($data->allianceId, $data->buildingId)) {
                $this->allianceBuildingRepository->updateForAlliance(
                    $data->allianceId,
                    $data->buildingId,
                    $data->level,
                    $data->memberFor
                );

                $this->addFlash('success', 'Gebäudedatensatz erfolgreich bearbeitet!');
            } else {
                $this->allianceBuildingRepository->addToAlliance(
                    $data->allianceId,
                    $data->buildingId,
                    $data->level,
                    $data->memberFor
                );

                $this->addFlash('success', 'Gebäudedatensatz erfolgreich eingefügt!');
            }
        }

        return $this->render('admin/alliance/edit/buildings.html.twig', [
            'alliance' => $alliance,
            'buildings' => $this->allianceBuildingRepository->getNames(),
            'buildlist' => $this->allianceBuildingRepository->getBuildList($alliance->id),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/alliances/{id}/technologies', name: 'admin.alliances.technologies')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function technologies(Request $request, int $id): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);
        $form = $this->createForm(AllianceTechnologyAddType::class, AllianceTechnologyListItem::createFromAlliance($alliance));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AllianceTechnologyListItem $data */
            $data = $form->getData();
            if ($this->allianceTechnologyRepository->existsInAlliance($data->allianceId, $data->technologyId)) {
                $this->allianceTechnologyRepository->updateForAlliance(
                    $data->allianceId,
                    $data->technologyId,
                    $data->level,
                    $data->memberFor
                );

                $this->addFlash('success', 'Technologiedatensatz erfolgreich bearbeitet!');
            } else {
                $this->allianceTechnologyRepository->addToAlliance(
                    $data->allianceId,
                    $data->technologyId,
                    $data->level,
                    $data->memberFor
                );

                $this->addFlash('success', 'Technologiedatensatz erfolgreich eingefügt!');
            }
        }

        return $this->render('admin/alliance/edit/technologies.html.twig', [
            'alliance' => $alliance,
            'technologies' => $this->allianceTechnologyRepository->getNames(),
            'techlist' => $this->allianceTechnologyRepository->getTechnologyList($alliance->id),
            'form' => $form->createView(),
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
