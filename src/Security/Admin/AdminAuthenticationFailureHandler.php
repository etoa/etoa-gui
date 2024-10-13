<?php declare(strict_types=1);

namespace EtoA\Security\Admin;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AdminAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    public function __construct(
        HttpKernelInterface   $httpKernel,
        HttpUtils             $httpUtils,
        LoggerInterface       $logger,
        UrlGeneratorInterface $urlGenerator,
    )
    {
        parent::__construct($httpKernel, $httpUtils, [
            'login_path' => $urlGenerator->generate('admin.login'),
        ], $logger);
    }
}