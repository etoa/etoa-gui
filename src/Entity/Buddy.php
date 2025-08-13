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

    #[ORM\JoinColumn(name: 'bl_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy:'buddyList')]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'bl_buddy_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $buddy = null;

    #[ORM\Column(name: "bl_comment", type: "string")]
    private ?string $comment;

    #[ORM\Column(name: "bl_allow", type: "boolean")]
    private bool $allowed;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBuddy(): ?User
    {
        return $this->buddy;
    }

    public function setBuddy(?User $buddy): static
    {
        $this->buddy = $buddy;

        return $this;
    }
}
