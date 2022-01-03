<?php declare(strict_types=1);

namespace EtoA\Message\Report;

use EtoA\Core\Database\PropertyAssign;
use EtoA\Message\Report;
use EtoA\Message\ReportContext;
use EtoA\Message\ReportData\BattleReportData;

class BattleReport extends Report implements ReportInterface
{
    private const SUB_TYPES = [
        'antrax' => 'Antraxangriff',
        'antraxfailed' => 'Antraxangriff erfolglos',
        'bombard' => 'Gebäude bombardiert',
        'bombardfailed' => 'Bombardierung erfolglos',
        'emp' => 'Deaktivierung',
        'empfailed' => 'Deaktivierung erfolglos',
        'gasattack' => 'Giftgasangriff',
        'gasattackfailed' => 'Giftgasangriff erfolglos',
        'invasion' => 'Planet erfolgreich invasiert',
        'invasionfailed' => 'Invasionsversuch gescheitert',
        'invaded' => 'Kolonie wurde invasiert',
        'invadedfailed' => 'Invasionsversuch abgewehrt',
        'spyattack' => 'Spionageangriff',
        'spyattackfailed' => 'Spionageangriff erfolglos',
        'battle' => 'Kampfbericht',
        'battlefailed' => 'Kampfbericht (Abgebrochen)',
        'battleban' => 'Kampfbericht (Abgebrochen)',
        'alliancefailed' => 'Allianzteilflotte abgebrochen',
    ];

    public function __construct(
        Report $report,
        public BattleReportData $data,
        public ReportContext $context
    ) {
        PropertyAssign::assign($report, $this);
    }

    public function getSubject(): string
    {
        switch ($this->data->subtype) {
            case 'battle':
                $subject = "Kampfbericht (";
                switch ($this->data->result) {
                    case 1:
                        if (in_array($this->userId, $this->data->users, true)) {
                            $subject .= 'Gewonnen';
                        } else {
                            $subject .= 'Verloren';
                        }

                        break;
                    case 2:
                        if (in_array($this->userId, $this->data->users, true)) {
                            $subject .= 'Verloren';
                        } else {
                            $subject .= 'Gewonnen';
                        }

                        break;
                    default:
                        $subject .= 'Unentschieden';
                }

                return $subject . ') ' . $this->context->entities[$this->entity1Id]->toString();
            default:
                return self::SUB_TYPES[$this->data->subtype];
        }
    }
}
