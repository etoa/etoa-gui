<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Alliance\AllianceRepository;
use EtoA\Building\BuildingBuildType;
use EtoA\Building\BuildingDataRepository;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Defense\DefenseBuildType;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Form\Type\Admin\LogGameType;
use EtoA\Log\GameLog;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\GameLogSearch;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipBuildType;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyBuildType;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('log_game_search')]
class LogGameSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var string[] */
    public array $facilities = GameLogFacility::FACILITIES;
    /** @var string[] */
    public array $severities = LogSeverity::SEVERITIES;
    /** @var string[] */
    public array $users;
    /** @var string[] */
    public array $alliances;
    /** @var string[] */
    public array $entities;
    /** @var string[][] */
    public array $objects;
    /** @var string[][] */
    public array $status;

    public function __construct(
        private GameLogRepository $logRepository,
        private UserRepository $userRepository,
        private AllianceRepository $allianceRepository,
        private ShipDataRepository $shipDataRepository,
        private DefenseDataRepository $defenseDataRepository,
        private BuildingDataRepository $buildingDataRepository,
        private TechnologyDataRepository $technologyDataRepository,
        private EntityRepository $entityRepository
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = GameLogSearch::create();
        if ($this->getFormValues()['user'] !== '') {
            $search->userId((int) $this->getFormValues()['user']);
        }

        if ($this->getFormValues()['alliance'] !== '') {
            $search->allianceId((int) $this->getFormValues()['alliance']);
        }

        if ($this->getFormValues()['entity'] !== '') {
            $search->entityId((int) $this->getFormValues()['entity']);
        }

        if ($this->getFormValues()['facility'] !== '') {
            $search->facility((int) $this->getFormValues()['facility']);
        }

        if ($this->getFormValues()['query'] !== '') {
            $search->messageLike($this->getFormValues()['query']);
        }

        if ($this->getFormValues()['severity'] > LogSeverity::DEBUG) {
            $search->severity((int) $this->getFormValues()['severity']);
        }

        if (isset($this->getFormValues()['object']) && $this->getFormValues()['object'] > 0) {
            $search->objectId((int) $this->getFormValues()['object']);
        }

        $total = $this->logRepository->count($search);

        $limit = $this->getLimit($total);

        $logs = $this->logRepository->searchLogs($search, $this->perPage, $limit);

        if ($total > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
            $this->alliances = $this->allianceRepository->getAllianceNames();

            $this->objects = [
                GameLogFacility::BUILD => $this->buildingDataRepository->getBuildingNames(),
                GameLogFacility::TECH => $this->technologyDataRepository->getTechnologyNames(),
                GameLogFacility::SHIP => $this->shipDataRepository->searchShipNames(),
                GameLogFacility::DEF => $this->defenseDataRepository->searchDefenseNames(),
            ];

            $this->status = [
                GameLogFacility::BUILD => BuildingBuildType::all(),
                GameLogFacility::TECH => TechnologyBuildType::all(),
                GameLogFacility::SHIP => ShipBuildType::all(),
                GameLogFacility::DEF => DefenseBuildType::all(),
            ];

            $entities = $this->entityRepository->searchEntityLabels(EntitySearch::create()->ids(array_unique(array_map(fn (GameLog $log) => $log->entityId, $logs))));
            foreach ($entities as $label) {
                $this->entities[$label->id] = $label->toString();
            }
        }

        return new SearchResult($logs, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LogGameType::class, $this->getFormValues());
    }

    private function resetFormValues(): void
    {
        $this->formValues = [
            'facility' => '',
            'query' => '',
            'severity' => LogSeverity::DEBUG,
        ];
    }
}
