<?php declare(strict_types=1);

namespace EtoA\Message;

enum ReportTypes: string
{
    case BATTLE = 'battle';
    case SPY = 'spy';
    case EXPLORE = 'explore';
    case MARKET = 'market';
    case CRYPTO = 'crypto';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BATTLE => 'Kampf',
            self::SPY => 'Spionage',
            self::EXPLORE => 'Erkundung',
            self::MARKET => 'Markt',
            self::CRYPTO => 'Krypto',
            self::OTHER => 'Sonstige',
        };
    }

    /**
     * @return array<string,string>
     */
    public static function items(): array
    {
        $items = [];
        foreach (self::cases() as $case) {
            $items[$case->value] = $case->label();
        }
        return $items;
    }
}

