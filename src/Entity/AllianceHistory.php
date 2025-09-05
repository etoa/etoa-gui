<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AllianceHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceHistoryRepository::class)]
class AllianceHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "history_id")]
    private int $id;

    #[ORM\JoinColumn(name: 'history_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class, inversedBy: 'history')]
    private Alliance|null $alliance;

    #[ORM\Column(name: "history_timestamp")]
    private int $timestamp;

    #[ORM\Column(name: "history_text")]
    private string $text;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

        return $this;
    }
}
