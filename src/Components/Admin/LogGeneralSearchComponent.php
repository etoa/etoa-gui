<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\LogGeneralType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSearch;
use EtoA\Log\LogSeverity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('log_general_search')]
class LogGeneralSearchComponent extends AbstractController
{
    private const PER_PAGE = 100;

    use ComponentWithFormTrait;
    use DefaultActionTrait;

    /** @var string[] */
    public array $facilities = LogFacility::FACILITIES;
    /** @var string[] */
    public array $severities = LogSeverity::SEVERITIES;

    #[LiveProp]
    public int $limit = 0;

    public function __construct(
        private LogRepository $logRepository
    ) {
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->limit = 0;
        $this->formValues = [
            'facility' => '',
            'query' => '',
            'severity' => LogSeverity::DEBUG,
        ];
        $this->instantiateForm();
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

        $limit = max(0, $this->limit);
        $limit = min($total, $limit);
        $limit -= $limit % self::PER_PAGE;

        $logs = $this->logRepository->searchLogs($search, self::PER_PAGE, $limit);

        return new SearchResult($logs, $limit, $total, self::PER_PAGE);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LogGeneralType::class, $this->getFormValues());
    }
}
