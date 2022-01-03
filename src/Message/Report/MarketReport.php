<?php declare(strict_types=1);

namespace EtoA\Message\Report;

use EtoA\Core\Database\PropertyAssign;
use EtoA\Message\Report;
use EtoA\Message\ReportContext;
use EtoA\Message\ReportData\MarketReportData;

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

    public function __construct(
        Report $report,
        public MarketReportData $data,
        public ReportContext $context
    ) {
        PropertyAssign::assign($report, $this);
    }

    public function getSubject(): string
    {
        return self::SUB_TYPES[$this->data->subtype];
    }
}
