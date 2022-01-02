<?php declare(strict_types=1);

namespace EtoA\Message\Report;

use EtoA\Core\Database\PropertyAssign;
use EtoA\Message\Report;
use EtoA\Message\ReportContext;
use EtoA\Message\ReportData\SpyReportData;

class SpyReport extends Report implements ReportInterface
{
    private const SUB_TYPES = [
        'spy' => 'Spionagebericht',
        'surveillance' => 'Raumüberwachung',
        'spyfailed' => 'Spionage fehlgeschlagen',
        'surveillancefailed' => 'Raumüberwachung (verhindert)',
        'analyze' => 'Ziel analysiert',
        'analyzefailed' => 'Analyseversuch gescheitert',
    ];

    public function __construct(
        Report $report,
        public SpyReportData $data,
        public ReportContext $context
    ) {
        PropertyAssign::assign($report, $this);
    }

    public function getSubject(): string
    {
        switch ($this->data->subtype) {
            case 'spy':
                return 'Spionagebericht ' . $this->context->entities[$this->entity1Id]->toString();
            case 'spyfailed':
                return 'Spionage fehlgeschlagen auf ' . $this->context->entities[$this->entity1Id]->toString();
            default:
                return self::SUB_TYPES[$this->data->subtype];
        }
    }
}
