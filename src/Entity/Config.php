<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Core\Configuration\ConfigurationRepository;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Config
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "config_id")]
    private ?int $id = null;

    #[ORM\Column(name: "config_name", length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: "config_value", length: 255, nullable: true)]
    private ?string $value = null;

    #[ORM\Column(name: "config_param1", length: 255, nullable: true)]
    private ?string $param1 = null;

    #[ORM\Column(name: "config_param2", length: 255, nullable: true)]
    private ?string $param2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getParam1(): ?string
    {
        return $this->param1;
    }

    public function setParam1(?string $param1): static
    {
        $this->param1 = $param1;

        return $this;
    }

    public function getParam2(): ?string
    {
        return $this->param2;
    }

    public function setParam2(?string $param2): static
    {
        $this->param2 = $param2;

        return $this;
    }
}
