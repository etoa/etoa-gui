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

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'failure_user_id', referencedColumnName: 'user_id')]
    protected User $user;
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

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): static
    {
        $this->client = $client;

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
