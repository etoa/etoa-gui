<?php declare(strict_types=1);

namespace EtoA\Universe\Resources;

class ResourceNames
{
    public const METAL = 'Titan';
    public const CRYSTAL = 'Silizium';
    public const PLASTIC = 'PVC';
    public const FUEL = 'Tritium';
    public const FOOD = 'Nahrung';
    public const POWER = 'Energie';
    public const TIME = 'Zeit';
    public const FIELDS = 'Felder';

    public const NAMES = [
        self::METAL,
        self::CRYSTAL,
        self::PLASTIC,
        self::FUEL,
        self::FOOD,
    ];

    public function getMetal(): string
    {
        return self::METAL;
    }

    public function getCrystal(): string
    {
        return self::CRYSTAL;
    }

    public function getPlastic(): string
    {
        return self::PLASTIC;
    }

    public function getFuel(): string
    {
        return self::FUEL;
    }

    public function getFood(): string
    {
        return self::FOOD;
    }

}
