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
    private ?string $battlesFought;

    #[ORM\Column]
    private ?string $battlesWon;

    #[ORM\Column]
    private ?string $battlesLost;

    #[ORM\Column]
    private ?string $battleRating;

    #[ORM\Column]
    private ?string $tradesSell;

    #[ORM\Column]
    private ?string $tradesBuy;

    #[ORM\Column]
    private ?string $tradeRating;

    #[ORM\Column]
    private ?string $diplomacyRating;

    #[ORM\Column]
    private ?string $elorating;

    public function getBattlesFought(): ?string
    {
        return $this->battlesFought;
    }

    public function setBattlesFought(string $battlesFought): static
    {
        $this->battlesFought = $battlesFought;

        return $this;
    }

    public function getBattlesWon(): ?string
    {
        return $this->battlesWon;
    }

    public function setBattlesWon(string $battlesWon): static
    {
        $this->battlesWon = $battlesWon;

        return $this;
    }

    public function getBattlesLost(): ?string
    {
        return $this->battlesLost;
    }

    public function setBattlesLost(string $battlesLost): static
    {
        $this->battlesLost = $battlesLost;

        return $this;
    }

    public function getBattleRating(): ?string
    {
        return $this->battleRating;
    }

    public function setBattleRating(string $battlesRating): static
    {
        $this->battleRating = $battlesRating;

        return $this;
    }

    public function getTradesSell(): ?string
    {
        return $this->tradesSell;
    }

    public function setTradesSell(string $tradesSell): static
    {
        $this->tradesSell = $tradesSell;

        return $this;
    }

    public function getTradesBuy(): ?string
    {
        return $this->tradesBuy;
    }

    public function setTradesBuy(string $tradesBuy): static
    {
        $this->tradesBuy = $tradesBuy;

        return $this;
    }

    public function getTradeRating(): ?string
    {
        return $this->tradeRating;
    }

    public function setTradeRating(string $tradeRating): static
    {
        $this->tradeRating = $tradeRating;

        return $this;
    }

    public function getDiplomacyRating(): ?string
    {
        return $this->diplomacyRating;
    }

    public function setDiplomacyRating(string $diplomacyRating): static
    {
        $this->diplomacyRating = $diplomacyRating;

        return $this;
    }

    public function getElorating(): ?string
    {
        return $this->elorating;
    }

    public function setElorating(string $elorating): static
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
