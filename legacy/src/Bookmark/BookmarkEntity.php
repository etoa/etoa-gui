<?php declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Universe\Entity\EntityLabel;

class BookmarkEntity extends EntityLabel
{
    public ?string $comment;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->comment = $data['comment'];
    }

    public function toString(): string
    {
        return sprintf('%s  - %s (%s)', $this->codeString(), parent::toString(), $this->comment);
    }
}
