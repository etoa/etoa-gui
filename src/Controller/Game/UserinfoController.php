<?php

namespace EtoA\Controller\Game;

use EtoA\Entity\User;
use EtoA\User\UserLogRepository;
use EtoA\User\UserRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserinfoController extends AbstractGameController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserLogRepository $userLogRepository
    )
    {
    }

    #[Route('/game/userinfo/{id}', name: 'game.userinfo')]
    public function info(?User $user = null):Response
    {
        if($user) {
            if ($user !== $this->getUser()->getData()) {
                $this->userRepository->addVisit($user);
            }

            return $this->render('game/userinfo/userinfo.html.twig',[
                'user'=>$user,
                'userLogRepository' => $this->userLogRepository
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Dieser Spieler existiert nicht!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Benutzerprofil'
        ]);
    }
}