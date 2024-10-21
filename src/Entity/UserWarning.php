<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Admin\AdminUser;
use EtoA\User\UserWarningRepository;

#[ORM\Entity(repositoryClass: UserWarningRepository::class)]
#[ORM\Table(name: 'user_warnings')]
class UserWarning
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "warning_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "warning_user_id", type: "integer")]
    private int $userId;

    #[ORM\JoinColumn(name: 'warning_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(name: "warning_date", type: "integer")]
    private int $date;

    #[ORM\Column(name: "warning_text", type: "string")]
    private string $text;

    #[ORM\Column(name: "warning_admin_id", type: "integer")]
    private int $adminId;

    #[ORM\JoinColumn(name: 'warning_admin_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    private ?AdminUser $admin;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): static
    {
        $this->date = $date;

        return $this;
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

    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    public function setAdminId(int $adminId): static
    {
        $this->adminId = $adminId;

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

    public function getAdmin(): ?AdminUser
    {
        return $this->admin;
    }

    public function setAdmin(?AdminUser $admin): static
    {
        $this->admin = $admin;

        return $this;
    }
}
