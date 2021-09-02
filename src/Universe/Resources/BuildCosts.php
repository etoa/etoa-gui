<?php

declare(strict_types=1);

namespace EtoA\Universe\Resources;

use EtoA\Universe\Planet\Planet;

class BuildCosts
{
    public float $metal = 0;
    public float $crystal = 0;
    public float $plastic = 0;
    public float $fuel = 0;
    public float $food = 0;
    public float $power = 0;

    public static function create(
        float $metal,
        float $crystal,
        float $plastic,
        float $fuel,
        float $food,
        float $power
    ): BuildCosts {
        $costs = new BuildCosts();
        $costs->metal = $metal;
        $costs->crystal = $crystal;
        $costs->plastic = $plastic;
        $costs->fuel = $fuel;
        $costs->food = $food;
        $costs->power = $power;

        return $costs;
    }

    public function clone(): BuildCosts
    {
        return clone $this;
    }

    public function add(BuildCosts $costs): BuildCosts
    {
        $this->metal += $costs->metal;
        $this->crystal += $costs->crystal;
        $this->plastic += $costs->plastic;
        $this->fuel += $costs->fuel;
        $this->food += $costs->food;
        $this->power += $costs->power;

        return $this;
    }

    public function multiply(float $factor): BuildCosts
    {
        $this->metal *= $factor;
        $this->crystal *= $factor;
        $this->plastic *= $factor;
        $this->fuel *= $factor;
        $this->food *= $factor;
        $this->power *= $factor;

        return $this;
    }

    public function total(): float
    {
        return $this->metal + $this->crystal + $this->plastic + $this->fuel + $this->food;
    }

    public function isCoveredOnPlanet(Planet $planet): bool
    {
        return $planet->resMetal >= $this->metal
            && $planet->resCrystal >= $this->crystal
            && $planet->resPlastic >= $this->plastic
            && $planet->resFuel >= $this->fuel
            && $planet->resFood >= $this->food
            && $planet->prodPower >= $this->power;
    }
}
