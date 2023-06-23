<?php declare(strict_types=1);

namespace EtoA\Security\Admin;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AdminAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    public function __construct(HttpUtils $httpUtils)
    {
        parent::__construct($httpUtils, [
            'default_target_path' => '/admin/',
            'login_path' => '/admin/login',
        ]);
    }
}
