<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Form\Type\Admin\ProfilePasswordType;
use EtoA\Form\Type\Admin\ProfileType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\User\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminProfileController extends AbstractAdminController
{
    public function __construct(
        private UserRepository $userRepository,
        private AdminUserRepository $adminUserRepository,
        private LogRepository $logRepository
    ) {
    }

    #[Route("/admin/profile/", name: 'admin.profile')]
    public function index(Request $request): Response
    {
        $userData = $this->getUser()->getData();
        $profileForm = $this->createForm(ProfileType::class, $userData);
        $profileForm->handleRequest($request);
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $this->adminUserRepository->save($userData);
            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $userData->nick . " ändert seine Daten");

            $this->addFlash('success', 'Die Daten wurden geändert!');
        }

        return $this->render('admin/profile/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $this->createForm(ProfilePasswordType::class)->createView(),
            'user' => $userData,
            'users' => $this->userRepository->searchUserNicknames(),
        ]);
    }

    #[Route("/admin/profile/password", name: 'admin.profile.password', methods: ['POST'])]
    public function updatePassword(Request $request, UserPasswordHasherInterface $passwordHasher): RedirectResponse
    {
        $adminUser = $this->getUser();

        $form = $this->createForm(ProfilePasswordType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                if ($form instanceof FormError) {
                    $this->addFlash('error', $error->getMessage());
                }
            }

            return $this->redirectToRoute('admin.profile');
        }

        $data = $form->getData();

        if (!$passwordHasher->isPasswordValid($adminUser, $data['password'])) {
            $this->addFlash('error', 'Das alte Passwort stimmt nicht mit dem gespeicherten Wert überein!');
        } else {
            $this->adminUserRepository->setPassword($adminUser->getData(), $passwordHasher->hashPassword($adminUser, $data['new_password']));
            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $adminUser->getId() . " ändert sein Passwort");

            $this->addFlash('success', 'Das Passwort wurde geändert!');
        }

        return $this->redirectToRoute('admin.profile');
    }
}
