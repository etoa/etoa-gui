<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class AbstractAdminController extends AbstractController
{
    protected function getUser(): CurrentAdmin
    {
        $user = parent::getUser();
        if (!$user instanceof CurrentAdmin) {
            throw new AccessDeniedHttpException();
        }

        return $user;
    }
}
