<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Form\Type\Admin\NewDefaultItemSetType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultItemController extends AbstractAdminController
{
    public function __construct(
        private DefaultItemRepository $defaultItemRepository,
    ) {
    }

    #[Route('/admin/default-items/', name: 'admin.default-items')]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function list(Request $request): Response
    {
        $addForm = $this->createForm(NewDefaultItemSetType::class);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $this->defaultItemRepository->createSet($addForm->getData()['name']);

            $this->addFlash('success', "Set erstellt!");
        }

        return $this->render('admin/defaultitems.html.twig', [
            'itemSets' => $this->defaultItemRepository->getSets(false),
            'addForm' => $addForm->createView(),
        ]);
    }

    #[Route('/admin/default-items/{id}/toggle', name: 'admin.default-items.toggle')]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function toggle(int $id): RedirectResponse
    {
        $this->defaultItemRepository->toggleSetActive($id);

        return $this->redirectToRoute('admin.default-items');
    }

    #[Route('/admin/default-items/{id}/delete', name: 'admin.default-items.delete')]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function delete(int $id): RedirectResponse
    {
        $this->defaultItemRepository->deleteSet($id);

        $this->addFlash('success', "Set gelöscht!");

        return $this->redirectToRoute('admin.default-items');
    }
}
