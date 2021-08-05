<?php declare(strict_types=1);

namespace EtoA\Universe\Resources;

class PreciseResources
{
    public float $metal = 0;
    public float $crystal = 0;
    public float $plastic = 0;
    public float $fuel = 0;
    public float $food = 0;
    public float $people = 0;

    public static function createFromBase(BaseResources $baseResources): PreciseResources
    {
        $resources = new PreciseResources();
        $resources->metal = $baseResources->metal;
        $resources->crystal = $baseResources->crystal;
        $resources->plastic = $baseResources->plastic;
        $resources->fuel = $baseResources->fuel;
        $resources->food = $baseResources->food;
        $resources->people = $baseResources->people;

        return $resources;
    }

    public function multiply(float $factor): PreciseResources
    {
        $resources = new PreciseResources();
        $resources->metal = $this->metal * $factor;
        $resources->crystal = $this->crystal * $factor;
        $resources->plastic = $this->plastic * $factor;
        $resources->fuel = $this->fuel * $factor;
        $resources->food = $this->food * $factor;
        $resources->people = $this->people * $factor;

        return $this;
    }

    public function sum(): float
    {
        return $this->metal + $this->crystal + $this->plastic + $this->fuel + $this->food + $this->people;
    }
}
