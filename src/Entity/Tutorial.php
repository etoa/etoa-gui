<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Tutorial\TutorialRepository;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: TutorialRepository::class)]
class Tutorial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"tutorial_id", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name:"tutorial_title", length: 255)]
    private ?string $title = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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
}
