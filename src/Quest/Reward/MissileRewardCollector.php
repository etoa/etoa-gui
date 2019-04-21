<?php declare(strict_types=1);

namespace EtoA\Quest\Reward;

use EtoA\Missile\MissileRepository;
use EtoA\Planet\PlanetRepository;
use LittleCubicleGames\Quests\Definition\Reward\Reward;
use LittleCubicleGames\Quests\Definition\Reward\RewardInterface;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Reward\Collect\CollectorInterface;

class MissileRewardCollector implements CollectorInterface
{
    public const TYPE = 'missile';

    /** @var MissileRepository */
    private $missileRepository;
    /** @var PlanetRepository */
    private $planetRepository;

    public function __construct(MissileRepository $missileRepository, PlanetRepository $planetRepository)
    {
        $this->missileRepository = $missileRepository;
        $this->planetRepository = $planetRepository;
    }

    public function collect(RewardInterface $reward, QuestInterface $quest): void
    {
        if ($reward instanceof Reward) {
            $data = $reward->getData();

            $mainPlanetId = $this->planetRepository->getUserMainId($quest->getUser());
            $this->missileRepository->addMissile($data['missile_id'], $data['value'], $quest->getUser(), $mainPlanetId);
        }
    }

    public function getType(): string
    {
        return self::TYPE;
    }
}
