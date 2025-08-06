<?php declare(strict_types=1);

namespace EtoA\Entity;
use Doctrine\ORM\Mapping as ORM;
use EtoA\Admin\AdminNotesRepository;

#[ORM\Entity(repositoryClass: AdminNotesRepository::class)]
#[ORM\Table(name: 'admin_notes')]
class AdminNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "notes_id")]
    private int $id;

    #[ORM\JoinColumn(name: 'admin_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    private ?AdminUser $admin = null;

    #[ORM\Column(name: "titel")]
    private string $title;

    #[ORM\Column]
    private string $text;

    #[ORM\Column]
    private int $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAdmin(): ?AdminUser
    {
        return $this->admin;
    }

    public function setAdmin(?AdminUser $admin): static
    {
        $this->admin = $admin;

        return $this;
    }
}
