<?php declare(strict_types=1);

namespace EtoA\User;

class ChatUser implements UserInterface
{
    /** @var int */
    private $id;
    /** @var string */
    private $nick;

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
