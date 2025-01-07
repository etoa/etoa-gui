<?php

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use EtoA\Message\MessageDataRepository;

#[ORM\Entity(repositoryClass: MessageDataRepository::class)]
class MessageData
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $text;

    #[ORM\JoinColumn(name: 'entity_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    private ?Entity $entity = null;

    #[ORM\JoinColumn(name: 'fleet_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Fleet::class)]
    private ?Fleet $fleet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

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

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    public function getFleet(): ?Fleet
    {
        return $this->fleet;
    }

    public function setFleet(?Fleet $fleet): static
    {
        $this->fleet = $fleet;

        return $this;
    }
}
