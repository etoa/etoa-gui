<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Message\Report\ExploreReport;
use EtoA\Message\ReportRepository;
use EtoA\Message\Report\MarketReport;
use EtoA\Message\Report\SpyReport;
use EtoA\Message\Report\OtherReport;
use EtoA\Message\Report\BattleReport;



#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ORM\Table(name: 'reports')]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    #[ORM\Column(type: "string")]
    private string $type;

    #[ORM\Column(type: "boolean")]
    private bool $read;

    #[ORM\Column(type: "boolean")]
    private bool $deleted;

    #[ORM\Column(type: "boolean")]
    private bool $archived;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private ?Alliance $alliance = null;

    #[ORM\Column(type: "string")]
    private ?string $content;

    #[ORM\JoinColumn(name: 'entity1_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    private ?Entity $entity1 = null;

    #[ORM\JoinColumn(name: 'entity2_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    private ?Entity $entity2 = null;

    #[ORM\JoinColumn(name: 'opponent1_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $opponent1 = null;

    #[ORM\OneToOne(inversedBy: 'id', targetEntity: SpyReportData::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private SpyReportData $spyReportData;

    #[ORM\OneToOne(inversedBy: 'id', targetEntity: MarketReportData::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private MarketReportData $marketReportData;

    #[ORM\OneToOne(inversedBy: 'id', targetEntity: BattleReportData::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private BattleReportData $battleReportData;

    #[ORM\OneToOne(inversedBy: 'id', targetEntity: OtherReportData::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private OtherReportData $otherReportData;

    public static function createFromArray(array $data): Report
    {
        $report = new Report();
        $report->id = (int) $data['id'];
        $report->timestamp = (int) $data['timestamp'];
        $report->type = $data['type'];
        $report->read = (bool) $data['read'];
        $report->deleted = (bool) $data['deleted'];
        $report->archived = (bool) $data['archived'];
        $report->userId = (int) $data['user_id'];
        $report->allianceId = (int) $data['alliance_id'];
        $report->content = $data['content'];
        $report->entity1Id = (int) $data['entity1_id'];
        $report->entity2Id = (int) $data['entity2_id'];
        $report->opponentId = (int) $data['opponent1_id'];

        return $report;
    }

    /**
     * @return int[]
     */
    public function getTransformedDataFromContent(): array
    {
        if ($this->content !== null) {
            return array_map(fn (string $value) => (int) $value, explode(':', $this->content));
        }

        return [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->read;
    }

    public function setRead(bool $read): static
    {
        $this->read = $read;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): static
    {
        $this->archived = $archived;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

        return $this;
    }

    public function getEntity1(): ?Entity
    {
        return $this->entity1;
    }

    public function setEntity1(?Entity $entity1): static
    {
        $this->entity1 = $entity1;

        return $this;
    }

    public function getEntity2(): ?Entity
    {
        return $this->entity2;
    }

    public function setEntity2(?Entity $entity2): static
    {
        $this->entity2 = $entity2;

        return $this;
    }

    public function getOpponent1(): ?User
    {
        return $this->opponent1;
    }

    public function setOpponent1(?User $opponent1): static
    {
        $this->opponent1 = $opponent1;

        return $this;
    }

    public function getSpyReportData(): ?SpyReportData
    {
        return $this->spyReportData;
    }

    public function setSpyReportData(?SpyReportData $spyReportData): static
    {
        $this->spyReportData = $spyReportData;

        return $this;
    }

    public function getMarketReportData(): ?MarketReportData
    {
        return $this->marketReportData;
    }

    public function setMarketReportData(?MarketReportData $marketReportData): static
    {
        $this->marketReportData = $marketReportData;

        return $this;
    }

    /**
     * Factory design pattern for getting instances
     *
     * @return OtherReport|MarketReport|ExploreReport|BattleReport|SpyReport New report object instance
     */
    public function createFactory(): OtherReport|MarketReport|ExploreReport|BattleReport|SpyReport
    {
        return match ($this->type) {
            'market' => new MarketReport($this, $this->marketReportData),
            'explore' => new ExploreReport($this),
            'spy' => new SpyReport($this, $this->spyReportData),
            'battle' => new BattleReport($this, $this->battleReportData),
            default => new OtherReport($this, $this->otherReportData),
        };
    }

    public function getBattleReportData(): ?BattleReportData
    {
        return $this->battleReportData;
    }

    public function setBattleReportData(?BattleReportData $battleReportData): static
    {
        $this->battleReportData = $battleReportData;

        return $this;
    }

    public function getOtherReportData(): ?OtherReportData
    {
        return $this->otherReportData;
    }

    public function setOtherReportData(?OtherReportData $otherReportData): static
    {
        $this->otherReportData = $otherReportData;

        return $this;
    }
}
