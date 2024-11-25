<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Alliance\AllianceDiplomacyRepository;

#[ORM\Entity(repositoryClass: AllianceDiplomacyRepository::class)]
#[ORM\Table(name: 'alliance_bnd')]
class AllianceDiplomacy
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "alliance_bnd_id")]
    private int $id;

    #[ORM\Column(name: "alliance_bnd_name")]
    private string $name;

    #[ORM\JoinColumn(name: 'alliance_bnd_alliance_id1', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private Alliance|null $alliance1;

    #[ORM\JoinColumn(name: 'alliance_bnd_alliance_id2', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private Alliance|null $alliance2;

    #[ORM\Column(name: "alliance_bnd_level")]
    private int $level;

    #[ORM\Column(name: "alliance_bnd_text_pub")]
    private string $text;

    #[ORM\Column(name: "alliance_bnd_date")]
    private int $date;

    #[ORM\Column(name: "alliance_bnd_text")]
    private string $privateText;

    #[ORM\Column(name: "alliance_bnd_points")]
    private int $points;

    #[ORM\Column(name: "alliance_bnd_diplomat_id")]
    private int $diplomatId;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

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

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPrivateText(): ?string
    {
        return $this->privateText;
    }

    public function setPrivateText(string $privateText): static
    {
        $this->privateText = $privateText;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getDiplomatId(): ?int
    {
        return $this->diplomatId;
    }

    public function setDiplomatId(int $diplomatId): static
    {
        $this->diplomatId = $diplomatId;

        return $this;
    }

    public function getAlliance1(): ?Alliance
    {
        return $this->alliance1;
    }

    public function setAlliance1(?Alliance $alliance1): static
    {
        $this->alliance1 = $alliance1;

        return $this;
    }

    public function getAlliance2(): ?Alliance
    {
        return $this->alliance2;
    }

    public function setAlliance2(?Alliance $alliance2): static
    {
        $this->alliance2 = $alliance2;

        return $this;
    }
}
