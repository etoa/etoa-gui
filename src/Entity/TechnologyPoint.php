<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Technology\TechnologyPointRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnologyPointRepository::class)]
#[ORM\Table(name: 'tech_points')]
class TechnologyPoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "bp_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "bp_tech_id", type: "integer")]
    private int $technologyId;

    #[ORM\Column(name: "bp_level", type: "integer")]
    private int $level;

    #[ORM\Column(name: "bp_points", type: "integer")]
    private float $points;
}
