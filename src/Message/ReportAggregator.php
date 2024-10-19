<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Building\BuildingDataRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseSearch;
use EtoA\Entity\Report;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Message\Report\BattleReport;
use EtoA\Message\Report\ExploreReport;
use EtoA\Message\Report\MarketReport;
use EtoA\Message\Report\OtherReport;
use EtoA\Message\Report\ReportInterface;
use EtoA\Message\Report\SpyReport;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

class ReportAggregator
{
    public function __construct(
        private ReportRepository $reportRepository,
        private BuildingDataRepository $buildingDataRepository,
        private DefenseDataRepository $defenseDataRepository,
        private ShipDataRepository $shipDataRepository,
        private TechnologyDataRepository $technologyDataRepository,
        private EntityRepository $entityRepository,
        private FleetRepository $fleetRepository,
        private UserRepository $userRepository,
        private ConfigurationService $config,
    ) {
    }

    /**
     * @param list<Report> $reports
     * @return list<Report&ReportInterface>
     */
    public function aggregate(array $reports): array
    {
        $request = new ReportContextRequest();

        $typeMap = [];
        foreach ($reports as $report) {
            $typeMap[$report->getType()][$report->getId()] = $report->getTransformedDataFromContent();

            $request
                ->addEntityId($report->getEntity1Id())
                ->addEntityId($report->getEntity2Id())
                ->addUserId($report->getUserId())
                ->addUserId($report->getOpponentId());
        }

        $battleReports = $this->reportRepository->getBattleData(array_keys($typeMap[ReportTypes::TYPE_BATTLE] ?? []));
        foreach ($battleReports as $data) {
            $request
                ->addFleetId($data->fleetId)
                ->addShipIds(array_keys($data->ships))
                ->addShipIds(array_keys($data->entityShips))
                ->addDefenseIds(array_keys($data->entityDefense))
                ->addUserIds($data->users)
                ->addUserIds($data->entityUsers)
            ;

            switch ($data->subtype) {
                case 'emp':
                case 'bombard':
                    $request->addBuildingIds([$typeMap[ReportTypes::TYPE_BATTLE][$data->id][0]]);

                    break;
                case 'spyattack':
                    $request->addTechnologyIds([$typeMap[ReportTypes::TYPE_BATTLE][$data->id][0]]);

                    break;
            }
        }

        $marketReports = $this->reportRepository->getMarketData(array_keys($typeMap[ReportTypes::TYPE_MARKET] ?? []));
        foreach ($marketReports as $data) {
            $request
                ->addFleetId($data->fleetId1)
                ->addFleetId($data->fleetId2)
                ->addShipIds([$data->shipId])
            ;
        }

        $otherReports = $this->reportRepository->getOtherData(array_keys($typeMap[ReportTypes::TYPE_OTHER] ?? []));
        foreach ($otherReports as $data) {
            $request
                ->addFleetId($data->fleetId)
                ->addShipIds(array_keys($data->ships));
        }

        $spyReports = $this->reportRepository->getSpyData(array_keys($typeMap[ReportTypes::TYPE_SPY] ?? []));
        foreach ($spyReports as $data) {
            $request
                ->addFleetId($data->fleetId)
                ->addShipIds(array_keys($data->ships))
                ->addDefenseIds(array_keys($data->defense))
                ->addBuildingIds(array_keys($data->buildings))
                ->addTechnologyIds(array_keys($data->technologies))
            ;
        }

        $context = new ReportContext(
            $this->config,
            count($request->getUserIds()) > 0 ? $this->userRepository->searchUserNicknames(UserSearch::create()->ids($request->getUserIds())) : [],
            count($request->getShipIds()) > 0 ? $this->shipDataRepository->searchShipNames(ShipSearch::create()->ids($request->getShipIds())) : [],
            count($request->getBuildingIds()) > 0 ? $this->buildingDataRepository->getBuildingNames(true) : [],
            count($request->getTechnologyIds()) > 0 ? $this->technologyDataRepository->getTechnologyNames(true) : [],
            count($request->getDefenseIds()) > 0 ? $this->defenseDataRepository->searchDefenseNames(DefenseSearch::create()->ids($request->getDefenseIds())) : [],
            count($request->getFleetIds()) > 0 ? $this->fleetRepository->search(FleetSearch::create()->ids($request->getFleetIds())) : [],
            count($request->getEntityIds()) > 0 ? $this->entityRepository->searchEntityLabels(EntityLabelSearch::create()->ids($request->getEntityIds())) : []
        );

        $fullReports = [];
        foreach ($reports as $report) {
            switch ($report->getType()) {
                case ReportTypes::TYPE_BATTLE:
                    $fullReports[] = new BattleReport($report, $battleReports[$report->getId()], $context);

                    break;
                case ReportTypes::TYPE_EXPLORE:
                    $fullReports[] = new ExploreReport($report, $context);

                    break;
                case ReportTypes::TYPE_MARKET:
                    $fullReports[] = new MarketReport($report, $marketReports[$report->getId()], $context);

                    break;
                case ReportTypes::TYPE_OTHER:
                    $fullReports[] = new OtherReport($report, $otherReports[$report->getId()], $context);

                    break;
                case ReportTypes::TYPE_SPY:
                    $fullReports[] = new SpyReport($report, $spyReports[$report->getId()], $context);

                    break;
            }
        }

        return $fullReports;
    }
}
