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

    #[ORM\OneToOne(mappedBy: "userRating", targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'user_id')]
    private User $user;

    #[ORM\Column]
    private ?int $battlesFought;

    #[ORM\Column]
    private ?int $battlesWon;

    #[ORM\Column]
    private ?int $battlesLost;

    #[ORM\Column]
    private ?int $battleRating;

    #[ORM\Column]
    private ?int $tradesSell;

    #[ORM\Column]
    private ?int $tradesBuy;

    #[ORM\Column]
    private ?int $tradeRating;

    #[ORM\Column]
    private ?int $diplomacyRating;

    #[ORM\Column]
    private ?int $elorating;

    public function getBattlesFought(): ?int
    {
        return $this->battlesFought;
    }

    public function setBattlesFought(int $battlesFought): static
    {
        $this->battlesFought = $battlesFought;

        return $this;
    }

    public function getBattlesWon(): ?int
    {
        return $this->battlesWon;
    }

    public function setBattlesWon(int $battlesWon): static
    {
        $this->battlesWon = $battlesWon;

        return $this;
    }

    public function getBattlesLost(): ?int
    {
        return $this->battlesLost;
    }

    public function setBattlesLost(int $battlesLost): static
    {
        $this->battlesLost = $battlesLost;

        return $this;
    }

    public function getBattleRating(): ?int
    {
        return $this->battleRating;
    }

    public function setBattleRating(int $battlesRating): static
    {
        $this->battleRating = $battlesRating;

        return $this;
    }

    public function getTradesSell(): ?int
    {
        return $this->tradesSell;
    }

    public function setTradesSell(int $tradesSell): static
    {
        $this->tradesSell = $tradesSell;

        return $this;
    }

    public function getTradesBuy(): ?int
    {
        return $this->tradesBuy;
    }

    public function setTradesBuy(int $tradesBuy): static
    {
        $this->tradesBuy = $tradesBuy;

        return $this;
    }

    public function getTradeRating(): ?int
    {
        return $this->tradeRating;
    }

    public function setTradeRating(int $tradeRating): static
    {
        $this->tradeRating = $tradeRating;

        return $this;
    }

    public function getDiplomacyRating(): ?int
    {
        return $this->diplomacyRating;
    }

    public function setDiplomacyRating(int $diplomacyRating): static
    {
        $this->diplomacyRating = $diplomacyRating;

        return $this;
    }

    public function getElorating(): ?int
    {
        return $this->elorating;
    }

    public function setElorating(int $elorating): static
    {
        $this->elorating = $elorating;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
