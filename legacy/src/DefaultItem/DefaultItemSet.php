<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

class DefaultItemSet
{
    public int $id;
    public string $name;
    public bool $active;

    public function __construct(array $data)
    {
        $this->id = (int) $data['set_id'];
        $this->name = $data['set_name'];
        $this->active = (bool) $data['set_active'];
    }
}
