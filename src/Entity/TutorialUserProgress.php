<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Tutorial\TutorialUserProgressRepository;

#[ORM\Entity(repositoryClass: TutorialUserProgressRepository::class)]
class TutorialUserProgress
{
    #[ORM\Id]
    #[ORM\Column(name:"tup_user_id")]
    private ?int $userId = null;

    #[ORM\JoinColumn(name: 'tup_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'tup_tutorial_id', referencedColumnName: 'tutorial_id')]
    #[ORM\ManyToOne(targetEntity: Tutorial::class)]
    private ?Tutorial $tutorial = null;

    #[ORM\Id]
    #[ORM\Column(name:"tup_tutorial_id")]
    private ?int $tutorialId = null;

    #[ORM\Column(name:"tup_text_step")]
    private ?int $textStep = null;

    #[ORM\Column(name:"tup_closed")]
    private ?bool $closed = false;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTutorialId(): ?int
    {
        return $this->tutorialId;
    }

    public function setTutorialId(int $tutorialId): static
    {
        $this->tutorialId = $tutorialId;

        return $this;
    }

    public function getTextStep(): ?int
    {
        return $this->textStep;
    }

    public function setTextStep(int $textStep): static
    {
        $this->textStep = $textStep;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTutorial(): ?Tutorial
    {
        return $this->tutorial;
    }

    public function setTutorial(?Tutorial $tutorial): static
    {
        $this->tutorial = $tutorial;

        return $this;
    }
}
