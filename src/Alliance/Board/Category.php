<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

class Category
{
    public int $id;
    public string $bullet;
    public string $name;
    public string $description;
    public int $order;

    public function __construct(array $data)
    {
        $this->id = (int) $data['cat_id'];
        $this->bullet = $data['cat_bullet'];
        $this->name = $data['cat_name'];
        $this->description = $data['cat_desc'];
        $this->order = (int) $data['cat_order'];
    }
}
