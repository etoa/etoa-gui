<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Entity\Report;
use EtoA\Message\ReportRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('admin_report_view')]
class ReportViewComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Report $report;

    public function __construct(
        private readonly ReportRepository $reportRepository,
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->report->setDeleted(true);
        $this->reportRepository->save();
    }

    #[LiveAction]
    public function undelete(): void
    {
        $this->report->setDeleted(false);
        $this->reportRepository->save();
    }
}
