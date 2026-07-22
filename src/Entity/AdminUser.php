<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Admin\AdminUserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: AdminUserRepository::class)]
#[ORM\Table(name: 'admin_users')]
class AdminUser implements PasswordAuthenticatedUserInterface
{
    public const CONTACT_REQUIRED_EMAIL_SUFFIX = "@etoa.ch";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "user_id", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "user_password", type: "string")]
    private ?string $password;

    #[ORM\Column(name: "user_force_pwchange", type: "boolean")]
    private bool $forcePasswordChange = false;

    #[ORM\Column(name: "user_nick", type: "string")]
    private string $nick = "";

    #[ORM\Column(name: "user_name", type: "string")]
    private string $name = "";

    #[ORM\Column(name: "user_email", type: "string")]
    private string $email = "";

    #[ORM\Column(type: "string")]
    private string $tfaSecret = "";

    #[ORM\JoinColumn(name: 'player_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    protected ?User $player = null;

    #[ORM\Column(name: "user_board_url", type: "string")]
    private ?string $boardUrl = "";

    #[ORM\Column(type: "string")]
    private string $userTheme = "";

    #[ORM\Column(name: "ticketmail", type: "boolean")]
    private bool $ticketEmail = false;

    #[ORM\Column(name: "user_locked", type: "boolean")]
    private bool $locked = false;

    #[ORM\Column(type: "boolean")]
    private bool $isContact = true;

    #[ORM\Column(type: "string")]
    private string $roles = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isForcePasswordChange(): ?bool
    {
        return $this->forcePasswordChange;
    }

    public function setForcePasswordChange(bool $forcePasswordChange): static
    {
        $this->forcePasswordChange = $forcePasswordChange;

        return $this;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(string $nick): static
    {
        $this->nick = $nick;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTfaSecret(): ?string
    {
        return $this->tfaSecret;
    }

    public function setTfaSecret(string $tfaSecret): static
    {
        $this->tfaSecret = $tfaSecret;

        return $this;
    }

    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(?User $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getBoardUrl(): ?string
    {
        return $this->boardUrl;
    }

    public function setBoardUrl(string $boardUrl): static
    {
        $this->boardUrl = $boardUrl;

        return $this;
    }

    public function getUserTheme(): ?string
    {
        return $this->userTheme;
    }

    public function setUserTheme(string $userTheme): static
    {
        $this->userTheme = $userTheme;

        return $this;
    }

    public function isTicketEmail(): ?bool
    {
        return $this->ticketEmail;
    }

    public function setTicketEmail(bool $ticketEmail): static
    {
        $this->ticketEmail = $ticketEmail;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): static
    {
        $this->locked = $locked;

        return $this;
    }

    public function isIsContact(): ?bool
    {
        return $this->isContact;
    }

    public function setIsContact(bool $isContact): static
    {
        $this->isContact = $isContact;

        return $this;
    }

    public function getRoles(): array
    {
        return blank($this->roles) ? [] : explode(",", $this->roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = implode(',',$roles);

        return $this;
    }

    public function isContact(): ?bool
    {
        return $this->isContact;
    }
}
