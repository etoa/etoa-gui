<?php declare(strict_types=1);

namespace EtoA\DefaultItem;

use EtoA\User\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefaultItemRepository::class)]
#[ORM\Table(name: 'default_items')]
class DefaultItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "item_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "item_set_id", type: "integer")]
    private int $setId;

    #[ORM\Column(name: "item_object_id", type: "integer")]
    private int $objectId;

    #[ORM\Column(name: "item_count", type: "integer")]
    private int $count;

    #[ORM\Column(name: "item_cat", type: "string")]
    private string $cat;



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
