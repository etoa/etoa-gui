<?php declare(strict_types=1);

namespace EtoA\Alliance\Base;

use EtoA\Universe\Resources\BaseResources;

class AllianceItemBuildStatus
{
    public const STATUS_OK = 'ok';
    public const STATUS_UNDER_CONSTRUCTION = 'under-construction';
    public const STATUS_ITEM_UNDER_CONSTRUCTION = 'item-under-construction';
    public const STATUS_MAX_LEVEL = 'max-level';
    public const STATUS_MISSING_RESOURCE = 'missing-resources';
    public const STATUS_MISSING_REQUIREMENTS = 'missing-requirements';

    public const STATUS_MESSAGES = [
        self::STATUS_UNDER_CONSTRUCTION => 'Es wird bereits gebaut!',
        self::STATUS_ITEM_UNDER_CONSTRUCTION => 'Es wird bereits gebaut!',
        self::STATUS_MAX_LEVEL => 'Maximalstufe erreicht!',
        self::STATUS_MISSING_RESOURCE => 'Zuwenig Rohstoffe vorhanden!',
        self::STATUS_MISSING_REQUIREMENTS => 'Voraussetzungen nicht erfüllt!',
    ];

    public string $status;
    public BaseResources $missingResources;

    private function __construct(string $status, BaseResources $missingResources = null)
    {
        $this->status = $status;
        $this->missingResources = $missingResources ?? new BaseResources();
    }

    public static function ok(): self
    {
        return new AllianceItemBuildStatus(self::STATUS_OK);
    }

    public static function underConstruction(): self
    {
        return new AllianceItemBuildStatus(self::STATUS_UNDER_CONSTRUCTION);
    }

    public static function itemUnderConstruction(): self
    {
        return new AllianceItemBuildStatus(self::STATUS_ITEM_UNDER_CONSTRUCTION);
    }

    public static function maxLevel(): self
    {
        return new AllianceItemBuildStatus(self::STATUS_MAX_LEVEL);
    }

    public static function missingResources(BaseResources $resources): self
    {
        return new AllianceItemBuildStatus(self::STATUS_MISSING_RESOURCE, $resources);
    }

    public static function missingRequirements(): self
    {
        return new AllianceItemBuildStatus(self::STATUS_MISSING_REQUIREMENTS);
    }
}
