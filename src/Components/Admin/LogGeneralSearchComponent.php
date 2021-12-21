<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\LogGeneralType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSearch;
use EtoA\Log\LogSeverity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('log_general_search')]
class LogGeneralSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;

    /** @var string[] */
    public array $facilities = LogFacility::FACILITIES;
    /** @var string[] */
    public array $severities = LogSeverity::SEVERITIES;

    public function __construct(
        private LogRepository $logRepository
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = LogSearch::create();
        if ($this->getFormValues()['facility'] !== '') {
            $search->facility((int) $this->getFormValues()['facility']);
        }

        if ($this->getFormValues()['query'] !== '') {
            $search->messageLike($this->getFormValues()['query']);
        }

        if ($this->getFormValues()['severity'] > LogSeverity::DEBUG) {
            $search->severity((int) $this->getFormValues()['severity']);
        }

        $total = $this->logRepository->count($search);

        $limit = $this->getLimit($total);

        $logs = $this->logRepository->searchLogs($search, $this->perPage, $limit);

        return new SearchResult($logs, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LogGeneralType::class, $this->getFormValues());
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
