<?php declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\Core\Database\AbstractSearch;

class ChatLogSearch extends AbstractSearch
{
    public static function create(): ChatLogSearch
    {
        return new ChatLogSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function textLike(string $text): self
    {
        $this->parts[] = 'text LIKE :textLike';
        $this->parameters['textLike'] = '%' . $text . '%';

        return $this;
    }
}
