<?php declare(strict_types=1);

namespace EtoA\Controller\Game;

use EtoA\Security\Player\CurrentPlayer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class AbstractGameController extends AbstractController
{
    protected function getUser(): CurrentPlayer
    {
        $user = parent::getUser();
        if (!$user instanceof CurrentPlayer) {
            throw new AccessDeniedHttpException();
        }

        return $user;
    }
}
