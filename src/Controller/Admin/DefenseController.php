<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Defense\Defense;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseListItem;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Form\Type\Admin\AddDefenseListType;
use EtoA\Form\Type\Admin\DefenseSearchType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Ranking\RankingService;
use EtoA\Requirement\ObjectRequirement;
use EtoA\Requirement\RequirementsUpdater;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function DeepCopy\deep_copy;

class DefenseController extends AbstractAdminController
{
    public function __construct(
        private readonly RankingService               $rankingService,
        private readonly DefenseDataRepository        $defenseDataRepository,
        private readonly DefenseQueueRepository       $defenseQueueRepository,
        private readonly DefenseRepository            $defenseRepository,
        private readonly PlanetRepository             $planetRepository,
        private readonly DefenseRequirementRepository $defenseRequirementRepository,
    )
    {
    }

    #[Route("/admin/defense/search", name: "admin.defense.search")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        $addItem = DefenseListItem::empty();
        $addForm = $this->createForm(AddDefenseListType::class, $addItem);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $userId = $this->planetRepository->getPlanetUserId($addItem->entityId);
            $this->defenseRepository->addDefense($addItem->defenseId, $addItem->count, $userId, $addItem->entityId);

            $this->addFlash('success', sprintf('%s Verteidigungsanlagen hinzugefügt', StringUtils::formatNumber($addItem->count)));
        }

        return $this->render('admin/defense/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(DefenseSearchType::class, $request->query->all()),
            'total' => $this->defenseRepository->count(),
        ]);
    }

    #[Route("/admin/defense/queue", name: "admin.defense.queue")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function queue(Request $request): Response
    {
        return $this->render('admin/defense/queue.html.twig', [
            'form' => $this->createForm(DefenseSearchType::class, $request->query->all()),
            'total' => $this->defenseQueueRepository->count(),
        ]);
    }

    #[Route("/admin/defense/points", name: "admin.defense.points")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function points(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $num = $this->rankingService->calcDefensePoints();
            $this->addFlash('success', sprintf("Die Punkte von %s Verteidigungsanlagen wurden aktualisiert!", $num));
        }

        $defenses = $this->defenseDataRepository->getAllDefenses();
        usort($defenses, fn(Defense $a, Defense $b) => $b->points <=> $a->points);

        return $this->render('admin/defense/points.html.twig', [
            'defenses' => $defenses,
        ]);
    }

    #[Route('/admin/defense/requirements', name: 'admin.defense.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $collection = $this->defenseRequirementRepository->getAll();
        $defenses = $this->defenseDataRepository->getAllDefenses();
        $requirements = [];
        $names = [];
        foreach ($defenses as $defense) {
            $names[$defense->id] = $defense->name;
            $requirements[$defense->id] = $collection->getAll($defense->id);
        }

        $requirementsCopy = deep_copy($requirements);

        $form = $this->createForm(ObjectRequirementListType::class, $requirements, ['objectIds' => array_keys($defenses), 'objectNames' => $names]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ObjectRequirement[][] $updatedRequirements */
            $updatedRequirements = $form->getData();
            (new RequirementsUpdater($this->defenseRequirementRepository))->update($requirementsCopy, $updatedRequirements);

            $this->addFlash('success', 'Voraussetzungen aktualisiert');
        }

        return $this->render('admin/requirements/requirements.html.twig', [
            'objects' => $defenses,
            'form' => $form->createView(),
            'name' => 'Verteidigung',
        ]);
    }
}
