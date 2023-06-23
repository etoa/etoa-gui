<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

class DefaultItem
{
    public int $id;
    public int $objectId;
    public int $count;
    public string $cat;

    public static function createFromData(array $data): DefaultItem
    {
        $item = new DefaultItem();
        $item->id = (int) $data['item_id'];
        $item->objectId = (int) $data['item_object_id'];
        $item->count = (int) $data['item_count'];
        $item->cat = $data['item_cat'];

        return $item;
    }

    public static function empty(): DefaultItem
    {
        $item = new DefaultItem();
        $item->id = 0;
        $item->objectId = 0;
        $item->count = 0;
        $item->cat = '';

        return $item;
    }

    public function setObject(?string $object): void
    {
        if ($object !== null) {
            $parts = explode(':', $object);
            $this->cat = $parts[0];
            $this->objectId = (int) $parts[1];
        }
    }

    public function getObject(): string
    {
        return $this->cat . ':' . $this->objectId;
    }
}
