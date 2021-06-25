<?php declare(strict_types=1);

namespace EtoA\Technology;

class TechnologyType
{
    public int $id;
    public string $name;
    public int $order;
    public string $color;

    public function __construct(array $data)
    {
        $this->id = (int) $data['type_id'];
        $this->name = $data['type_name'];
        $this->order = (int) $data['type_order'];
        $this->color = $data['type_color'];
    }
}
