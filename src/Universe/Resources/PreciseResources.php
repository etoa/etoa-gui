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
    public float $time = 0;

    public function set(int $index, float $value): void
    {
        switch ($index) {
            case 0:
                $this->metal = $value;

                break;
            case 1:
                $this->crystal = $value;

                break;
            case 2:
                $this->plastic = $value;

                break;
            case 3:
                $this->fuel = $value;

                break;
            case 4:
                $this->food = $value;

                break;
            default:
                throw new \Exception('Invalid resource index ' . $index);
        }
    }

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

        return $resources;
    }

    public function add(BaseResources|PreciseResources $add): PreciseResources
    {
        $resources = new PreciseResources();
        $resources->metal = $this->metal + $add->metal;
        $resources->crystal = $this->crystal + $add->crystal;
        $resources->plastic = $this->plastic + $add->plastic;
        $resources->fuel = $this->fuel + $add->fuel;
        $resources->food = $this->food + $add->food;
        $resources->people = $this->people + $add->people;

        return $resources;
    }

    public function missing(PreciseResources|BaseResources $available): PreciseResources
    {
        $resources = new PreciseResources();
        $resources->metal = max(0, $this->metal - $available->metal);
        $resources->crystal = max(0, $this->crystal - $available->crystal);
        $resources->plastic = max(0, $this->plastic - $available->plastic);
        $resources->fuel = max(0, $this->fuel - $available->fuel);
        $resources->food = max(0, $this->food - $available->food);
        $resources->people = max(0, $this->people - $available->people);

        return $resources;
    }

    public function getSum(): float
    {
        return $this->metal + $this->crystal + $this->plastic + $this->fuel + $this->food + $this->people;
    }
}
