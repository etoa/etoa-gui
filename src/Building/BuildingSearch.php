<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\Database\AbstractSearch;

class BuildingSearch extends AbstractSearch
{
    public static function create(): BuildingSearch
    {
        return new BuildingSearch();
    }

    public function withProductionOrPowerUse(): self
    {
        $this->parts[] = 'building_prod_metal > 0 OR building_prod_crystal > 0 OR building_prod_plastic > 0 OR building_prod_fuel > 0 OR building_prod_food > 0 OR building_power_use > 0';

        return $this;
    }

    public function withPowerProduction(): self
    {
        $this->parts[] = 'building_prod_power > 0';

        return $this;
    }

    public function storage(): self
    {
        $this->parts[] = 'building_store_metal > 0 OR building_store_crystal > 0 OR building_store_plastic > 0 OR building_store_fuel > 0 OR building_store_food > 0';

        return $this;
    }

    public function show(): self
    {
        $this->parts[] = 'building_show = 1';

        return $this;
    }
}
