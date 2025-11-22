<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Text\TextRepository;

#[ORM\Entity(repositoryClass: TextRepository::class)]
#[ORM\Table(name: 'texts')]
class Text
{
    #[ORM\Id]
    #[ORM\Column(name: "text_id", type: "string")]
    private string $id;

    #[ORM\Column(name: "text_content", type: 'string')]
    private string $content;

    #[ORM\Column(name: "text_updated", type: 'integer')]
    private int $updated = 0;

    #[ORM\Column(name: "text_enabled", type: 'boolean')]
    private bool $enabled = true;

    private string $label = '';

    private string $description = '';

    public function isEnabled(): bool
    {
        return $this->enabled && $this->content !== '';
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

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

    public function getUpdated(): int
    {
        return $this->updated;
    }

    public function setUpdated(int $updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


}
