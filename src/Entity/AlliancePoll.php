<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AlliancePollRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlliancePollRepository::class)]
#[ORM\Table(name: 'alliance_polls')]
class AlliancePoll
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "poll_id")]
    private int $id;

    #[ORM\JoinColumn(name: 'poll_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class, inversedBy: 'polls')]
    private Alliance|null $alliance;

    #[ORM\Column(name: "poll_title")]
    private string $title;

    #[ORM\Column(name: "poll_question")]
    private string $question;

    #[ORM\Column(name: "poll_timestamp")]
    private int $timestamp;

    #[ORM\Column(name: "poll_a1_text")]
    private ?string $answer1;

    #[ORM\Column(name: "poll_a2_text")]
    private ?string $answer2;

    #[ORM\Column(name: "poll_a3_text")]
    private ?string $answer3 = '';

    #[ORM\Column(name: "poll_a4_text")]
    private ?string $answer4 = '';

    #[ORM\Column(name: "poll_a5_text")]
    private ?string $answer5 = '';

    #[ORM\Column(name: "poll_a6_text")]
    private ?string $answer6 = '';

    #[ORM\Column(name: "poll_a7_text")]
    private ?string $answer7 = '';

    #[ORM\Column(name: "poll_a8_text")]
    private ?string $answer8 = '';

    #[ORM\Column(name: "poll_a1_count")]
    private ?int $answer1Count = 0;

    #[ORM\Column(name: "poll_a2_count")]
    private ?int $answer2Count = 0;

    #[ORM\Column(name: "poll_a3_count")]
    private ?int $answer3Count = 0;

    #[ORM\Column(name: "poll_a4_count")]
    private ?int $answer4Count = 0;

    #[ORM\Column(name: "poll_a5_count")]
    private ?int $answer5Count = 0;

    #[ORM\Column(name: "poll_a6_count")]
    private ?int $answer6Count = 0;

    #[ORM\Column(name: "poll_a7_count")]
    private ?int $answer7Count = 0;

    #[ORM\Column(name: "poll_a8_count")]
    private ?int $answer8Count = 0;

    #[ORM\Column(name: "poll_active")]
    private ?bool $active = false;

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

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

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

    public function getAnswer1(): ?string
    {
        return $this->answer1;
    }

    public function setAnswer1(string $answer1): static
    {
        $this->answer1 = $answer1;

        return $this;
    }

    public function getAnswer2(): ?string
    {
        return $this->answer2;
    }

    public function setAnswer2(string $answer2): static
    {
        $this->answer2 = $answer2;

        return $this;
    }

    public function getAnswer3(): ?string
    {
        return $this->answer3;
    }

    public function setAnswer3(?string $answer3): static
    {
        $this->answer3 = $answer3;

        return $this;
    }

    public function getAnswer4(): ?string
    {
        return $this->answer4;
    }

    public function setAnswer4(?string $answer4): static
    {
        $this->answer4 = $answer4;

        return $this;
    }

    public function getAnswer5(): ?string
    {
        return $this->answer5;
    }

    public function setAnswer5(?string $answer5): static
    {
        $this->answer5 = $answer5;

        return $this;
    }

    public function getAnswer6(): ?string
    {
        return $this->answer6;
    }

    public function setAnswer6(?string $answer6): static
    {
        $this->answer6 = $answer6;

        return $this;
    }

    public function getAnswer7(): ?string
    {
        return $this->answer7;
    }

    public function setAnswer7(?string $answer7): static
    {
        $this->answer7 = $answer7;

        return $this;
    }

    public function getAnswer8(): ?string
    {
        return $this->answer8;
    }

    public function setAnswer8(?string $answer8): static
    {
        $this->answer8 = $answer8;

        return $this;
    }

    public function getAnswer1Count(): ?int
    {
        return $this->answer1Count;
    }

    public function setAnswer1Count(?int $answer1Count): static
    {
        $this->answer1Count = $answer1Count;

        return $this;
    }

    public function getAnswer2Count(): ?int
    {
        return $this->answer2Count;
    }

    public function setAnswer2Count(?int $answer2Count): static
    {
        $this->answer2Count = $answer2Count;

        return $this;
    }

    public function getAnswer3Count(): ?int
    {
        return $this->answer3Count;
    }

    public function setAnswer3Count(?int $answer3Count): static
    {
        $this->answer3Count = $answer3Count;

        return $this;
    }

    public function getAnswer4Count(): ?int
    {
        return $this->answer4Count;
    }

    public function setAnswer4Count(?int $answer4Count): static
    {
        $this->answer4Count = $answer4Count;

        return $this;
    }

    public function getAnswer5Count(): ?int
    {
        return $this->answer5Count;
    }

    public function setAnswer5Count(?int $answer5Count): static
    {
        $this->answer5Count = $answer5Count;

        return $this;
    }

    public function getAnswer6Count(): ?int
    {
        return $this->answer6Count;
    }

    public function setAnswer6Count(?int $answer6Count): static
    {
        $this->answer6Count = $answer6Count;

        return $this;
    }

    public function getAnswer7Count(): ?int
    {
        return $this->answer7Count;
    }

    public function setAnswer7Count(?int $answer7Count): static
    {
        $this->answer7Count = $answer7Count;

        return $this;
    }

    public function getAnswer8Count(): ?int
    {
        return $this->answer8Count;
    }

    public function setAnswer8Count(?int $answer8Count): static
    {
        $this->answer8Count = $answer8Count;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): static
    {
        $this->active = $active;

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
}
