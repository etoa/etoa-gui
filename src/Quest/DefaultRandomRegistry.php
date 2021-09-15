<?php declare(strict_types=1);

namespace EtoA\Quest;

use Doctrine\Common\Cache\Cache;
use LittleCubicleGames\Quests\Definition\Quest\QuestBuilder;
use LittleCubicleGames\Quests\Definition\Registry\RandomRegistry;
use LittleCubicleGames\Quests\Guard\TriggerValidator;

class DefaultRandomRegistry extends RandomRegistry
{
    public function __construct(QuestBuilder $questBuilder, TriggerValidator $triggerValidator, Cache $cache)
    {
        $quests = require __DIR__ . '/../../data/quests.php';

        parent::__construct($quests, $questBuilder, $triggerValidator, $cache, 'random');
    }
}
