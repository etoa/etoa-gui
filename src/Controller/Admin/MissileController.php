<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Entity\MissileListItem;
use EtoA\Form\Type\Admin\AddMissileListType;
use EtoA\Form\Type\Admin\MissileSearchType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Missile\MissileRequirementRepository;
use EtoA\Requirement\ObjectRequirement;
use EtoA\Requirement\RequirementsUpdater;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function DeepCopy\deep_copy;

class MissileController extends AbstractAdminController
{
    public function __construct(
        private readonly MissileRepository            $missileRepository,
        private readonly PlanetRepository             $planetRepository,
        private readonly MissileDataRepository        $missileDataRepository,
        private readonly MissileRequirementRepository $missileRequirementRepository,
    )
    {
    }

    #[Route('/admin/missiles/', name: 'admin.missiles')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        $addItem = MissileListItem::empty();
        $addForm = $this->createForm(AddMissileListType::class, $addItem);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $userId = $this->planetRepository->getPlanetUserId($addItem->getEntityId());
            $this->missileRepository->addMissile($addItem->getMissileId(), $addItem->getCount(), $userId, $addItem->getEntityId());

            $this->addFlash('success', sprintf('%s Raketen hinzugefügt', StringUtils::formatNumber($addItem->getCount())));
        }

        return $this->render('admin/missiles/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(MissileSearchType::class, $request->query->all()),
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
            $names[$missile->getId()] = $missile->getName();
            $requirements[$missile->getId()] = $collection->getAll($missile->getId());
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

        return $this->render('admin/requirements/requirements.html.twig', [
            'objects' => $missiles,
            'form' => $form->createView(),
            'name' => 'Raketen',
        ]);
    }
}
