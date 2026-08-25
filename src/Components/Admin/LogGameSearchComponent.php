<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Building\BuildingBuildType;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Defense\DefenseBuildType;
use EtoA\Form\Request\Admin\LogGameSearchRequest;
use EtoA\Form\Type\Admin\LogGameType;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\GameLogSearch;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipBuildType;
use EtoA\Technology\TechnologyBuildType;
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
    /** @var string[][] */
    public array $status;
    private LogGameSearchRequest $request;

    public function __construct(
        private GameLogRepository $logRepository,
    ) {
        $this->request = new LogGameSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = GameLogSearch::create();
        if ($this->request->user) {
            $search->user($this->request->user);
        }

        if ($this->request->alliance) {
            $search->allianceId($this->request->alliance);
        }

        if ($this->request->entity) {
            $search->entity($this->request->entity);
        }

        if ($this->request->facility !== null) {
            $search->facility($this->request->facility);
        }

        if ($this->request->query !== null) {
            $search->messageLike($this->request->query);
        }

        if ($this->request->severity > LogSeverity::DEBUG) {
            $search->severity($this->request->severity);
        }

        if ($this->request->object > 0) {
            $search->objectId($this->request->object);
        }

        $total = $this->logRepository->countBySearch($search);

        $limit = $this->getLimit($total);

        $logs = $this->logRepository->searchLogs($search, $this->perPage, $limit);

        if ($total > 0) {
            // object names come straight off the log row via the entity getter
            $this->status = [
                GameLogFacility::BUILD => BuildingBuildType::all(),
                GameLogFacility::TECH => TechnologyBuildType::all(),
                GameLogFacility::SHIP => ShipBuildType::all(),
                GameLogFacility::DEF => DefenseBuildType::all(),
            ];
        }

        return new SearchResult($logs, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LogGameType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new LogGameSearchRequest();
    }
}
