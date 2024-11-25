<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AlliancePointsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlliancePointsRepository::class)]
#[ORM\Table(name: 'alliance_points')]
class AlliancePoints
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "point_id")]
    private int $id;
    #[ORM\JoinColumn(name: 'point_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private Alliance|null $alliance;

    #[ORM\Column(name: "point_timestamp")]
    private int $timestamp;

    #[ORM\Column(name: "point_points")]
    private int $points;

    #[ORM\Column(name: "point_avg")]
    private int $avg;

    #[ORM\Column(name: "point_cnt")]
    private int $count;

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

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getAvg(): ?int
    {
        return $this->avg;
    }

    public function setAvg(int $avg): static
    {
        $this->avg = $avg;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

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
