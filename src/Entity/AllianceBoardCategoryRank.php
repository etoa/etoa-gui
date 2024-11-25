<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Repository\AllianceBoardCategoryRankRepository;

#[ORM\Entity(repositoryClass: AllianceBoardCategoryRankRepository::class)]
#[ORM\Table(name: 'allianceboard_catranks')]
class AllianceBoardCategoryRank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "cr_id")]
    private ?int $id = null;

    #[ORM\Column(name: "cr_rank_id")]
    private ?int $rankId = null;

    #[ORM\Column(name: "cr_cat_id")]
    private ?int $catId = null;

    #[ORM\Column(name: "cr_bnd_id")]
    private ?int $bndId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRankId(): ?int
    {
        return $this->rankId;
    }

    public function setRankId(int $rankId): static
    {
        $this->rankId = $rankId;

        return $this;
    }

    public function getCatId(): ?int
    {
        return $this->catId;
    }

    public function setCatId(int $catId): static
    {
        $this->catId = $catId;

        return $this;
    }

    public function getBndId(): ?int
    {
        return $this->bndId;
    }

    public function setBndId(int $bndId): static
    {
        $this->bndId = $bndId;

        return $this;
    }
}
