<?php declare(strict_types=1);

namespace EtoA\Security\Admin;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AdminAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    public function __construct(
        HttpUtils             $httpUtils,
        UrlGeneratorInterface $urlGenerator,
    )
    {
        parent::__construct($httpUtils, [
            'default_target_path' => $urlGenerator->generate('admin.index'),
            'login_path' => $urlGenerator->generate('admin.login'),
        ]);
    }
}
