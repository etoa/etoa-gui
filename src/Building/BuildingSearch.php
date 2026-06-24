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
        $this->parts[] = 'q.prodMetal > 0 OR q.prodCrystal > 0 OR q.prodPlastic > 0 OR q.prodFuel > 0 OR q.prodFood > 0 OR q.powerUse > 0';

        return $this;
    }

    public function withPowerProduction(): self
    {
        $this->parts[] = 'q.prodPower > 0';

        return $this;
    }

    public function storage(): self
    {
        $this->parts[] = 'q.storeMetal > 0 OR q.storeCrystal > 0 OR q.storePlastic > 0 OR q.storeFuel > 0 OR q.storeFood > 0';

        return $this;
    }

    public function show(): self
    {
        $this->parts[] = 'q.show = 1';

        return $this;
    }
}
