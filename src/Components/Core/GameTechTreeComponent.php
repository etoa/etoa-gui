<?php declare(strict_types=1);

namespace EtoA\Components\Core;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent('game_techtree')]
class GameTechTreeComponent extends TechTreeComponent
{
    #[LiveProp(writable: true)]
    public string $headline = 'Grafische Darstellung';

    #[LiveProp(writable: true)]
    public bool $showSelection = true;
}
