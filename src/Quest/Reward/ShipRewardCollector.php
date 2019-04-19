<?php declare(strict_types=1);

namespace EtoA\Quest\Reward;

use EtoA\Planet\PlanetRepository;
use EtoA\Ship\ShipRepository;
use LittleCubicleGames\Quests\Definition\Reward\Reward;
use LittleCubicleGames\Quests\Definition\Reward\RewardInterface;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Reward\Collect\CollectorInterface;

class ShipRewardCollector implements CollectorInterface
{
    public const TYPE = 'ship';

    /** @var ShipRepository */
    private $shipRepository;
    /** @var PlanetRepository */
    private $planetRepository;

    public function __construct(ShipRepository $shipRepository, PlanetRepository $planetRepository)
    {
        $this->shipRepository = $shipRepository;
        $this->planetRepository = $planetRepository;
    }

    public function collect(RewardInterface $reward, QuestInterface $quest): void
    {
        if ($reward instanceof Reward) {
            $data = $reward->getData();

            $mainPlanetId = $this->planetRepository->getUserMainId($quest->getUser());
            $this->shipRepository->addShip($data['ship_id'], $data['value'], $quest->getUser(), $mainPlanetId);
        }
    }

    public function getType(): string
    {
        return self::TYPE;
    }
}
