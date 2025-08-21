<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Entity\User;
use EtoA\Form\Request\Admin\UserObserveRequest;
use EtoA\Form\Type\Admin\EditUserObserverType;
use EtoA\Form\Type\Admin\UserObserveType;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserSessionLogRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSurveillanceRepository;
use EtoA\User\UserSurveillanceSearch;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        private readonly UserSessionLogRepository   $userSessionLogRepository
    )
    {
    }

    #[Route('/admin/users/observer', name: 'admin.users.observer', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function list(Request $request): Response
    {
        $formRequest = new UserObserveRequest();
        $form = $this->createForm(UserObserveType::class, $formRequest)->add('submit', SubmitType::class, [
            'label' => 'Zur Beobachtungsliste hinzufügen',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $formRequest->user;
            $user->setObserve($formRequest->reason);

            $this->userRepository->save();

            $this->addFlash('success', 'Spieler unter beobachtung gestellt');
        }

        $search = UserSearch::create();
        $search->parts[] = 'user_observe IS NOT NULL';
        $users = $this->userRepository->searchAdminView($search);
        $userIds = array_map(fn(array $user) => $user['user_id'], $users);
        $entryCounts = $this->userSurveillanceRepository->counts($userIds);

        return $this->render('admin/user-observer/list.html.twig', [
            'users' => $users,
            'entryCounts' => $entryCounts,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/observer/{id}/details', name: 'admin.users.observer.details')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function details(?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'User existiert nicht');

            return $this->redirectToRoute('admin.users.observer');
        }

        $sessionActionCounts = $this->userSurveillanceRepository->countPerSession(UserSurveillanceSearch::create()->user($user));
        $sessionTimestamps = $this->userSurveillanceRepository->timestampsPerSession(UserSurveillanceSearch::create()->user($user));
        $sessionLogs = $user->getSessionLogs();
        $currentSession = $user->getSession();

        $availableSessions = [];
        foreach ($sessionLogs as $session) {
            $availableSessions[$session->getSessionId()] = $session;
        }
        if($currentSession)
            $availableSessions[$currentSession->getId()] = $currentSession;

        return $this->render('admin/user-observer/details.html.twig', [
            'sessionTimestamps' => $sessionTimestamps,
            'sessionActionCounts' => $sessionActionCounts,
            'sessions' => $availableSessions,
            'user' => $user,
        ]);
    }

    #[Route('/admin/users/observer/{id}/details/{sessionId}', name: 'admin.users.observer.details.session')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function sessionDetails(string $sessionId, ?User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash('error', 'User existiert nicht');

            return $this->redirectToRoute('admin.users.observer');
        }

        $session = $this->userSessionRepository->findOneBy(['id'=>$sessionId]);
        if (!$session) {
            $session = $this->userSessionLogRepository->findOneBy(['id'=>$sessionId]);
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
    public function edit(Request $request, ?User $user = null): Response
    {
        if ($user === null || null === $user->getObserve()) {
            $this->addFlash('error', 'Spieler steht nicht unter beobachtung');

            return $this->redirectToRoute('admin.users.observer');
        }

        $form = $this->createForm(EditUserObserverType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save();
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
    public function remove(?User $user = null): Response
    {
        if ($user === null || null === $user->getObserve()) {
            $this->addFlash('error', 'Spieler steht nicht unter beobachtung');
        } else {
            $user->setObserve(null);
            $this->userRepository->save();
            $this->userSurveillanceRepository->removeForUser($user);

            $this->addFlash('success', 'Spieler von der Beobachtungsliste entfernt');
        }

        return $this->redirectToRoute('admin.users.observer');
    }
}
