<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Request\Admin\AddUserWarningRequest;
use EtoA\Form\Type\Admin\AddUserWarningType;
use EtoA\Form\Type\Admin\EditUserWarningType;
use EtoA\Message\MessageRepository;
use EtoA\User\UserWarningRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserWarningController extends AbstractAdminController
{
    public function __construct(
        private UserWarningRepository $userWarningRepository,
        private MessageRepository $messageRepository,
    ) {
    }

    #[Route('/admin/users/warnings', name: 'admin.users.warnings')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function show(Request $request): Response
    {
        $formRequest = new AddUserWarningRequest();
        $form = $this->createForm(AddUserWarningType::class, $formRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userWarningRepository->addEntry($formRequest->userId, $formRequest->text, $this->getUser()->getId());
            $this->messageRepository->createSystemMessage($formRequest->userId, 7, "Verwarnung", "Du hast vom Administrator " . $this->getUser()->getUsername() . " eine Verwarnung erhalten!\n\n" . $formRequest->text);
            $this->addFlash('success', 'Verwarnung gespeichert');
        }

        $warnings = $this->userWarningRepository->search();
        $groupedWarnings = [];
        foreach ($warnings as $warning) {
            $groupedWarnings[$warning->id][] = $warning;
        }

        return $this->render('admin/user-warning/list.html.twig', [
            'form' => $form->createView(),
            'warnings' => $groupedWarnings,
        ]);
    }

    #[Route('/admin/users/warnings/{id}/edit', name: 'admin.users.warnings.edit')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function edit(Request $request, int $id): Response
    {
        $warning = $this->userWarningRepository->getWarning($id);
        if ($warning === null) {
            $this->addFlash('error', 'Verwarnung nicht vorhanden');

            return $this->redirectToRoute('admin.users.warnings');
        }

        $form = $this->createForm(EditUserWarningType::class, $warning);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userWarningRepository->updateEntry($warning->id, $warning->text, $warning->adminId);
            $this->addFlash('success', "Verwarnung gespeichert!");

            return $this->redirectToRoute('admin.users.warnings');
        }

        return $this->render('admin/user-warning/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/warnings/{id}/delete', name: 'admin.users.warnings.delete')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function delete(int $id): RedirectResponse
    {
        $this->userWarningRepository->deleteEntry($id);
        $this->addFlash('success', "Verwarnung gelöscht!");

        return $this->redirectToRoute('admin.users.warnings');
    }
}
