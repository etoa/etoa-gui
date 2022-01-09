<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\AddMissileListType;
use EtoA\Form\Type\Admin\MissileSearchType;
use EtoA\Missile\MissileListItem;
use EtoA\Missile\MissileRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MissileController extends AbstractAdminController
{
    public function __construct(
        private MissileRepository $missileRepository,
        private PlanetRepository $planetRepository,
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
}
