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
        'analyzefaild' => 'Analyseversuch gescheitert',
    ];

    public function __construct(
        private readonly Report $report,
        public SpyReportData $data,
    ) {}

    public function getSubject(): string
    {
        return match ($this->data->getSubtype()) {
            'spy' => 'Spionagebericht ' . $this->report->getEntity1()->toString(),
            'spyfailed' => 'Spionage fehlgeschlagen auf ' . $this->report->getEntity1()->toString(),
            default => self::SUB_TYPES[$this->data->getSubtype()],
        };
    }

    public function getSubtype(): ?string
    {
        return $this->data->getSubtype();
    }
}
