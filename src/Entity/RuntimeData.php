<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Repository\RuntimeDataRepository;
use EtoA\Support\RuntimeDataStore;

#[ORM\Entity(repositoryClass: RuntimeDataStore::class)]
class RuntimeData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $dataKey = null;

    #[ORM\Column(length: 255)]
    private ?string $dataValue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataKey(): ?string
    {
        return $this->dataKey;
    }

    public function setDataKey(string $dataKey): static
    {
        $this->dataKey = $dataKey;

        return $this;
    }

    public function getDataValue(): ?string
    {
        return $this->dataValue;
    }

    public function setDataValue(string $dataValue): static
    {
        $this->dataValue = $dataValue;

        return $this;
    }
}
