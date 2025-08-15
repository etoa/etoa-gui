<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Entity\UserWarning;
use EtoA\Form\Type\Admin\AddUserWarningType;
use EtoA\Form\Type\Admin\EditUserWarningType;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\User\UserWarningRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserWarningController extends AbstractAdminController
{
    public function __construct(
        private readonly UserWarningRepository $userWarningRepository,
        private readonly MessageRepository     $messageRepository,
        private readonly MessageCategoryRepository $messageCategoryRepository
    )
    {
    }

    #[Route('/admin/users/warnings', name: 'admin.users.warnings', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function show(Request $request): Response
    {
        $warning = new UserWarning();
        $form = $this->createForm(AddUserWarningType::class, $warning);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $warning->setAdmin($this->getUser()->getData());
            $warning->setDate(time());

            $this->userWarningRepository->persist($warning);
            $this->userWarningRepository->save();
            $this->messageRepository->createSystemMessage($warning->getUser(), $this->messageCategoryRepository->find(7), "Verwarnung", "Du hast vom Administrator " . $this->getUser()->getUsername() . " eine Verwarnung erhalten!\n\n" . $warning->getText());
            $this->addFlash('success', 'Verwarnung gespeichert');
        }

        $warnings = $this->userWarningRepository->findAll();

        return $this->render('admin/user-warning/list.html.twig', [
            'form' => $form->createView(),
            'warnings' => $warnings,
        ]);
    }

    #[Route('/admin/users/warnings/{id}/edit', name: 'admin.users.warnings.edit')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function edit(Request $request, ?UserWarning $warning): Response
    {
        if ($warning === null) {
            $this->addFlash('error', 'Verwarnung nicht vorhanden');

            return $this->redirectToRoute('admin.users.warnings');
        }

        $form = $this->createForm(EditUserWarningType::class, $warning);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userWarningRepository->save();
            $this->addFlash('success', "Verwarnung gespeichert!");

            return $this->redirectToRoute('admin.users.warnings');
        }

        return $this->render('admin/user-warning/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/warnings/{id}/delete', name: 'admin.users.warnings.delete')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function delete(UserWarning $userWarning): RedirectResponse
    {
        $this->userWarningRepository->remove($userWarning);
        $this->userWarningRepository->save();

        $this->addFlash('success', "Verwarnung gelöscht!");

        return $this->redirectToRoute('admin.users.warnings');
    }
}
