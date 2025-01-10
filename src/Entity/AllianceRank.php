<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EtoA\Alliance\AllianceRankRepository;

#[ORM\Entity(repositoryClass: AllianceRankRepository::class)]
#[ORM\Table(name: 'alliance_ranks')]
class AllianceRank
{

    public function __construct() {
        $this->rights = new ArrayCollection();
        $this->rankRights = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "rank_id")]
    private int $id;

    #[ORM\JoinColumn(name: 'rank_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private ?Alliance $alliance = null;

    #[ORM\Column(name: "rank_level")]
    private int $level = 0;

    #[ORM\Column(name: "rank_name")]
    private ?string $name = null;

    /**
     * Many Ranks have Many Rights.
     * @var Collection<int, AllianceRight>
     */
    #[ORM\JoinTable(name: 'alliance_rankrights')]
    #[ORM\JoinColumn(name: 'rr_rank_id', referencedColumnName: 'rank_id')]
    #[ORM\InverseJoinColumn(name: 'rr_right_id', referencedColumnName: 'right_id')]
    #[ORM\ManyToMany(targetEntity: AllianceRight::class)]
    private Collection $rights;

    #[ORM\OneToMany(mappedBy: 'rank', targetEntity: AllianceRankRight::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'rank_id', referencedColumnName: 'rr_rank_id')]
    private Collection $rankRights;

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

    /**
     * @return Collection<int, AllianceRight>
     */
    public function getRights(): Collection
    {
        return $this->rights;
    }

    public function addRight(AllianceRight $right): static
    {
        if (!$this->rights->contains($right)) {
            $this->rights->add($right);
        }

        return $this;
    }

    public function removeRight(AllianceRight $right): static
    {
        $this->rights->removeElement($right);

        return $this;
    }

    /**
     * @return Collection<int, AllianceRankRight>
     */
    public function getRankRights(): Collection
    {
        return $this->rankRights;
    }

    public function addRankRight(AllianceRankRight $rankRight): static
    {
        if (!$this->rankRights->contains($rankRight)) {
            $this->rankRights->add($rankRight);
            $rankRight->setRank($this);
        }

        return $this;
    }

    public function removeRankRight(AllianceRankRight $rankRight): static
    {
        if ($this->rankRights->removeElement($rankRight)) {
            // set the owning side to null (unless already changed)
            if ($rankRight->getRank() === $this) {
                $rankRight->setRank(null);
            }
        }

        return $this;
    }
}
