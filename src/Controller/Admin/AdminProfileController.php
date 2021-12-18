<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\User\UserRepository;
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
        private LogRepository $logRepository,
        private ConfigurationService $config
    ) {
    }

    /**
     * @Route("/admin/profile/", name="admin.profile", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/profile/profile.html.twig', [
            'user' => $this->getUser()->getData(),
            'users' => $this->userRepository->searchUserNicknames(),
        ]);
    }

    /**
     * @Route("/admin/profile/", name="admin.profile.update", methods={"POST"})
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $adminUser = $this->getUser();

        $data = $adminUser->getData();
        $data->name = $request->request->get('user_name');
        $data->email = $request->request->get('user_email');
        $data->boardUrl = $request->request->get('user_board_url');
        $data->userTheme = $request->request->get('user_theme', '');
        $data->ticketEmail = $request->request->getBoolean('ticketmail');
        $data->playerId = $request->request->getInt('player_id');

        $this->adminUserRepository->save($data);
        $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $data->nick . " ändert seine Daten");

        $this->addFlash('success', 'Die Daten wurden geändert!');

        return $this->redirectToRoute('admin.profile');
    }

    /**
     * @Route("/admin/profile/password", name="admin.profile.password", methods={"POST"})
     */
    public function updatePassword(Request $request, UserPasswordHasherInterface $passwordHasher): RedirectResponse
    {
        $adminUser = $this->getUser();

        if (!$passwordHasher->isPasswordValid($adminUser, $request->request->get('user_password_old'))) {
            $this->addFlash('error', 'Das alte Passwort stimmt nicht mit dem gespeicherten Wert überein!');
        } elseif (!($request->request->get('user_password') === $request->request->get('user_password2') && $request->request->get('user_password_old') !== $request->request->get('user_password'))) {
            $this->addFlash('error', 'Die Kennwortwiederholung stimmt nicht oder das alte und das neue Passwort sind gleich!');
        } elseif (strlen($request->request->get('user_password')) < $this->config->getInt('password_minlength')) {
            $this->addFlash('error', 'Das Passwort ist zu kurz! Es muss mindestens ' . $this->config->getInt('password_minlength') . ' Zeichen lang sein!');
        } else {
            $this->adminUserRepository->setPassword($adminUser->getData(), $passwordHasher->hashPassword($adminUser, $request->request->get('user_password')));
            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $adminUser->getId() . " ändert sein Passwort");

            $this->addFlash('success', 'Das Passwort wurde geändert!');
        }

        return $this->redirectToRoute('admin.profile');
    }
}
