<?php declare(strict_types=1);

namespace EtoA\Quest\Reward;

use EtoA\Defense\DefenseRepository;
use EtoA\Planet\PlanetRepository;
use LittleCubicleGames\Quests\Definition\Reward\Reward;
use LittleCubicleGames\Quests\Definition\Reward\RewardInterface;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Reward\Collect\CollectorInterface;

class DefenseRewardCollector implements CollectorInterface
{
    public const TYPE = 'defense';

    /** @var DefenseRepository */
    private $defenseRepository;
    /** @var PlanetRepository */
    private $planetRepository;

    public function __construct(DefenseRepository $defenseRepository, PlanetRepository $planetRepository)
    {
        $this->defenseRepository = $defenseRepository;
        $this->planetRepository = $planetRepository;
    }

    public function collect(RewardInterface $reward, QuestInterface $quest): void
    {
        if ($reward instanceof Reward) {
            $data = $reward->getData();

            $mainPlanetId = $this->planetRepository->getUserMainId($quest->getUser());
            $this->defenseRepository->addDefense($data['defense_id'], $data['value'], $quest->getUser(), $mainPlanetId);
        }
    }

    public function getType(): string
    {
        return self::TYPE;
    }
}
