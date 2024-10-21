<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Tutorial\TutorialManager;

#[ORM\Entity(repositoryClass: TutorialManager::class)]
#[ORM\Table(name: 'tutorial_texts')]
class TutorialText
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "text_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "text_tutorial_id", type: "integer")]
    private int $tutorialId;

    #[ORM\JoinColumn(name: 'text_tutorial_id', referencedColumnName: 'tutorial_id')]
    #[ORM\ManyToOne(targetEntity: Tutorial::class)]
    private Tutorial $tutorial;

    #[ORM\Column(name: "text_title", type: "string")]
    private string $title;

    #[ORM\Column(name: "text_content", type: "string")]
    private string $content;

    #[ORM\Column(name: "text_step", type: "integer")]
    private int $step = 0;

    public ?int $prev = null;

    public ?int $next = null;

    public static function createFromArray(array $data): TutorialText
    {
        $text = new TutorialText();
        $text->id = (int) $data['text_id'];
        $text->tutorialId = (int) $data['text_tutorial_id'];
        $text->title = $data['text_title'];
        $text->content = $data['text_content'];
        $text->step = (int) $data['text_step'];

        return $text;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(int $step): static
    {
        $this->step = $step;

        return $this;
    }
}
