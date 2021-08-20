<?php

declare(strict_types=1);

namespace EtoA\Universe\Resources;

use Exception;

class BaseResources
{
    public const NUM_RESOURCES = 5;

    public int $metal = 0;
    public int $crystal = 0;
    public int $plastic = 0;
    public int $fuel = 0;
    public int $food = 0;
    public int $people = 0;

    public function get(int $index): int
    {
        if ($index == 0) {
            return $this->metal;
        }
        if ($index == 1) {
            return $this->crystal;
        }
        if ($index == 2) {
            return $this->plastic;
        }
        if ($index == 3) {
            return $this->fuel;
        }
        if ($index == 4) {
            return $this->food;
        }

        throw new Exception('Invalid resource index ' . $index);
    }

    public function set(int $index, int $value): void
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
                throw new Exception('Invalid resource index ' . $index);
        }
    }

    public function add(BaseResources $resources): void
    {
        $this->metal += $resources->metal;
        $this->crystal += $resources->crystal;
        $this->plastic += $resources->plastic;
        $this->fuel += $resources->fuel;
        $this->food += $resources->food;
    }

    public function remove(BaseResources $resources): void
    {
        $this->metal -= $resources->metal;
        $this->crystal -= $resources->crystal;
        $this->plastic -= $resources->plastic;
        $this->fuel -= $resources->fuel;
        $this->food -= $resources->food;
    }

    /**
     * @param BaseResources|PreciseResources $available
     */
    public function missing($available): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = (int) ceil(max(0, $this->metal - $available->metal));
        $resources->crystal = (int) ceil(max(0, $this->crystal - $available->crystal));
        $resources->plastic = (int) ceil(max(0, $this->plastic - $available->plastic));
        $resources->fuel = (int) ceil(max(0, $this->fuel - $available->fuel));
        $resources->food = (int) ceil(max(0, $this->food - $available->food));
        $resources->people = (int) ceil(max(0, $this->people - $available->people));

        return $resources;
    }

    public function getSum(): int
    {
        return $this->metal + $this->crystal + $this->plastic + $this->fuel + $this->food + $this->people;
    }
}
