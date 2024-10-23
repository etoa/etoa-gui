<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\HostCache\HostCacheRepository;
use EtoA\Repository\HostnameCacheRepository;

#[ORM\Entity(repositoryClass: HostCacheRepository::class)]
class HostnameCache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 39)]
    private ?string $addr = null;

    #[ORM\Column(length: 255)]
    private ?string $host = null;

    #[ORM\Column]
    private ?int $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddr(): ?string
    {
        return $this->addr;
    }

    public function setAddr(string $addr): static
    {
        $this->addr = $addr;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): static
    {
        $this->host = $host;

        return $this;
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
}
