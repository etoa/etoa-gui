<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\Race;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserCreateRequest implements PasswordAuthenticatedUserInterface
{
    private string $name = '';
    private string $email = '';
    private string $nick = '';
    private string $password = '';
    private ?Race $race = null;
    private bool $ghost = false;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getNick(): string
    {
        return $this->nick;
    }

    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): void
    {
        $this->race = $race;
    }

    public function isGhost(): bool
    {
        return $this->ghost;
    }

    public function setGhost(bool $ghost): void
    {
        $this->ghost = $ghost;
    }


}
