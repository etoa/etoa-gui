<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;

class AllianceShipPointsService
{
    public function __construct(
        private readonly ConfigurationService $config,
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly UserRepository $userRepository)
    {}

    public function update(): int
    {
        $shipyards = $this->allianceBuildListRepository->getShipyardLevelsWhereNonNegativeResources();
        foreach ($shipyards as $shipyard) {
            // New exponential algorithm by river
            $shipPointsAdd = (int) ceil($this->config->getInt("alliance_shippoints_per_hour") * $this->config->getFloat('alliance_shippoints_base') ** ($shipyard->getLevel() - 1));

            $this->userRepository->addAllianceShipPoints($shipyard->getAlliance(), $shipPointsAdd);
        }

        return count($shipyards);
    }
}
