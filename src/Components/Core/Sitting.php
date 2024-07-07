<?php

namespace EtoA\Components\Core;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\Component\HttpFoundation\RequestStack;
use EtoA\Support\StringUtils;

#[AsTwigComponent(template: 'components/sitting.html.twig')]
class Sitting
{
    public function __construct(
        protected RequestStack $requestStack,
    ) {
    }

    public function getSittingUntil(): string|bool
    {
        $session = $this->requestStack->getSession();
        if($session->get('sittingActive')) {
            return StringUtils::formatDate($session->get('sittingUntil'));
        }
        return false;
    }
}