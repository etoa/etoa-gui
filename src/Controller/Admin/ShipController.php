<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Requirement\ObjectRequirement;
use EtoA\Requirement\RequirementsUpdater;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRequirementRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function DeepCopy\deep_copy;

class ShipController extends AbstractAdminController
{
    public function __construct(
        private ShipDataRepository $shipDataRepository,
        private ShipRequirementRepository $shipRequirementRepository
    ) {
    }

    #[Route('/admin/ships/requirements', name: 'admin.ships.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $collection = $this->shipRequirementRepository->getAll();
        $ships = $this->shipDataRepository->getAllShips();
        $requirements = [];
        $names = [];
        foreach ($ships as $ship) {
            $names[$ship->id] = $ship->name;
            $requirements[$ship->id] = $collection->getAll($ship->id);
        }

        $requirementsCopy = deep_copy($requirements);

        $form = $this->createForm(ObjectRequirementListType::class, $requirements, ['objectIds' => array_keys($ships), 'objectNames' => $names]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ObjectRequirement[][] $updatedRequirements */
            $updatedRequirements = $form->getData();
            (new RequirementsUpdater($this->shipRequirementRepository))->update($requirementsCopy, $updatedRequirements);

            $this->addFlash('success', 'Voraussetzungen aktualisiert');
        }

        return $this->render('admin/requirements/requirements.html.twig', [
            'objects' => $ships,
            'form' => $form->createView(),
            'name' => 'Schiffe',
        ]);
    }
}
