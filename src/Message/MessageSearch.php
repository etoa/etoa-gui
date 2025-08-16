<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\MessageCategory;
use EtoA\Entity\User;

class MessageSearch extends AbstractSearch
{
    public static function create(): MessageSearch
    {
        return new MessageSearch();
    }

    public function id(int $id): self
    {
        $this->parts[] = 'q.id = :id';
        $this->parameters['id'] = $id;

        return $this;
    }

    public function fromUser(int|User $user): self
    {
        $this->parts[] = 'q.userFrom = :fromUser';
        $this->parameters['fromUser'] = $user;

        return $this;
    }

    public function toUser(int|User $user): self
    {
        $this->parts[] = 'q.userTo = :toUser';
        $this->parameters['toUser'] = $user;

        return $this;
    }

    public function subjectLike(string $subject): self
    {
        $this->parts[] = 'd.subject LIKE :subjectLike';
        $this->parameters['subjectLike'] = '%' . $subject . '%';

        return $this;
    }

    public function textLike(string $text): self
    {
        $this->parts[] = 'd.text LIKE :textLike';
        $this->parameters['textLike'] = '%' . $text . '%';

        return $this;
    }

    public function category(int|MessageCategory $category): self
    {
        $this->parts[] = 'q.cat = :category';
        $this->parameters['category'] = $category;

        return $this;
    }

    public function read(bool $read): self
    {
        $this->parts[] = 'q.read = :read';
        $this->parameters['read'] = $read;

        return $this;
    }

    public function massmail(bool $massmail): self
    {
        $this->parts[] = 'q.massMail = :massmail';
        $this->parameters['massmail'] = $massmail;

        return $this;
    }

    public function deleted(bool $deleted): self
    {
        $this->parts[] = 'q.deleted = :deleted';
        $this->parameters['deleted'] = $deleted;

        return $this;
    }

    public function archived(bool $archived): self
    {
        $this->parts[] = 'q.archived = :archived';
        $this->parameters['archived'] = $archived;

        return $this;
    }
}
