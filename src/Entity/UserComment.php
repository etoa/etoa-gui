<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\User\UserCommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCommentRepository::class)]
#[ORM\Table(name: 'user_comments')]
class UserComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "comment_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "comment_text")]
    private string $text;

    #[ORM\Column(name: "comment_timestamp")]
    private int $timestamp;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy:'comments')]
    #[ORM\JoinColumn(name: 'comment_user_id', referencedColumnName: 'user_id')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    #[ORM\JoinColumn(name: 'comment_admin_id', referencedColumnName: 'user_id')]
    private AdminUser $admin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

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

    public function getAdmin(): ?AdminUser
    {
        return $this->admin;
    }

    public function setAdmin(?AdminUser $admin): static
    {
        $this->admin = $admin;

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
