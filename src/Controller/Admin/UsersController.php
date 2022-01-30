<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\UserLoginFailureType;
use EtoA\Form\Type\Admin\UserSearchType;
use EtoA\Ranking\UserBannerService;
use EtoA\User\ProfileImage;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserPointsRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserSittingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractAdminController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserSittingRepository $userSittingRepository,
        private UserLoginFailureRepository $loginFailureRepository,
        private UserPointsRepository $userPointsRepository,
        private UserBannerService $userBannerService,
        private string $webRooDir,
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

    #[Route('/admin/users/points', name: 'admin.users.points')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function points(Request $request): Response
    {
        $users = $this->userRepository->searchUserNicknames();
        if (count($users) === 0) {
            $this->addFlash('error', 'Keine Benutzer vorhanden!');
        }

        $user = null;
        $points = [];
        if ($request->query->getInt('userId') > 0) {
            $user = $this->userRepository->getUser($request->query->getInt('userId'));
            if ($user !== null) {
                $points = $this->userPointsRepository->getPoints($user->id);
            }
        }

        return $this->render('admin/user/points.html.twig', [
            'users' => $users,
            'user' => $user,
            'points' => $points,
        ]);
    }

    #[Route('/admin/users/banners', name: 'admin.users.banners')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function banners(Request $request): Response
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

    #[Route('/admin/users/imagecheck', name: 'admin.users.imagecheck')]
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
                $url = str_replace($this->webRooDir, '', (string) $file->getRealPath());
                if (!in_array($url, $usedPaths, true)) {
                    $unused[$url] = $file;
                }
            }
        }

        if ($request->request->has('clearoverhead')) {
            foreach ($unused as $file) {
                unlink((string) $file->getRealPath());
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
}
