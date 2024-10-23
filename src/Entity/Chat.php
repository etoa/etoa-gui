<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Chat\ChatLogRepository;
use EtoA\Chat\ChatRepository;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    #[ORM\Column]
    private string $nick;

    #[ORM\Column]
    private string $text;

    #[ORM\Column]
    private string $color;

    #[ORM\Column(type: "integer")]
    private int $userId;

    #[ORM\Column(type: "integer")]
    private int $admin;

    #[ORM\Column(type:'integer')]
    private int $channelId;
}
