<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\AddTechnologyItemType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Form\Type\Admin\TechnologySearchType;
use EtoA\Ranking\RankingService;
use EtoA\Requirement\ObjectRequirement;
use EtoA\Requirement\RequirementsUpdater;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyListItem;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologyRequirementRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function DeepCopy\deep_copy;

class TechnologyController extends AbstractAdminController
{
    public function __construct(
        private TechnologyRepository $technologyRepository,
        private TechnologyDataRepository $technologyDataRepository,
        private TechnologyPointRepository $technologyPointRepository,
        private RankingService $rankingService,
        private TechnologyRequirementRepository $technologyRequirementRepository,
    ) {
    }

    #[Route("/admin/technology/", name: "admin.technology")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        return $this->render('admin/technology/search.html.twig', [
            'form' => $this->createForm(TechnologySearchType::class, $request->request->all())->createView(),
            'total' => $this->technologyRepository->count(),
        ]);
    }

    #[Route("/admin/technology/add", name: "admin.technology.add")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function add(Request $request): Response
    {
        $item = TechnologyListItem::empty();
        $form = $this->createForm(AddTechnologyItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ((bool) $form->get('all')->getData()) {
                $techIds = array_keys($this->technologyDataRepository->getTechnologyNames(true));
                foreach ($techIds as $techId) {
                    $this->technologyRepository->addTechnology($techId, $item->currentLevel, $item->userId, $item->entityId);
                }

                $this->addFlash('success', count($techIds) . ' Forschungen hinzugefügt');
            } else {
                $this->technologyRepository->addTechnology($item->technologyId, $item->currentLevel, $item->userId, $item->entityId);

                $this->addFlash('success', 'Forschung hinzugefügt');
            }
        }

        return $this->render('admin/technology/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/technology/points", name: "admin.technology.points")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function points(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $numTechnologies = $this->rankingService->calcTechPoints();
            $this->addFlash('success', sprintf("Die Punkte von %s Technologien wurden aktualisiert!", $numTechnologies));
        }

        return $this->render('admin/technology/points.html.twig', [
            'technologyNames' => $this->technologyDataRepository->getTechnologyNames(true),
            'pointsMap' => $this->technologyPointRepository->getAllMap(),
        ]);
    }

    #[Route('/admin/technology/requirements', name: 'admin.technology.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $collection = $this->technologyRequirementRepository->getAll();
        $technologies = $this->technologyDataRepository->getTechnologies();
        $requirements = [];
        $names = [];
        foreach ($technologies as $technology) {
            $names[$technology->id] = $technology->name;
            $requirements[$technology->id] = $collection->getAll($technology->id);
        }

        $requirementsCopy = deep_copy($requirements);

        $form = $this->createForm(ObjectRequirementListType::class, $requirements, ['objectIds' => array_keys($names), 'objectNames' => $names]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ObjectRequirement[][] $updatedRequirements */
            $updatedRequirements = $form->getData();
            (new RequirementsUpdater($this->technologyRequirementRepository))->update($requirementsCopy, $updatedRequirements);

            $this->addFlash('success', 'Voraussetzungen aktualisiert');
        }

        return $this->render('admin/requirements/requirements.html.twig', [
            'objects' => $technologies,
            'form' => $form->createView(),
            'name' => 'Forschung',
        ]);
    }
}
