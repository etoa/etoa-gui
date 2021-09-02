<?php declare(strict_types=1);

namespace EtoA\Universe\Resources;

class ResIcons
{
    public const METAL = '<img class="resIcon" src="images/resources/metal_s.png" alt="' . ResourceNames::METAL . '" />';
    public const CRYSTAL = '<img class="resIcon" src="images/resources/metal_s.png" alt="' . ResourceNames::CRYSTAL . '" />';
    public const PLASTIC = '<img class="resIcon" src="images/resources/metal_s.png" alt="' . ResourceNames::PLASTIC . '" />';
    public const FUEL = '<img class="resIcon" src="images/resources/metal_s.png" alt="' . ResourceNames::FUEL . '" />';
    public const FOOD = '<img class="resIcon" src="images/resources/metal_s.png" alt="' . ResourceNames::FOOD . '" />';
    public const POWER = '<img class="resIcon" src="images/resources/metal_s.png" alt="' . ResourceNames::POWER . '" />';
    public const POWER_USE = '<img class="resIcon" src="images/resources/metal_s.png" alt="Energieverbrauch" />';
    public const PEOPLE = '<img class="resIcon" src="images/resources/metal_s.png" alt="Bevölkerung" />';
    public const TIME = '<img class="resIcon" src="images/resources/metal_s.png" alt="' . ResourceNames::TIME . '" />';
    public const FIELDS = '<img class="resIcon" src="images/resources/metal_s.png" alt="' . ResourceNames::FIELDS . '" />';

    public const ICONS = [
        self::METAL,
        self::CRYSTAL,
        self::PLASTIC,
        self::FUEL,
        self::FOOD,
    ];
}
