<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AllianceBoardTopicRepository::class)]
#[ORM\Table(name: 'allianceboard_topics')]
class AllianceBoardTopic
{
    public function __construct() {
        $this->posts = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "topic_id")]
    private int $id;

    #[ORM\JoinColumn(name: 'topic_cat_id', referencedColumnName: 'cat_id')]
    #[ORM\ManyToOne(targetEntity: AllianceBoardCategory::class)]
    private AllianceBoardCategory|null $category;

    #[ORM\Column(name: "topic_bnd_id")]
    private int $bndId = 0;

    #[ORM\JoinColumn(name: 'topic_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User|null $user;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: AllianceBoardPost::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'topic_id', referencedColumnName: 'post_topic_id')]
    private Collection $posts;

    #[ORM\Column(name: "topic_subject")]
    private string $subject = '';

    #[ORM\Column(name: "topic_user_nick")]
    private string $userNick;

    #[ORM\Column(name: "topic_count")]
    private int $count = 0;

    #[ORM\Column(name: "topic_timestamp")]
    private int $timestamp = 0;

    #[ORM\Column(name: "topic_top")]
    private bool $top = false;

    #[ORM\Column(name: "topic_closed")]
    private bool $closed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBndId(): ?int
    {
        return $this->bndId;
    }

    public function setBndId(int $bndId): static
    {
        $this->bndId = $bndId;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function isTop(): ?bool
    {
        return $this->top;
    }

    public function setTop(bool $top): static
    {
        $this->top = $top;

        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): static
    {
        $this->closed = $closed;

        return $this;
    }

    public function getCategory(): ?AllianceBoardCategory
    {
        return $this->category;
    }

    public function setCategory(?AllianceBoardCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, AllianceBoardPost>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(AllianceBoardPost $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setTopic($this);
        }

        return $this;
    }

    public function removePost(AllianceBoardPost $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getTopic() === $this) {
                $post->setTopic(null);
            }
        }

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

    public function getUserNick(): ?string
    {
        return $this->userNick;
    }

    public function setUserNick(string $userNick): static
    {
        $this->userNick = $userNick;

        return $this;
    }


}
