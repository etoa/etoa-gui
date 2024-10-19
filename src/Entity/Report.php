<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Message\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(type: "integer")]
    private int $userId;

    #[ORM\Column(type: "integer")]
    private int $allianceId;

    #[ORM\Column(type: "string")]
    private ?string $content;

    #[ORM\Column(type: "integer")]
    private int $entity1Id;

    #[ORM\Column(type: "integer")]
    private int $entity2Id;

    #[ORM\Column(name:"opponent1_id", type: "integer")]
    private int $opponentId;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAllianceId(): ?int
    {
        return $this->allianceId;
    }

    public function setAllianceId(int $allianceId): static
    {
        $this->allianceId = $allianceId;

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

    public function getEntity1Id(): ?int
    {
        return $this->entity1Id;
    }

    public function setEntity1Id(int $entity1Id): static
    {
        $this->entity1Id = $entity1Id;

        return $this;
    }

    public function getEntity2Id(): ?int
    {
        return $this->entity2Id;
    }

    public function setEntity2Id(int $entity2Id): static
    {
        $this->entity2Id = $entity2Id;

        return $this;
    }

    public function getOpponentId(): ?int
    {
        return $this->opponentId;
    }

    public function setOpponentId(int $opponentId): static
    {
        $this->opponentId = $opponentId;

        return $this;
    }
}
