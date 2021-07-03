<?php

declare(strict_types=1);

namespace EtoA\Text;

class Text
{
    public string $id;
    public string $label;
    public string $description;
    public string $content;
    public int $updated;
    public bool $enabled = true;
    public bool $isOriginal = true;

    public function __construct(string $id, string $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    public function isEnabled(): bool
    {
        return $this->enabled && $this->content !== '';
    }
}
