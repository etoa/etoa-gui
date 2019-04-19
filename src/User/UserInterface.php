<?php declare(strict_types=1);

namespace EtoA\User;

interface UserInterface
{
    public function getId(): int;

    public function getNick(): string;
}
