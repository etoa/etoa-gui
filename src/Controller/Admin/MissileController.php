<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\AddMissileListType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Form\Type\Admin\MissileSearchType;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileListItem;
use EtoA\Missile\MissileRepository;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Requirement\ObjectRequirement;
use EtoA\Requirement\RequirementsUpdater;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function DeepCopy\deep_copy;

class MissileController extends AbstractAdminController
{
    public function __construct(
        private MissileRepository $missileRepository,
        private PlanetRepository $planetRepository,
        private MissileDataRepository $missileDataRepository,
        private MissileRequirementRepository $missileRequirementRepository,
    ) {
    }

    #[Route('/admin/missiles/', name: 'admin.missiles')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        $addItem = MissileListItem::empty();
        $addForm = $this->createForm(AddMissileListType::class, $addItem);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $userId = $this->planetRepository->getPlanetUserId($addItem->entityId);
            $this->missileRepository->addMissile($addItem->missileId, $addItem->count, $userId, $addItem->entityId);

            $this->addFlash('success', sprintf('%s Raketen hinzugefügt', StringUtils::formatNumber($addItem->count)));
        }

        return $this->render('admin/missiles/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(MissileSearchType::class, $request->query->all())->createView(),
            'total' => $this->missileRepository->count(),
        ]);
    }

    #[Route('/admin/missiles/requirements', name: 'admin.missiles.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $collection = $this->missileRequirementRepository->getAll();
        $missiles = $this->missileDataRepository->getMissiles();
        $requirements = [];
        $names = [];
        foreach ($missiles as $missile) {
            $names[$missile->id] = $missile->name;
            $requirements[$missile->id] = $collection->getAll($missile->id);
        }

        $requirementsCopy = deep_copy($requirements);

        $form = $this->createForm(ObjectRequirementListType::class, $requirements, ['objectIds' => array_keys($missiles), 'objectNames' => $names]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ObjectRequirement[][] $updatedRequirements */
            $updatedRequirements = $form->getData();
            (new RequirementsUpdater($this->missileRequirementRepository))->update($requirementsCopy, $updatedRequirements);

            $this->addFlash('success', 'Voraussetzungen aktualisiert');
        }

        return $this->render('admin/missiles/requirements.html.twig', [
            'missiles' => $missiles,
            'form' => $form->createView(),
        ]);
    }
}
