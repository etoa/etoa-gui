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
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "text_id", type: "string")]
    private string $id;

    #[ORM\Column(name: "text_content", type: 'string')]
    private string $content;

    #[ORM\Column(name: "text_updated", type: 'integer')]
    private int $updated = 0;

    #[ORM\Column(name: "text_enabled", type: 'boolean')]
    private bool $enabled = true;

    public function isEnabled(): bool
    {
        return $this->enabled && $this->content !== '';
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated($updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
}
