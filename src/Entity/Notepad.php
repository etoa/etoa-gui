<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Notepad\NotepadRepository;

#[ORM\Entity(repositoryClass: NotepadRepository::class)]
class Notepad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy:'notes')]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $timestamp = null;

    #[ORM\OneToOne(mappedBy: "notepad", targetEntity: NotepadData::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private ?NotepadData $data = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getData(): ?NotepadData
    {
        return $this->data;
    }

    public function setData(?NotepadData $data): static
    {
        $this->data = $data;

        return $this;
    }
}
