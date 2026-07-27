<?php declare(strict_types=1);

namespace EtoA\Bookmark;

class BookmarkOrder
{
    /** The values are DQL paths of the query in BookmarkRepository::findForUser() */
    public const ORDER_ID = 'bookmarks.id';
    public const ORDER_COORDINATES = 'bookmarks.entity';
    public const ORDER_COMMENT = 'bookmarks.comment';
    public const ORDER_ENTITY_TYPE = 'entities.code';
    public const ORDER_OWNER = 'users.nick';

    public const ALL_ORDERS = [
        self::ORDER_ID => "Erstelldatum",
        self::ORDER_COORDINATES => "Koordinaten",
        self::ORDER_COMMENT => "Kommentar",
        self::ORDER_ENTITY_TYPE => "Typ",
        self::ORDER_OWNER => "Besitzer",
    ];

    public string $order;
    public string $direction;

    public function __construct(?string $order, ?string $direction)
    {
        // The order values are the array keys, not the (human readable) values
        $this->order = $order !== null && array_key_exists($order, self::ALL_ORDERS) ? $order : self::ORDER_ID;
        $this->direction = strtoupper((string) $direction) === 'DESC' ? 'DESC' : 'ASC';
    }

    /**
     * Ordering by owner needs the target planet and its user joined.
     */
    public function requiresOwnerJoin(): bool
    {
        return $this->order === self::ORDER_OWNER;
    }
}
