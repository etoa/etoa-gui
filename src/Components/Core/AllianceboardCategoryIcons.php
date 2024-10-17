<?php

namespace EtoA\Components\Core;

use EtoA\Admin\AllianceBoardAvatar;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent(template: 'components/allianceboard_category_icons.html.twig')]
class AllianceboardCategoryIcons
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $image = AllianceBoardAvatar::DEFAULT_IMAGE;

    #[LiveProp]
    public string $dropdown;

    #[PreMount]
    public function preMount(array $data): array
    {
        if(!$data['image'])
            $data['image'] = AllianceBoardAvatar::DEFAULT_IMAGE;

        return $data;
    }
}