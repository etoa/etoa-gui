<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminNotesRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotepadController extends AbstractAdminController
{
    public function __construct(
        private AdminNotesRepository $adminNotesRepository
    ) {
    }

    /**
     * @Route("/admin/notepad", name="admin.notepad")
     */
    public function noteIndex(): Response
    {
        $user = $this->getUser();

        return $this->render('admin/notepad/index.html.twig', [
            'notes' => $this->adminNotesRepository->findAllForAdmin($user->getId()),
        ]);
    }

    /**
     * @Route("/admin/notepad/new", name="admin.notepad.new")
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $this->adminNotesRepository->create($request->request->get('Titel'), $request->request->get('Text'), $user->getId());

            return $this->redirectToRoute('admin.notepad');
        }

        return $this->render('admin/notepad/new.html.twig');
    }

    /**
     * @Route("/admin/notepad/{id}/edit", name="admin.notepad.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $user = $this->getUser();

        $note = $this->adminNotesRepository->findForAdmin($id, $user->getId());
        if ($note === null) {
            $this->addFlash('error', 'Notiz nicht gefunden');
        }

        if ($request->isMethod('POST')) {
            $this->adminNotesRepository->update($id, $request->request->get('Titel'), $request->request->get('Text'));

            return $this->redirectToRoute('admin.notepad');
        }

        return $this->render('admin/notepad/edit.html.twig', [
            'note' => $note,
        ]);
    }

    /**
     * @Route("/admin/notepad/{id}/delete", name="admin.notepad.delete")
     */
    public function delete(int $id): RedirectResponse
    {
        $user = $this->getUser();

        $note = $this->adminNotesRepository->findForAdmin($id, $user->getId());
        if ($note !== null) {
            $this->adminNotesRepository->remove($id);

            $this->addFlash('success', "Notiz gelöscht");
        }

        return $this->redirectToRoute('admin.notepad');
    }
}
