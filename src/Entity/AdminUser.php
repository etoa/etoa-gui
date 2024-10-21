<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Admin\AdminUserRepository;

#[ORM\Entity(repositoryClass: AdminUserRepository::class)]
#[ORM\Table(name: 'admin_users')]
class AdminUser
{
    public const CONTACT_REQUIRED_EMAIL_SUFFIX = "@etoa.ch";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "user_id", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "user_password", type: "string")]
    private ?string $passwordString;

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

    #[ORM\Column(type: "integer")]
    private int $playerId = 0;

    #[ORM\Column(name: "user_board_url", type: "string")]
    private ?string $boardUrl = "";

    #[ORM\Column(type: "string")]
    private string $userTheme = "";

    #[ORM\Column(name: "ticketemail", type: "boolean")]
    private bool $ticketEmail = false;

    #[ORM\Column(name: "user_locked", type: "boolean")]
    private bool $locked = false;

    #[ORM\Column(type: "boolean")]
    private bool $isContact = true;

    /** @var string[] */
    #[ORM\Column(type: "json")]
    private array $roles = [];

    public static function createFromArray(array $data): AdminUser
    {
        $adminUser = new AdminUser();
        $adminUser->id = (int)$data['user_id'];
        $adminUser->passwordString = $data['user_password'];
        $adminUser->nick = $data['user_nick'];
        $adminUser->forcePasswordChange = (bool)$data['user_force_pwchange'];
        $adminUser->name = $data['user_name'];
        $adminUser->email = $data['user_email'];
        $adminUser->tfaSecret = $data['tfa_secret'];
        $adminUser->playerId = (int)$data['player_id'];
        $adminUser->boardUrl = $data['user_board_url'];
        $adminUser->userTheme = $data['user_theme'];
        $adminUser->ticketEmail = (bool)$data['ticketmail'];
        $adminUser->locked = (bool)$data['user_locked'];
        $adminUser->roles = blank($data['roles']) ? [] : explode(",", $data['roles']);
        $adminUser->isContact = (bool)$data['is_contact'];

        return $adminUser;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPasswordString(): ?string
    {
        return $this->passwordString;
    }

    public function setPasswordString(string $passwordString): static
    {
        $this->passwordString = $passwordString;

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

    public function getPlayerId(): ?int
    {
        return $this->playerId;
    }

    public function setPlayerId(int $playerId): static
    {
        $this->playerId = $playerId;

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
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
}
