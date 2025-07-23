<?php declare(strict_types=1);

namespace EtoA\Message\Report;

use EtoA\Core\Database\PropertyAssign;
use EtoA\Entity\Report;
use EtoA\Message\ReportContext;

class ExploreReport extends Report implements ReportInterface
{
    public function __construct(
        Report $report,
    ) {
        PropertyAssign::assign($report, $this);
    }

    public function getSubject(): string
    {
        return 'Erkundung';
    }

    public function getSubtype(): ?string
    {
        return 'other';
    }
}
