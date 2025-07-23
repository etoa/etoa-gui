<?php declare(strict_types=1);

namespace EtoA\Message\Report;

use EtoA\Entity\Report;
use EtoA\Entity\SpyReportData;

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
        private readonly Report $report,
        public SpyReportData $data,
    ) {}

    public function getSubject(): string
    {
        switch ($this->data->getSubtype()) {
            case 'spy':
                return 'Spionagebericht ' . $this->report->getEntity1()->toString();
            case 'spyfailed':
                return 'Spionage fehlgeschlagen auf ' . $this->report->getEntity1()->toString();
            default:
                return self::SUB_TYPES[$this->data->getSubtype()];
        }
    }

    public function getSubtype(): ?string
    {
        return $this->data->getSubtype();
    }
}
