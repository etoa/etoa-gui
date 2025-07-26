<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Defense\DefenseCategoryRepository;

#[ORM\Entity(repositoryClass: DefenseCategoryRepository::class)]
#[ORM\Table(name: 'def_cat')]
class DefenseCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "cat_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "cat_name")]
    private string $name;

    #[ORM\Column(name: "cat_order", type: "integer")]
    private int $order;

    #[ORM\Column(name: "cat_color")]
    private string $color;
}
