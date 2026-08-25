<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Notepad\NotepadDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotepadDataRepository::class)]
class NotepadData
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'data', targetEntity: Notepad::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private ?Notepad $notepad = null;

    #[ORM\Column]
    private string $subject;

    #[ORM\Column]
    private string $text;

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

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

    public function getNotepad(): ?Notepad
    {
        return $this->notepad;
    }

    public function setNotepad(?Notepad $notepad): static
    {
        $this->notepad = $notepad;

        return $this;
    }
}
