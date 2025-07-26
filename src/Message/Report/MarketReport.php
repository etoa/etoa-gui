<?php declare(strict_types=1);

namespace EtoA\Message\Report;

use EtoA\Entity\MarketReportData;
use EtoA\Entity\Report;
use EtoA\Message\ReportContext;

class MarketReport extends Report implements ReportInterface
{
    private const SUB_TYPES = [
        'resadd' => 'Rohstoffangebot eingestellt',
        'rescancel' => 'Rohstoffangebot zurückgezogen',
        'ressold' => 'Rohstoffe verkauft',
        'resbought' => 'Rohstoffe gekauft',
        'shipadd' => 'Schiffangebot eingestellt',
        'shipcancel' => 'Schiffangebot zurückgezogen',
        'shipsold' => 'Schiffe verkauft',
        'shipbought' => 'Schiffe gekauft',
        'auctionadd' => 'Auktion hinzugefügt',
        'auctioncancel' => 'Auktion abgebrochen',
        'auctionbid' => 'Gebot abgegeben',
        'auctionoverbid' => 'Überboten',
        'auctionwon' => 'Auktion gewonnen',
        'auctionfinished' => 'Auktion beendet',
    ];

    public readonly ReportContext $context;

    public function __construct(
        Report $report,
        public MarketReportData $data
    ) {
        $this->context = new ReportContext();
    }

    public function getSubject(): string
    {
        return self::SUB_TYPES[$this->data->getSubtype()];
    }

    public function getSubtype(): ?string
    {
        return $this->data->getSubtype();
    }
}
