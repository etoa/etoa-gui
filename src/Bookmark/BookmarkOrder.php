<?php declare(strict_types=1);

namespace EtoA\Bookmark;

class BookmarkOrder
{
    public const ORDER_ID = 'bookmarks.id';
    public const ORDER_COORDINATES = 'bookmarks.entity_id';
    public const ORDER_COMMENT = 'bookmarks.comment';
    public const ORDER_ENTITY_TYPE = 'entities.code';
    public const ORDER_OWNER = 'users.user_nick';

    public const ALL_ORDERS = [
        self::ORDER_ID => "Erstelldatum",
        self::ORDER_COORDINATES => "Koordianten",
        self::ORDER_COMMENT => "Kommentar",
        self::ORDER_ENTITY_TYPE => "Typ",
        self::ORDER_OWNER => "Besitzer",
    ];

    public string $order;
    public string $direction;

    public function __construct(string $order, string $direction)
    {
        $this->order = in_array($order, self::ALL_ORDERS, true) ? $order : self::ORDER_ID;
        $this->direction = $direction;
    }
}
