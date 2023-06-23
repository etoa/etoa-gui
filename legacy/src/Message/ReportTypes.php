<?php declare(strict_types=1);

namespace EtoA\Message;

class ReportTypes
{
    public const TYPE_BATTLE = 'battle';
    public const TYPE_SPY = 'spy';
    public const TYPE_EXPLORE = 'explore';
    public const TYPE_MARKET = 'market';
    public const TYPE_CRYPTO = 'crypto';
    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_BATTLE => 'Kampf',
        self::TYPE_SPY => 'Spionage',
        self::TYPE_EXPLORE => 'Erkundung',
        self::TYPE_MARKET => 'Markt',
        self::TYPE_CRYPTO => 'Krypto',
        self::TYPE_OTHER => 'Sonstige',
    ];
}
