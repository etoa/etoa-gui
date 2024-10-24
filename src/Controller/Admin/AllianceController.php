<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceImageStorage;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Entity\AllianceBuildListItem;
use EtoA\Entity\AllianceTechnologyListItem;
use EtoA\Form\Type\Admin\AllianceBuildingAddType;
use EtoA\Form\Type\Admin\AllianceCreateType;
use EtoA\Form\Type\Admin\AllianceDepositSearchType;
use EtoA\Form\Type\Admin\AllianceEditType;
use EtoA\Form\Type\Admin\AllianceSearchType;
use EtoA\Form\Type\Admin\AllianceTechnologyAddType;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AllianceController extends AbstractAdminController
{
    public function __construct(
        private readonly AllianceRepository           $allianceRepository,
        private readonly AllianceService              $allianceService,
        private readonly AllianceHistoryRepository    $allianceHistoryRepository,
        private readonly AllianceTechnologyRepository $allianceTechnologyRepository,
        private readonly AllianceBuildingRepository   $allianceBuildingRepository,
        private readonly AllianceImageStorage         $allianceImageStorage,
        private readonly AllianceDiplomacyRepository  $allianceDiplomacyRepository,
        private readonly AllianceRankRepository       $allianceRankRepository,
        private readonly UserRepository               $userRepository,
    )
    {
    }

    #[Route('/admin/alliances/', name: 'admin.alliances')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function list(Request $request): Response
    {
        return $this->render('admin/alliance/list.html.twig', [
            'form' => $this->createForm(AllianceSearchType::class, $request->query->all()),
            'total' => $this->allianceRepository->count(),
        ]);
    }

    #[Route('/admin/alliances/create', name: 'admin.alliances.new')]
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

    #[Route('/admin/alliances/{id}', name: 'admin.alliances.view')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function view(int $id): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);
        if ($alliance === null) {
            $this->addFlash('error', 'Allianz nicht gefunden!');

            return $this->redirectToRoute('admin.alliances');
        }

        return $this->render('admin/alliance/view.html.twig', [
            'alliance' => $alliance,
            'founder' => $this->userRepository->getUser($alliance->founderId),
            'members' => $this->allianceRepository->findUsers($id),
            'ranks' => $this->allianceRankRepository->getRanks($id),
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
            if ($form->has('deleteImage') && (bool)$form->get('deleteImage')->getData()) {
                if ((bool)$alliance->image) {
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
            return $this->redirectToRoute('admin.alliances.view', ['id' => $id]);
        }

        return $this->render('admin/alliance/edit.html.twig', [
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
                    $this->allianceRepository->assignRankToUser((int)$rankId, (int)$userId);
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

        return $this->render('admin/alliance/members.html.twig', [
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

        $changesMade = false;
        if ($request->request->has('alliance_bnd_del') && count($request->request->all('alliance_bnd_del')) > 0) {
            foreach (array_keys($request->request->all('alliance_bnd_del')) as $diplomacyId) {
                $this->allianceDiplomacyRepository->deleteDiplomacy($diplomacyId);
            }
            $changesMade = true;
        }

        if (count($request->request->all('alliance_bnd_level')) > 0) {
            foreach (array_keys($request->request->all('alliance_bnd_level')) as $diplomacyId) {
                $this->allianceDiplomacyRepository->updateDiplomacy(
                    $diplomacyId,
                    (int)$request->request->all('alliance_bnd_level')[$diplomacyId],
                    $request->request->all('alliance_bnd_name')[$diplomacyId]
                );
            }
            $changesMade = true;
        }

        if ($changesMade) {
            $this->addFlash('success', 'Diplomatie aktualisiert!');
            return $this->redirectToRoute('admin.alliances.view', ['id' => $id]);
        }

        return $this->render('admin/alliance/diplomacy.html.twig', [
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

        return $this->render('admin/alliance/history.html.twig', [
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

        return $this->render('admin/alliance/resources.html.twig', [
            'alliance' => $alliance,
        ]);
    }

    #[Route('/admin/alliances/{id}/deposit', name: 'admin.alliances.deposit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function deposit(int $id, Request $request): Response
    {
        $alliance = $this->allianceRepository->getAlliance($id);

        return $this->render('admin/alliance/deposit.html.twig', [
            'alliance' => $alliance,
            'form' => $this->createForm(AllianceDepositSearchType::class, $request->query->all(), ['allianceId' => $alliance->id]),
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

        return $this->render('admin/alliance/buildings.html.twig', [
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

        return $this->render('admin/alliance/technologies.html.twig', [
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
