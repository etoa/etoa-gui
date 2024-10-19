<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Request\Admin\UserObserveRequest;
use EtoA\Form\Type\Admin\EditUserObserverType;
use EtoA\Form\Type\Admin\UserObserveType;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSessionSearch;
use EtoA\User\UserSurveillanceRepository;
use EtoA\User\UserSurveillanceSearch;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserObserverController extends AbstractAdminController
{
    public function __construct(
        private readonly UserRepository             $userRepository,
        private readonly UserSurveillanceRepository $userSurveillanceRepository,
        private readonly UserSessionRepository      $userSessionRepository,
    )
    {
    }

    #[Route('/admin/users/observer', name: 'admin.users.observer', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function list(Request $request): Response
    {
        $formRequest = new UserObserveRequest();
        $form = $this->createForm(UserObserveType::class, $formRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->updateObserve((int)$formRequest->userId, $formRequest->reason);

            $this->addFlash('success', 'Spieler unter beobachtung gestellt');
        }

        $users = $this->userRepository->searchAdminView(UserSearch::create()->observed());
        $userIds = array_map(fn(User $user) => $user->getId(), $users);
        $entryCounts = $this->userSurveillanceRepository->counts($userIds);

        return $this->render('admin/user-observer/list.html.twig', [
            'users' => $users,
            'entryCounts' => $entryCounts,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/observer/{id}/details', name: 'admin.users.observer.details')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function details(int $id): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'User existiert nicht');

            return $this->redirectToRoute('admin.users.observer');
        }

        $sessionActionCounts = $this->userSurveillanceRepository->countPerSession(UserSurveillanceSearch::create()->userId($id));
        $sessionTimestamps = $this->userSurveillanceRepository->timestampsPerSession(UserSurveillanceSearch::create()->userId($id));
        $sessionLogs = $this->userSessionRepository->getSessionLogs(UserSessionSearch::create()->userId($id));
        $sessions = $this->userSessionRepository->getActiveUserSessions($id);

        $availableSessions = [];
        foreach ($sessionLogs as $session) {
            $availableSessions[$session->sessionId] = $session;
        }
        foreach ($sessions as $session) {
            $availableSessions[$session->id] = $session;
        }

        return $this->render('admin/user-observer/details.html.twig', [
            'sessionTimestamps' => $sessionTimestamps,
            'sessionActionCounts' => $sessionActionCounts,
            'sessions' => $availableSessions,
            'user' => $user,
        ]);
    }

    #[Route('/admin/users/observer/{id}/details/{sessionId}', name: 'admin.users.observer.details.session')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function sessionDetails(int $id, string $sessionId): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'User existiert nicht');

            return $this->redirectToRoute('admin.users.observer');
        }

        $session = $this->userSessionRepository->findLog($sessionId);
        if ($session === null) {
            $session = $this->userSessionRepository->find($sessionId);
        }

        return $this->render('admin/user-observer/session-details.html.twig', [
            'entries' => $this->userSurveillanceRepository->search(UserSurveillanceSearch::create()->session($sessionId)),
            'session' => $session,
            'sessionId' => $sessionId,
            'user' => $user,
        ]);
    }

    #[Route('/admin/users/observer/{id}/edit', name: 'admin.users.observer.edit')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function edit(Request $request, int $id): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null || null === $user->getObserve()) {
            $this->addFlash('error', 'Spieler steht nicht unter beobachtung');

            return $this->redirectToRoute('admin.users.observer');
        }

        $form = $this->createForm(EditUserObserverType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->updateObserve($user->getId(), $user->getObserve());
            $this->addFlash('success', 'Beobachtungsgrund aktualisiert!');

            return $this->redirectToRoute('admin.users.observer');
        }

        return $this->render('admin/user-observer/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/observer/{id}/remove', name: 'admin.users.observer.remove')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function remove(int $id): Response
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null || null === $user->getObserve()) {
            $this->addFlash('error', 'Spieler steht nicht unter beobachtung');
        } else {
            $this->userRepository->updateObserve($user->getId(), null);
            $this->userSurveillanceRepository->removeForUser($user->getId());

            $this->addFlash('success', 'Spieler von der Beobachtungsliste entfernt');
        }

        return $this->redirectToRoute('admin.users.observer');
    }
}
