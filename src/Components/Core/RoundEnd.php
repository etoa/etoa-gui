<?php

namespace EtoA\Components\Core;

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\Component\HttpFoundation\RequestStack;
use EtoA\Support\StringUtils;

#[AsTwigComponent(template: 'components/round_end.html.twig')]
class RoundEnd
{
    public function __construct(
        private readonly ConfigurationService         $config,
    ) {
    }

    public function getParam1(): string
    {
        #die();
         return StringUtils::formatDate($this->config->param1Int("round_end"));
    }

    public function getParam2(): string
    {
        return $this->config->param2("round_end") ?? '';
    }
}