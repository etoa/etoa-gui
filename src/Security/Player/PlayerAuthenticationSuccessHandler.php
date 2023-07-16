<?php declare(strict_types=1);

namespace EtoA\Security\Player;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class PlayerAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    public function __construct(
        HttpUtils             $httpUtils,
        UrlGeneratorInterface $urlGenerator,
    )
    {
        parent::__construct($httpUtils, [
            'default_target_path' => $urlGenerator->generate('game.index'),
            'login_path' => $urlGenerator->generate('game.login'),
        ]);
    }
}
