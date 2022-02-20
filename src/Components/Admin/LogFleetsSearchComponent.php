<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\LogFleetType;
use EtoA\Log\FleetLogFacility;
use EtoA\Log\FleetLogRepository;
use EtoA\Log\FleetLogSearch;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipDataRepository;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('log_fleets_search')]
class LogFleetsSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var string[] */
    public array $facilities = FleetLogFacility::FACILITIES;
    /** @var string[] */
    public array $severities = LogSeverity::SEVERITIES;
    /** @var string[] */
    public array $fleetStatusCode;
    /** @var string[] */
    public array $fleetActions;
    /** @var string[]  */
    public array $users;
    /** @var EntityLabel[]  */
    public array $entities;
    /** @var string[]  */
    public array $shipNames;

    public function __construct(
        private FleetLogRepository $logRepository,
        private UserRepository $userRepository,
        private EntityRepository $entityRepository,
        private ShipDataRepository $shipDataRepository
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = FleetLogSearch::create();
        if ($this->getFormValues()['facility'] !== '') {
            $search->facility((int) $this->getFormValues()['facility']);
        }

        if ($this->getFormValues()['severity'] > LogSeverity::DEBUG) {
            $search->severity((int) $this->getFormValues()['severity']);
        }

        if ($this->getFormValues()['action'] != "") {
            $search->action($this->getFormValues()['action']);
        }

        if ($this->getFormValues()['status'] != "") {
            $search->status((int) $this->getFormValues()['status']);
        }

        if ($this->getFormValues()['fleetUser'] !== '') {
            $search->fleetUserId((int) $this->getFormValues()['fleetUser']);
        }

        if ($this->getFormValues()['entityUser'] !== '') {
            $search->entityUserId((int) $this->getFormValues()['entityUser']);
        }

        $total = $this->logRepository->count($search);

        $limit = $this->getLimit($total);

        $logs = $this->logRepository->searchLogs($search, $this->perPage, $limit);

        if ($total > 0) {
            $this->fleetStatusCode = \FleetAction::$statusCode;
            $this->fleetActions = \FleetAction::getAll();
            $this->shipNames = $this->shipDataRepository->searchShipNames();
            $this->users = $this->userRepository->searchUserNicknames();

            $entityIds = [];
            foreach ($logs as $log) {
                $entityIds[] = $log->entityFromId;
                $entityIds[] = $log->entityToId;
            }
            $entities = $this->entityRepository->searchEntityLabels(EntitySearch::create()->ids(array_unique($entityIds)));
            foreach ($entities as $label) {
                $this->entities[$label->id] = $label;
            }
        }

        return new SearchResult($logs, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LogFleetType::class, $this->getFormValues());
    }

    private function resetFormValues(): void
    {
        $this->formValues = [
            'facility' => '',
            'action' => '',
            'severity' => LogSeverity::DEBUG,
            'fleetUser' => '',
            'entityUser' => '',
        ];
    }
}
