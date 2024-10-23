<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Message\MessageCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageCategoryRepository::class)]
#[ORM\Table(name: 'users')]
class MessageCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "cat_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "cat_name")]
    private string $name;

    #[ORM\Column(name: "cat_desc")]
    private string $description;

    #[ORM\Column(name: "cat_sender")]
    private string $sender;

    public static function createFromArray(array $data): MessageCategory
    {
        $category = new MessageCategory();

        $category->id = (int) $data['cat_id'];
        $category->name = $data['cat_name'];
        $category->description = $data['cat_desc'];
        $category->sender = $data['cat_sender'];

        return $category;
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): static
    {
        $this->sender = $sender;

        return $this;
    }
}
