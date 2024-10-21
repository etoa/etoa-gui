<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\BuddyList\BuddyListRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuddyListRepository::class)]
#[ORM\Table(name: 'buddylist')]
class Buddy
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "bl_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "bl_user_id", type: "integer")]
    private int $userId;

    #[ORM\Column(name: "bl_buddy_id", type: "integer")]
    private int $buddyId;

    #[ORM\Column(name: "bl_comment", type: "string")]
    private ?string $comment;


    #[ORM\Column(name: "bl_allow", type: "boolean")]
    private bool $allowed;

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

    public function getBuddyId(): ?int
    {
        return $this->buddyId;
    }

    public function setBuddyId(int $buddyId): static
    {
        $this->buddyId = $buddyId;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function isAllowed(): ?bool
    {
        return $this->allowed;
    }

    public function setAllowed(bool $allowed): static
    {
        $this->allowed = $allowed;

        return $this;
    }
}
