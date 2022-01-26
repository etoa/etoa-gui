<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\UserSearchType;
use EtoA\User\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractAdminController
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    #[Route('/admin/users/', name: 'admin.users')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function list(): Response
    {
        return $this->render('admin/user/list.html.twig', [
            'form' => $this->createForm(UserSearchType::class)->createView(),
            'total' => $this->userRepository->count(),
        ]);
    }
}
