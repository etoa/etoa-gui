<?php declare(strict_types=1);

namespace EtoA\Message\Report;

use EtoA\Entity\BattleReportData;
use EtoA\Entity\Report;

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
        private readonly Report $report,
        public BattleReportData $data
    ) {}

    public function getSubject(): string
    {
        switch ($this->data->getSubtype()) {
            case 'battle':
                $subject = "Kampfbericht (";
                switch ($this->data->getResult()) {
                    case 1:
                        if (in_array($this->getUser(), $this->data->getUsers(), true)) {
                            $subject .= 'Gewonnen';
                        } else {
                            $subject .= 'Verloren';
                        }

                        break;
                    case 2:
                        if (in_array($this->getUser()->getId(), $this->data->getUsers(), true)) {
                            $subject .= 'Verloren';
                        } else {
                            $subject .= 'Gewonnen';
                        }

                        break;
                    default:
                        $subject .= 'Unentschieden';
                }

                return $subject . ') ' . $this->report->getEntity1()->toString();
            default:
                return self::SUB_TYPES[$this->data->getSubtype()];
        }
    }

    public function getSubtype(): ?string
    {
        return $this->data->getSubtype();
    }
}
