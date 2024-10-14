<?php

namespace EtoA\Components\Core;

use EtoA\User\UserPropertiesRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/table.html.twig')]
class Table
{
    public string $title;

    public mixed $width = 0;

    public string $layout = '';

    public string $class = '';

    public string $style  = '';

    public function __construct(
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly ?UserInterface           $user = null,
    )
    {
    }

    public function getCalculatedWidth(): string
    {
        if (is_numeric($this->width) && $this->width > 0) {
            return "width:" . $this->width . "px;";
        }
        if (!empty($this->width)) {
            return "width:" . $this->width;
        }

        $userId = $this->user?->getId();
        if ($userId !== null) {
            $properties = $this->userPropertiesRepository->getOrCreateProperties($userId);
            if ($properties->cssStyle == "Graphite") {
                return "width:650px";
            }
        }

        return "width:100%";
    }
}