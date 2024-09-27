<?php

namespace EtoA\Components\Core;

use EtoA\Design\DesignsService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;

#[AsLiveComponent(template: 'components/design_info.twig')]
class DesignInfo
{
    public function __construct(
        private readonly DesignsService $designsService
    ){}

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $cssStyle;

    #[LiveProp]
    public string $dropdown;

    #[LiveAction]
    public function getInfoText(): string
    {
        $designs = $this->designsService->getDesigns();

        if ($this->cssStyle && isset($designs[$this->cssStyle])) {
            $cd = $designs[$this->cssStyle];
            $out = "
                <b>Version:</b> " . $cd['version'] . "<br/>
                <b>Geändert:</b> " . $cd['changed'] . "<br/>
                <b>Autor:</b> <a href=\"mailto:" . $cd['email'] . "\">" . $cd['author'] . "</a><br/>
                <b>Beschreibung:</b> " . $cd['description'] . "";
            }    else {
                $out = '';
            }

        return $out;
    }
}