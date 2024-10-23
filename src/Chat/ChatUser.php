<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\User\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'chat_users')]
class ChatUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "user_id", type: "integer")]
    private int $id;

    #[ORM\Column]
    private string $nick;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    #[ORM\Column]
    private ?string $kick;
}
