<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Resources\BaseResources;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FleetBookmarkRepository::class)]
#[ORM\Table(name: 'fleet_bookmarks')]
class FleetBookmark
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy:'fleetBookmarks')]
    private ?User $user = null;

    #[ORM\Column]
    private string $name = '';

    #[ORM\JoinColumn(name: 'target_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Entity::class)]
    private ?Entity $target = null;

    #[ORM\Column(type: Types::JSON)]
    private array $ships = [];

    #[ORM\Column(name: 'res', type: 'baseResources')]
    private BaseResources $freight;

    #[ORM\Column(name: 'resfetch', type: 'baseResources')]
    private BaseResources $fetch;

    #[ORM\Column]
    private string $action = 'flight';

    #[ORM\Column]
    private int $speed = 100;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getShips(): array
    {
        return $this->ships;
    }

    public function setShips(array $ships): static
    {
        $this->ships = $ships;

        return $this;
    }

    public function getAction(): ?string
    {
        return FleetAction::createFactory($this->action);
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): static
    {
        $this->speed = $speed;

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

    public function getTarget(): ?Entity
    {
        return $this->target;
    }

    public function setTarget(?Entity $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getFreight():BaseResources
    {
        return $this->freight;
    }

    public function setFreight(BaseResources $freight): static
    {
        $this->freight = $freight;

        return $this;
    }

    public function getFetch(): BaseResources
    {
        return $this->fetch;
    }

    public function setFetch(BaseResources $fetch): static
    {
        $this->fetch = $fetch;

        return $this;
    }
}
