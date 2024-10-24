<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\User\UserLoginFailureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserLoginFailureRepository::class)]
#[ORM\Table(name: 'login_failures')]
class UserLoginFailure
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "failure_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "failure_time", type: "integer")]
    private int $time;

    #[ORM\Column(name: "failure_ip")]
    private string $ip;

    #[ORM\Column(name: "failure_host")]
    private ?string $host;

    #[ORM\Column(name: "failure_user_id")]
    private int $userId;

    #[ORM\Column(name: "failure_user_nick")]
    private ?string $userNick;

    #[ORM\Column(name: "failure_client")]
    private string $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserNick(): ?string
    {
        return $this->userNick;
    }

    public function setUserNick(string $userNick): static
    {
        $this->userNick = $userNick;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): static
    {
        $this->client = $client;

        return $this;
    }
}
