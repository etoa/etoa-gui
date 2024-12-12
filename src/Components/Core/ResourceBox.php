<?php

namespace EtoA\Components\Core;

use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsTwigComponent(template: 'components/resourceBox.html.twig')]
class ResourceBox
{
    public ?string $cpid;

    public function __construct(
        private readonly ResourceBoxDrawer $resourceBoxDrawer,
        private readonly PlanetRepository $planetRepository,
        private readonly RequestStack $requestStack
    )
    {
    }

    public function getResourceBox():string
    {
        $cpid = $this->cpid??$this->requestStack->getSession()->get('cpid');
        $planet = $this->planetRepository->find($cpid);
        return $this->resourceBoxDrawer->getHTML($planet);
    }
}