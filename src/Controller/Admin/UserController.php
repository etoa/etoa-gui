<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Request\Admin\UserCreateRequest;
use EtoA\Form\Type\Admin\UserCreateType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\User\UserService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private LogRepository $logRepository,
    ) {
    }

    #[Route('/admin/users/new', name: 'admin.users.new')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function new(Request $request): Response
    {
        $createUserRequest = new UserCreateRequest();
        $form = $this->createForm(UserCreateType::class, $createUserRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $this->userService->register($createUserRequest->name, $createUserRequest->email, $createUserRequest->nick, $createUserRequest->password, $createUserRequest->raceId, $createUserRequest->ghost, true);
                $this->logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $user->nick . " (" . $user->name . ", " . $user->email . ") wurde registriert!");
                $this->addFlash('success', 'Spieler erstellt');

                return $this->redirectToRoute('admin.users.edit', ['id' => $user->id]);
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/{id}/edit', name: 'admin.users.edit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function edit(int $id): RedirectResponse
    {
        return $this->redirect('/admin/?page=user&sub=edit&id=' . $id);
    }
}
