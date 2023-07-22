<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\UserLoginFailureType;
use EtoA\Form\Type\Admin\UserSearchType;
use EtoA\HostCache\NetworkNameService;
use EtoA\Ranking\UserBannerService;
use EtoA\User\ProfileImage;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSessionSearch;
use EtoA\User\UserSittingRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UsersController extends AbstractAdminController
{
    public function __construct(
        private readonly UserRepository             $userRepository,
        private readonly UserSittingRepository      $userSittingRepository,
        private readonly UserLoginFailureRepository $loginFailureRepository,
        private readonly UserBannerService          $userBannerService,
        private readonly UserSessionRepository      $userSessionRepository,
        private readonly UserMultiRepository        $userMultiRepository,
        private readonly UserLoginFailureRepository $userLoginFailureRepository,
        private readonly NetworkNameService         $networkNameService,
        private readonly string                     $webRooDir,
    )
    {
    }

    #[Route('/admin/users/', name: 'admin.users')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function list(Request $request): Response
    {
        return $this->render('admin/user/list.html.twig', [
            'form' => $this->createForm(UserSearchType::class, $request->query->all()),
            'total' => $this->userRepository->count(),
        ]);
    }

    #[Route('/admin/users/sitting', name: 'admin.users.sitting', priority: 10)]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function sitting(): Response
    {
        return $this->render('admin/user/sitting.html.twig', [
            'entries' => $this->userSittingRepository->getActiveSittingEntries(),
        ]);
    }

    #[Route('/admin/users/login-failures', name: 'admin.users.login-failures', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function loginFailures(Request $request): Response
    {
        return $this->render('admin/user/login-failures.html.twig', [
            'form' => $this->createForm(UserLoginFailureType::class, $request->query->all()),
            'total' => $this->loginFailureRepository->count(),
        ]);
    }

    #[Route('/admin/users/banners', name: 'admin.users.banners', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function banners(): Response
    {
        $banners = [];
        $userNicks = $this->userRepository->searchUserNicknames();
        foreach ($userNicks as $userId => $userNick) {
            $banners[$userNick] = $this->userBannerService->getUserBanner($userId);
        }

        return $this->render('admin/user/banners.html.twig', [
            'banners' => $banners,
            'width' => UserBannerService::BANNER_WIDTH,
            'height' => UserBannerService::BANNER_HEIGHT,
        ]);
    }

    #[Route('/admin/users/imagecheck', name: 'admin.users.imagecheck', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function imageCheck(Request $request): Response
    {
        $path = ProfileImage::IMAGE_PATH;
        $storedImagePath = $this->webRooDir . $path;
        if ($request->request->has('validate_submit')) {
            foreach ($request->request->all('validate') as $userId => $validate) {
                if ($validate == 0) {
                    $user = $this->userRepository->getUser($userId);
                    if ($user !== null) {
                        if (file_exists($storedImagePath . $user->getProfileImageUrl())) {
                            unlink($storedImagePath . $user->getProfileImageUrl());
                        }
                        if ($this->userRepository->updateImgCheck($userId, false, '')) {
                            $this->addFlash('success', 'Bild entfernt!');
                        }
                    }
                } else {
                    $this->userRepository->updateImgCheck($userId, false);
                }
            }
        }

        $usersWithImage = $this->userRepository->searchUsers(UserSearch::create()->confirmedImageCheck());
        $userImageExists = [];
        $usedPaths = [];
        foreach ($usersWithImage as $user) {
            $usedPaths[] = $user->profileImage;
            $userImageExists[$user->id] = file_exists($path . $user->profileImage);
        }

        $unused = [];
        if (is_dir($storedImagePath)) {
            $finder = Finder::create()->files()->in([$storedImagePath]);
            foreach ($finder as $file) {
                $url = str_replace($this->webRooDir, '', (string)$file->getRealPath());
                if (!in_array($url, $usedPaths, true)) {
                    $unused[$url] = $file;
                }
            }
        }

        if ($request->request->has('clearoverhead')) {
            foreach ($unused as $file) {
                unlink((string)$file->getRealPath());
            }

            $unused = [];
            $this->addFlash('success', 'Verwaiste Bilder gelöscht!');
        }

        return $this->render('admin/user/image-check.html.twig', [
            'usersWithImage' => $usersWithImage,
            'userImageExists' => $userImageExists,
            'imagePath' => $path,
            'unused' => $unused,
        ]);
    }

    #[Route('/admin/users/multis', name: 'admin.users.multis', priority: 10)]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function multis(): Response
    {
        $ips = [];
        foreach ($this->userSessionRepository->getLatestUserIps() as $ip) {
            $ips[$ip] = isset($ips[$ip]) ? $ips[$ip] + 1 : 1;
        }

        $ips = array_keys(array_filter($ips, fn($count) => $count > 1));

        $ipUsers = [];
        $userIds = [];
        foreach ($ips as $ip) {
            $users = $this->userRepository->getUsersWithIp($ip);
            $ipUsers[$ip] = $users;
            foreach ($users as $user) {
                $userIds[] = (int)$user['user_id'];
            }
        }

        $multiEntries = $this->userMultiRepository->getUsersEntries($userIds);
        $sittingEntries = $this->userSittingRepository->getActiveUsersEntry($userIds);

        return $this->render('admin/user/multi.html.twig', [
            'ipUsers' => array_filter($ipUsers),
            'multiEntries' => $multiEntries,
            'sittingEntries' => $sittingEntries,
            'time' => time(),
        ]);
    }

    #[Route('/admin/users/ips', name: 'admin.users.ips', priority: 10)]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function ipSearch(Request $request): Response
    {
        $ip = $request->query->get('ip');
        if (blank($ip)) {
            return $this->render('admin/user/ip-search-form.html.twig');
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = $this->networkNameService->getAddr($ip);
        }

        $sessions = $this->userSessionRepository->getSessions(UserSessionSearch::create()->ip($ip));
        $sessionLogs = $this->userSessionRepository->getSessionLogs(UserSessionSearch::create()->ip($ip));

        $userIds = [];
        foreach ($sessions as $session) {
            $userIds[] = $session->userId;
        }
        foreach ($sessionLogs as $log) {
            $userIds[] = $log->userId;
        }
        $userIds = array_unique($userIds);

        $multiEntries = $this->userMultiRepository->getUsersEntries($userIds);
        $sittingEntries = $this->userSittingRepository->getActiveUsersEntry($userIds);

        return $this->render('admin/user/ip-search.html.twig', [
            'ip' => $ip,
            'users' => $this->userRepository->searchUserNicknames(),
            'sessions' => $sessions,
            'sessionLogs' => $sessionLogs,
            'loginFailures' => $this->userLoginFailureRepository->getIpLoginFailures($ip),
            'multiEntries' => $multiEntries,
            'sittingEntries' => $sittingEntries,
        ]);
    }
}
