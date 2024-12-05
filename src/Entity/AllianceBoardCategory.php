<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EtoA\Alliance\Board\AllianceBoardCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceBoardCategoryRepository::class)]
#[ORM\Table(name: 'allianceboard_cat')]
class AllianceBoardCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "cat_id")]
    private int $id;

    #[ORM\Column(name: "cat_bullet")]
    private string $bullet;

    #[ORM\Column(name: "cat_name")]
    private string $name;

    #[ORM\Column(name: "cat_desc")]
    private string $description;

    #[ORM\Column(name: "cat_order")]
    private int $order;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: AllianceBoardTopic::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'cat_id', referencedColumnName: 'topic_cat_id')]
    private Collection $topics;

    #[ORM\JoinColumn(name: 'cat_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private Alliance|null $alliance;

    #[ORM\Column(name: "cat_alliance_id")]
    private string $allianceId;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBullet(): ?string
    {
        return $this->bullet;
    }

    public function setBullet(string $bullet): static
    {
        $this->bullet = $bullet;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getAllianceId(): ?string
    {
        return $this->allianceId;
    }

    public function setAllianceId(string $allianceId): static
    {
        $this->allianceId = $allianceId;

        return $this;
    }

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

        return $this;
    }

    /**
     * @return Collection<int, AllianceBoardTopic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(AllianceBoardTopic $topic): static
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->setCategory($this);
        }

        return $this;
    }

    public function removeTopic(AllianceBoardTopic $topic): static
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getCategory() === $this) {
                $topic->setCategory(null);
            }
        }

        return $this;
    }


}
