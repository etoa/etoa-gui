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

    public function setCatName(string $catName):void {
        $this->name = $catName;
    }

    public function getCatName():string {
        return $this->name;
    }

    public function getCatDesc():string {
        return $this->description;
    }

    public function setCatDesc(string $catDesc):void {
        $this->description = $catDesc;
    }

    public function getCatOrder():int {
        return $this->order;
    }

    public function setCatOrder(int $catOrder):void {
        $this->order = $catOrder;
    }

    public function setCatBullet(string $catBullet):void {
        $this->bullet = $catBullet;
    }

    public function getCatBullet():string {
        return $this->bullet;
    }
}
