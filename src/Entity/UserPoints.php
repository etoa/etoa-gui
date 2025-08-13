<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\User\UserPointsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPointsRepository::class)]
class UserPoints
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "point_id", type: "integer")]
    private int $id;

    #[ORM\JoinColumn(name: 'point_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy:'userPoints')]
    private User $user;

    #[ORM\Column(name: "point_timestamp", type: "integer")]
    private int $timestamp;

    #[ORM\Column(name: "point_points", type: "integer")]
    private int $points;

    #[ORM\Column(name: "point_ship_points", type: "integer")]
    private int $shipPoints;

    #[ORM\Column(name: "point_tech_points", type: "integer")]
    private int $techPoints;

    #[ORM\Column(name: "point_building_points", type: "integer")]
    private int $buildingPoints;

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

    public function getShipPoints(): ?int
    {
        return $this->shipPoints;
    }

    public function setShipPoints(int $shipPoints): static
    {
        $this->shipPoints = $shipPoints;

        return $this;
    }

    public function getTechPoints(): ?int
    {
        return $this->techPoints;
    }

    public function setTechPoints(int $techPoints): static
    {
        $this->techPoints = $techPoints;

        return $this;
    }

    public function getBuildingPoints(): ?int
    {
        return $this->buildingPoints;
    }

    public function setBuildingPoints(int $buildingPoints): static
    {
        $this->buildingPoints = $buildingPoints;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
