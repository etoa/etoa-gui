<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\User\UserOnlineStatsRepository;

#[ORM\Entity(repositoryClass: UserOnlineStatsRepository::class)]
#[ORM\Table(name: 'user_onlinestats')]
class UserOnlineStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "stats_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "stats_timestamp", type: "integer")]
    private int $timestamp;

    #[ORM\Column(name: "stats_count", type: "integer")]
    private int $sessionCount;

    #[ORM\Column(name: "stats_regcount", type: "integer")]
    private int $userCount;

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

    public function getSessionCount(): ?int
    {
        return $this->sessionCount;
    }

    public function setSessionCount(int $sessionCount): static
    {
        $this->sessionCount = $sessionCount;

        return $this;
    }

    public function getUserCount(): ?int
    {
        return $this->userCount;
    }

    public function setUserCount(int $userCount): static
    {
        $this->userCount = $userCount;

        return $this;
    }
}
