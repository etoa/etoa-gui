<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\User\UserRatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRatingRepository::class)]
#[ORM\Table(name: 'user_ratings')]
class UserRating
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "integer")]
    private int $userId;

    #[ORM\Column(name: "point_user", type: "integer")]
    private string $userNick;

    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUserNick(): ?int
    {
        return $this->userNick;
    }

    public function setUserNick(int $userNick): static
    {
        $this->userNick = $userNick;

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
}
