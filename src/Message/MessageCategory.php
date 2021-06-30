<?php

declare(strict_types=1);

namespace EtoA\Message;

class MessageCategory
{
    public int $id;
    public string $name;
    public string $description;
    public string $sender;

    public static function createFromArray(array $data): MessageCategory
    {
        $category = new MessageCategory();

        $category->id = (int) $data['cat_id'];
        $category->name = $data['cat_name'];
        $category->description = $data['cat_desc'];
        $category->sender = $data['cat_sender'];

        return $category;
    }
}
