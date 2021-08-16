<?php

declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;

class MarketHandler
{
    private MarketRateRepository $marketRateRepository;
    private RuntimeDataStore $runtimeDataStore;
    private PlanetRepository $planetRepository;
    private FleetRepository $fleetRepository;
    private ConfigurationService $config;

    public function __construct(
        MarketRateRepository $marketRateRepository,
        RuntimeDataStore $runtimeDataStore,
        PlanetRepository $planetRepository,
        FleetRepository $fleetRepository,
        ConfigurationService $config
    ) {
        $this->marketRateRepository = $marketRateRepository;
        $this->runtimeDataStore = $runtimeDataStore;
        $this->planetRepository = $planetRepository;
        $this->fleetRepository = $fleetRepository;
        $this->config = $config;
    }

    /**
     * @return float[]
     */
    public function calcRate(): array
    {
        $rateMin = $this->config->getFloat('market_rate_min');
        $rateMax = $this->config->getFloat('market_rate_max');

        // Resulting rates
        $rates = array_fill(0, BaseResources::NUM_RESOURCES, 1);
        $res_rates = array_fill(0, BaseResources::NUM_RESOURCES, 1);

        // Load previous rates
        $previousRates = $this->marketRateRepository->getRates($this->config->getInt('market_rates_count'));
        $nr = count($previousRates);
        $rc = 0;
        foreach ($previousRates as $rate) {
            // Weight factor calculation. Insert any other formula if you desire so.
            // Newer values should weight more than older values
            $factor = $nr - $rc;
            //$factor = log($nr-$rc);

            // For every resource calculate the ration and multiply with the weight factor
            for ($i = 0; $i < BaseResources::NUM_RESOURCES; $i++) {
                if ($rate->supply->get($i) > 0) {
                    $r = ($rate->demand->get($i) / $rate->supply->get($i));
                    if ($r > $rateMax) {
                        $rates[$i] += $rateMax * $factor;
                    }
                    if ($r < $rateMin) {
                        $rates[$i] += $rateMin * $factor;
                    } else {
                        $rates[$i] += $r * $factor;
                    }
                } else {
                    if ($rate->demand->get($i) > 0) {
                        $rates[$i] += $rateMax * $factor;
                    } else {
                        $rates[$i] += 1 * $factor;
                    }
                }
            }
            $rc++;
        }
        // Normalize the resulting values
        $normalizer = array_sum($rates) / BaseResources::NUM_RESOURCES;
        for ($i = 0; $i < BaseResources::NUM_RESOURCES; $i++) {
            $rates[$i] = round($rates[$i] / $normalizer, 2);
        }

        // Adding planet/fleet res in universe
        $planetRes = $this->planetRepository->getGlobalResources();
        $fleetRes = $this->fleetRepository->getGlobalResources();

        for ($i = 0; $i < BaseResources::NUM_RESOURCES; $i++) {
            $resSum = $planetRes->get($i) + $fleetRes->get($i);
            $res_rates[$i] = $resSum > 0 ? 1 / $resSum : 1;
        }

        $normalizer = array_sum($res_rates) / BaseResources::NUM_RESOURCES;
        for ($i = 0; $i < BaseResources::NUM_RESOURCES; $i++) {
            $res_rates[$i] = round($res_rates[$i] / $normalizer, 2);
        }

        for ($i = 0; $i < BaseResources::NUM_RESOURCES; $i++) {
            $rates[$i] = $res_rates[$i] + $rates[$i];
        }

        $normalizer = array_sum($rates) / BaseResources::NUM_RESOURCES;
        for ($i = 0; $i < BaseResources::NUM_RESOURCES; $i++) {
            $rates[$i] = round($rates[$i] / $normalizer, 2);
        }

        return $rates;
    }

    /**
     * Update market resource rates basen on previous demand and supply
     */
    public function updateRates(): void
    {
        $rates = $this->calcRate();

        $rate = new MarketRate();
        $rate->timestamp = time();
        for ($i = 0; $i < BaseResources::NUM_RESOURCES; $i++) {
            $this->runtimeDataStore->set('market_rate_' . $i, (string) $rates[$i]);
            $rate->rate->set($i, $rates[$i]);
        }

        // Add a new row to the rates table. This row gets filled from now on with buy results
        $this->marketRateRepository->save($rate);

        // Remove old values
        $rates = $this->marketRateRepository->getRates(1, $this->config->getInt('market_rates_count') * 2);
        if (count($rates) > 0) {
            $this->marketRateRepository->removeWhereIdLowerThan($rates[0]->id);
        }
    }

    /**
     * Add resources when a transaction is made
     *
     * @param  int[] $supply
     * @param  int[] $demand
     */
    public function addResToRate(array $supply, array $demand): void
    {
        $rates = $this->marketRateRepository->getRates(1);
        $rate = $rates[0];

        for ($i = 0; $i < BaseResources::NUM_RESOURCES; $i++) {
            $rate->supply->set($i, $supply[$i]);
            $rate->demand->set($i, $demand[$i]);
        }

        $this->marketRateRepository->save($rate);
    }
}
