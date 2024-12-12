<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Alliance\AllianceRankRightRepository;

#[ORM\Entity(repositoryClass: AllianceRankRightRepository::class)]
class AllianceRankRight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'rr_rank_id', referencedColumnName: 'rank_id')]
    #[ORM\ManyToOne(targetEntity: AllianceRank::class)]
    protected ?AllianceRank $rank;

    #[ORM\JoinColumn(name: 'rr_right_id', referencedColumnName: 'right_id')]
    #[ORM\ManyToOne(targetEntity: AllianceRight::class)]
    protected ?AllianceRight $right;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRank(): ?AllianceRank
    {
        return $this->rank;
    }

    public function setRank(?AllianceRank $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function getRight(): ?AllianceRight
    {
        return $this->right;
    }

    public function setRight(?AllianceRight $right): static
    {
        $this->right = $right;

        return $this;
    }
}
