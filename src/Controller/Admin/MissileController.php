<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Entity\MissileListItem;
use EtoA\Entity\MissileRequirements;
use EtoA\Form\Type\Admin\AddMissileListType;
use EtoA\Form\Type\Admin\MissileSearchType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Support\StringUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MissileController extends AbstractAdminController
{
    public function __construct(
        private readonly MissileRepository            $missileRepository,
        private readonly MissileDataRepository        $missileDataRepository
    )
    {
    }

    #[Route('/admin/missiles/', name: 'admin.missiles')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        $addItem = new MissileListItem();
        $addForm = $this->createForm(AddMissileListType::class, $addItem);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $this->missileRepository->addMissile($addItem->getMissile(), $addItem->getCount(), $addForm->get('entity')->getData()->getUser(), $addItem->getEntity());

            $this->addFlash('success', sprintf('%s Raketen hinzugefügt', StringUtils::formatNumber($addItem->getCount())));
        }

        return $this->render('admin/missiles/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(MissileSearchType::class, $request->query->all()),
            'total' => $this->missileRepository->count([]),
        ]);
    }

    #[Route('/admin/missiles/requirements', name: 'admin.missiles.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $missiles = $this->missileDataRepository->getMissiles();

        $form = $this->createForm(ObjectRequirementListType::class, $missiles, ['type'=>MissileRequirements::class]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->missileDataRepository->save();

            $this->addFlash('success', 'Voraussetzungen aktualisiert');
        }

        return $this->render('admin/requirements/requirements.html.twig', [
            'objects' => $missiles,
            'form' => $form->createView(),
            'name' => 'Raketen',
        ]);
    }
}
