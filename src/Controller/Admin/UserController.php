<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\UserLoginFailureType;
use EtoA\Form\Type\Admin\UserSearchType;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSittingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractAdminController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserSittingRepository $userSittingRepository,
        private UserLoginFailureRepository $loginFailureRepository,
    ) {
    }

    #[Route('/admin/users/', name: 'admin.users')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function list(Request $request): Response
    {
        return $this->render('admin/user/list.html.twig', [
            'form' => $this->createForm(UserSearchType::class, $request->query->all())->createView(),
            'total' => $this->userRepository->count(),
        ]);
    }

    #[Route('/admin/users/sitting', name: 'admin.users.sitting')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function sitting(): Response
    {
        return $this->render('admin/user/sitting.html.twig', [
            'entries' => $this->userSittingRepository->getActiveSittingEntries(),
        ]);
    }

    #[Route('/admin/users/login-failures', name: 'admin.users.login-failures')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function loginFailures(Request $request): Response
    {
        return $this->render('admin/user/login-failures.html.twig', [
            'form' => $this->createForm(UserLoginFailureType::class, $request->query->all())->createView(),
            'total' => $this->loginFailureRepository->count(),
        ]);
    }
}
