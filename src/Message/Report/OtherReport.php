<?php declare(strict_types=1);

namespace EtoA\Message\Report;

use EtoA\Core\Database\PropertyAssign;
use EtoA\Entity\OtherReportData;
use EtoA\Entity\Report;

class OtherReport extends Report implements ReportInterface
{
    private const SUB_TYPES = [
        'return' => 'Flotte angekommen',
        'collectmetal' => 'Asteroiden gesammelt',
        'collectmetalfailed' => 'Asteroidensammeln gescheitert',
        'delivery' => 'Flotte von der Allianzbasis',
        'colonize' => 'Planet kolonialisiert',
        'colonizefailed' => 'Landung nicht möglich',
        'createdebris' => 'Trümmerfeld erstellt',
        'collectfuel' => 'Gas gesaugt',
        'collectfuelfailed' => 'Gassaugen gescheitert',
        'market' => 'Flotte vom Handelsministerium',
        'collectcrystal' => 'Nebelfeld gesammelt',
        'collectcrystalfailed' => 'Nebelfeldensammeln gescheitert',
        'supportreturn' => 'Supportflotte Rückflug',
        'support' => 'Supportflotte angekommen',
        'supportfailed' => 'Supportflug fehlgeschlagen"',
        'supportoverflow' => 'Support nicht möglich',
        'transport' => 'Transport angekommen',
        'collectdebris' => 'Trümmer gesammelt',
        'collectdebrisfailed' => 'Trümmersammeln gescheitert',
        'fetch' => 'Warenabholung',
        'fetchfailed' => 'Warenabholung gescheitert',
        'actionmain' => 'Flotte umgelenkt',
        'actionshot' => 'Flotte abgeschossen',
        'actionfailed' => 'Aktion gescheitert',
    ];

    public function __construct(
        Report $report,
        public OtherReportData $data
    ) {}

    public function getSubject(): string
    {
        return self::SUB_TYPES[$this->data->getSubtype()];
    }

    public function getSubtype(): ?string
    {
        return $this->data->getSubtype();
    }
}
