<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\User;

class ChatLogSearch extends AbstractSearch
{
    public static function create(): ChatLogSearch
    {
        return new ChatLogSearch();
    }

    public function userId(int|User $userId): self
    {
        $this->parts[] = 'q.user = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function textLike(string $text): self
    {
        $this->parts[] = 'q.text LIKE :textLike';
        $this->parameters['textLike'] = '%' . $text . '%';

        return $this;
    }
}
