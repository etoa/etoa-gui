<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceBoardTopicRepository::class)]
#[ORM\Table(name: 'allianceboard_topics')]
class AllianceBoardTopic
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "topic_id")]
    private int $id;

    #[ORM\JoinColumn(name: 'topic_cat_id', referencedColumnName: 'cat_id')]
    #[ORM\ManyToOne(targetEntity: AllianceBoardCategory::class)]
    private AllianceBoardCategory|null $category;

    #[ORM\Column(name: "topic_bnd_id")]
    private int $bndId;

    #[ORM\Column(name: "topic_user_id")]
    private int $userId;

    #[ORM\Column(name: "topic_subject")]
    private string $subject;

    #[ORM\Column(name: "topic_count")]
    private int $count;

    #[ORM\Column(name: "topic_timestamp")]
    private int $timestamp;

    #[ORM\Column(name: "topic_top")]
    private bool $top;

    #[ORM\Column(name: "topic_closed")]
    private bool $closed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBndId(): ?int
    {
        return $this->bndId;
    }

    public function setBndId(int $bndId): static
    {
        $this->bndId = $bndId;

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

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
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

    public function isTop(): ?bool
    {
        return $this->top;
    }

    public function setTop(bool $top): static
    {
        $this->top = $top;

        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): static
    {
        $this->closed = $closed;

        return $this;
    }

    public function getCategory(): ?AllianceBoardCategory
    {
        return $this->category;
    }

    public function setCategory(?AllianceBoardCategory $category): static
    {
        $this->category = $category;

        return $this;
    }


}
