<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\LogGeneralSearchRequest;
use EtoA\Form\Type\Admin\LogGeneralType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSearch;
use EtoA\Log\LogSeverity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('log_general_search')]
class LogGeneralSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var string[] */
    public array $facilities = LogFacility::FACILITIES;
    /** @var string[] */
    public array $severities = LogSeverity::SEVERITIES;
    private LogGeneralSearchRequest $request;

    public function __construct(
        private LogRepository $logRepository
    ) {
        $this->request = new LogGeneralSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = LogSearch::create();
        if ($this->request->facility !== null) {
            $search->facility($this->request->facility);
        }

        if ($this->request->query !== null) {
            $search->messageLike($this->request->query);
        }

        if ($this->request->severity > LogSeverity::DEBUG) {
            $search->severity($this->request->severity);
        }

        $total = $this->logRepository->count($search);

        $limit = $this->getLimit($total);

        $logs = $this->logRepository->searchLogs($search, $this->perPage, $limit);

        return new SearchResult($logs, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LogGeneralType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new LogGeneralSearchRequest();
    }
}
