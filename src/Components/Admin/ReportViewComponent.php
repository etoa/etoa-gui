<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Message\Report;
use EtoA\Message\ReportAggregator;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportSearch;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('admin_report_view')]
class ReportViewComponent
{
    use DefaultActionTrait;

    #[LiveProp]
    public int $reportId;
    #[LiveProp]
    public string $userNick;
    #[LiveProp]
    public int $userId;
    #[LiveProp]
    public string $type = '';
    private ?Report $report = null;

    public function __construct(
        private ReportRepository $reportRepository,
        private ReportAggregator $reportAggregator,
    ) {
    }

    public function mount(Report $report = null): void
    {
        $this->report = $report;
        if ($this->report !== null) {
            $this->reportId = $report->id;
            $this->userId = $report->userId;
        }
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->reportRepository->setDeleted($this->reportId, true);
    }

    #[LiveAction]
    public function undelete(): void
    {
        $this->reportRepository->setDeleted($this->reportId, false);
    }

    public function getReport(): Report
    {
        if ($this->report === null) {
            $report = $this->reportRepository->searchReport(ReportSearch::create()->id($this->reportId));
            $this->report = $this->reportAggregator->aggregate([$report])[0];
        }

        return $this->report;
    }
}
