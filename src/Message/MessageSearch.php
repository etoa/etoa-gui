<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\Database\AbstractSearch;

class MessageSearch extends AbstractSearch
{
    public static function create(): MessageSearch
    {
        return new MessageSearch();
    }

    public function id(int $id): self
    {
        $this->parts[] = 'm.message_id = :id';
        $this->parameters['id'] = $id;

        return $this;
    }

    public function fromUser(int $id): self
    {
        $this->parts[] = 'm.message_user_from = :fromUser';
        $this->parameters['fromUser'] = $id;

        return $this;
    }

    public function toUser(int $id): self
    {
        $this->parts[] = 'm.message_user_to = :toUser';
        $this->parameters['toUser'] = $id;

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

    public function category(int $id): self
    {
        $this->parts[] = 'm.message_cat_id = :category';
        $this->parameters['category'] = $id;

        return $this;
    }

    public function read(bool $read): self
    {
        $this->parts[] = 'm.message_read = :read';
        $this->parameters['read'] = (int) $read;

        return $this;
    }

    public function massmail(bool $massmail): self
    {
        $this->parts[] = 'm.message_massmail = :massmail';
        $this->parameters['massmail'] = (int) $massmail;

        return $this;
    }

    public function deleted(bool $deleted): self
    {
        $this->parts[] = 'm.message_deleted = :deleted';
        $this->parameters['deleted'] = (int) $deleted;

        return $this;
    }

    public function archived(bool $archived): self
    {
        $this->parts[] = 'm.message_archived = :archived';
        $this->parameters['archived'] = (int) $archived;

        return $this;
    }
}
