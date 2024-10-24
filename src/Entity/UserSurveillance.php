<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\User\UserSurveillanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSurveillanceRepository::class)]
class UserSurveillance
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $timestamp;

    #[ORM\Column]
    private int $userId;

    #[ORM\Column]
    private string $page;

    #[ORM\Column]
    private string $request;

    #[ORM\Column]
    private string $requestRaw;

    #[ORM\Column]
    private string $post;

    #[ORM\Column]
    private string $session;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(string $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(string $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function getRequestRaw(): ?string
    {
        return $this->requestRaw;
    }

    public function setRequestRaw(string $requestRaw): static
    {
        $this->requestRaw = $requestRaw;

        return $this;
    }

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function setPost(string $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(string $session): static
    {
        $this->session = $session;

        return $this;
    }
}
