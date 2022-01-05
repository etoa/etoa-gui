<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\EntitySearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntityController extends AbstractAdminController
{
    #[Route('/admin/universe/entities', name: 'admin.universe.entities')]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function search(Request $request): Response
    {
        return $this->render('admin/universe/entities.html.twig', [
            'form' => $this->createForm(EntitySearchType::class, $request->query->all())->createView(),
        ]);
    }
}
