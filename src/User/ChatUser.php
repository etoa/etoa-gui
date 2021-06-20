<?php declare(strict_types=1);

namespace EtoA\User;

class ChatUser implements UserInterface
{
    private int $id;
    private string $nick;

    public function __construct(int $id, string $nick)
    {
        $this->id = $id;
        $this->nick = $nick;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNick(): string
    {
        return $this->nick;
    }
}
