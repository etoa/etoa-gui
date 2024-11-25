<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Alliance\AllianceRankRepository;

#[ORM\Entity(repositoryClass: AllianceRankRepository::class)]
#[ORM\Table(name: 'alliance_ranks')]
class AllianceRank
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "rank_id")]
    private int $id;

    #[ORM\JoinColumn(name: 'rank_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    protected Alliance|null $alliance;

    #[ORM\Column(name: "rank_level")]
    private int $level;

    #[ORM\Column(name: "rank_name")]
    private ?string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
