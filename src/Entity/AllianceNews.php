<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Alliance\AllianceNewsRepository;

#[ORM\Entity(repositoryClass: AllianceNewsRepository::class)]
class AllianceNews
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "alliance_news_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "alliance_news_title")]
    private string $title;

    #[ORM\Column(name: "alliance_news_text")]
    private string $text;

    #[ORM\Column(name: "alliance_news_date")]
    private int $date;

    #[ORM\Column(name: "alliance_news_alliance_id")]
    private int $authorAllianceId;

    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    #[ORM\JoinColumn(name: 'alliance_news_alliance_id', referencedColumnName: 'alliance_id')]
    private Alliance $alliance;

    #[ORM\Column(name: "alliance_news_user_id")]
    private ?int $authorUserId;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'alliance_news_user_id', referencedColumnName: 'user_id')]
    private User $author;

    #[ORM\Column(name: "alliance_news_alliance_to_id")]
    private ?int $toAllianceId;

    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    #[ORM\JoinColumn(name: 'alliance_news_alliance_to_id', referencedColumnName: 'alliance_id')]
    private Alliance $toAlliance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAuthorAllianceId(): ?int
    {
        return $this->authorAllianceId;
    }

    public function setAuthorAllianceId(int $authorAllianceId): static
    {
        $this->authorAllianceId = $authorAllianceId;

        return $this;
    }

    public function getAuthorUserId(): ?int
    {
        return $this->authorUserId;
    }

    public function setAuthorUserId(int $authorUserId): static
    {
        $this->authorUserId = $authorUserId;

        return $this;
    }

    public function getToAllianceId(): ?int
    {
        return $this->toAllianceId;
    }

    public function setToAllianceId(int $toAllianceId): static
    {
        $this->toAllianceId = $toAllianceId;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getToAlliance(): ?Alliance
    {
        return $this->toAlliance;
    }

    public function setToAlliance(?Alliance $toAlliance): static
    {
        $this->toAlliance = $toAlliance;

        return $this;
    }
}
