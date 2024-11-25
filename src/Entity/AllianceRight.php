<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Alliance\AllianceRightRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceRightRepository::class)]
#[ORM\Table(name: 'alliance_rights')]
class AllianceRight
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "right_id")]
    private int $id;

    #[ORM\Column(name: "right_key")]
    private string $key;

    #[ORM\Column(name: "right_desc")]
    private string $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

}
