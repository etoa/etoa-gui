<?php declare(strict_types=1);

namespace EtoA\User;

class ChatUser implements UserInterface
{
    /** @var int */
    private $id;
    /** @var string */
    private $nick;

    public function __construct($id, $nick)
    {
        $this->id = $id;
        $this->nick = $nick;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNick()
    {
        return $this->nick;
    }
}
