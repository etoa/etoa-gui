<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Entity\DefenseListItem;
use EtoA\Entity\DefenseRequirements;
use EtoA\Form\Type\Admin\AddDefenseListType;
use EtoA\Form\Type\Admin\DefenseSearchType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Ranking\RankingService;
use EtoA\Support\StringUtils;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefenseController extends AbstractAdminController
{
    public function __construct(
        private readonly RankingService               $rankingService,
        private readonly DefenseDataRepository        $defenseDataRepository,
        private readonly DefenseQueueRepository       $defenseQueueRepository,
        private readonly DefenseRepository            $defenseRepository,
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
            $this->defenseRepository->addDefense($addItem->getDefense(), $addItem->getCount(), $addForm->get('entity')->getData()->getUser(), $addItem->getEntity());

            $this->addFlash('success', sprintf('%s Verteidigungsanlagen hinzugefügt', StringUtils::formatNumber($addItem->getCount())));
        }

        return $this->render('admin/defense/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(DefenseSearchType::class, $request->query->all()),
            'total' => $this->defenseRepository->count([]),
        ]);
    }

    #[Route("/admin/defense/queue", name: "admin.defense.queue")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function queue(Request $request): Response
    {
        return $this->render('admin/defense/queue.html.twig', [
            'form' => $this->createForm(DefenseSearchType::class, $request->query->all()),
            'total' => $this->defenseQueueRepository->count([]),
        ]);
    }

    #[Route("/admin/defense/points", name: "admin.defense.points")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function points(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('calc', SubmitType::class, ['label' => 'Neu berechnen'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $num = $this->rankingService->calcDefensePoints();
            $this->addFlash('success', sprintf("Die Punkte von %s Verteidigungsanlagen wurden aktualisiert!", $num));
        }

        return $this->render('admin/defense/points.html.twig', [
            'defenses' => $this->defenseDataRepository->getAllDefenses(),
            'form' => $form
        ]);
    }

    #[Route('/admin/defense/requirements', name: 'admin.defense.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $defenses = $this->defenseDataRepository->getAllDefenses();
        $form = $this->createForm(ObjectRequirementListType::class, $defenses, ['type'=>DefenseRequirements::class]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->defenseRepository->save();

            $this->addFlash('success', 'Voraussetzungen aktualisiert');
        }

        return $this->render('admin/requirements/requirements.html.twig', [
            'objects' => $defenses,
            'form' => $form->createView(),
            'name' => 'Verteidigung',
        ]);
    }
}
