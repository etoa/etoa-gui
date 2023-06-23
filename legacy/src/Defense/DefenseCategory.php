<?php declare(strict_types=1);

namespace EtoA\Defense;

class DefenseCategory
{
    public int $id;
    public string $name;
    public int $order;
    public string $color;

    public function __construct(array $data)
    {
        $this->id = (int) $data['cat_id'];
        $this->name = $data['cat_name'];
        $this->order = (int) $data['cat_order'];
        $this->color = $data['cat_color'];
    }
}
