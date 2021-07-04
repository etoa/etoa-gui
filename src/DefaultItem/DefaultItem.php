<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

class DefaultItem
{
    public int $id;
    public int $objectId;
    public int $count;
    public string $cat;

    public function __construct(array $data)
    {
        $this->id = (int) $data['item_id'];
        $this->objectId = (int) $data['item_object_id'];
        $this->count = (int) $data['item_count'];
        $this->cat = $data['item_cat'];
    }
}
