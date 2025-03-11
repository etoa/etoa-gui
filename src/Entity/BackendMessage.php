<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Backend\BackendMessageRepository;

#[ORM\Entity(repositoryClass: BackendMessageRepository::class)]
#[ORM\Table(name: 'backend_message_queue')]
class BackendMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $cmd = null;

    #[ORM\Column(length: 255)]
    private ?string $arg = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCmd(): ?string
    {
        return $this->cmd;
    }

    public function setCmd(string $cmd): static
    {
        $this->cmd = $cmd;

        return $this;
    }

    public function getArg(): ?string
    {
        return $this->arg;
    }

    public function setArg(string $arg): static
    {
        $this->arg = $arg;

        return $this;
    }
}
