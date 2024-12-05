<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\Board\AllianceBoardPostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceBoardPostRepository::class)]
#[ORM\Table(name: 'allianceboard_posts')]
class AllianceBoardPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "post_id")]
    private int $id;

    #[ORM\Column(name: "post_user_id")]
    private int $userId;

    #[ORM\Column(name: "post_user_nick")]
    private string $userNick;

    #[ORM\JoinColumn(name: 'post_topic_id', referencedColumnName: 'topic_id')]
    #[ORM\ManyToOne(targetEntity: AllianceBoardTopic::class, cascade:['persist'])]
    private AllianceBoardTopic|null $topic;

    #[ORM\Column(name: "post_text")]
    private string $text;

    #[ORM\Column(name: "post_timestamp")]
    private int $timestamp;

    #[ORM\Column(name: "post_changed")]
    private ?int $changed;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserNick(): ?string
    {
        return $this->userNick;
    }

    public function setUserNick(string $userNick): static
    {
        $this->userNick = $userNick;

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

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getChanged(): ?int
    {
        return $this->changed;
    }

    public function setChanged(int $changed): static
    {
        $this->changed = $changed;

        return $this;
    }

    public function getTopic(): ?AllianceBoardTopic
    {
        return $this->topic;
    }

    public function setTopic(?AllianceBoardTopic $topic): static
    {
        $this->topic = $topic;

        return $this;
    }
}
