<?php

declare(strict_types=1);

namespace EtoA\UI;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\Planet;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserPropertiesRepository;

/**
 * Displays a box with resources, power and population
 */
class ResourceBoxDrawer
{
    private ConfigurationService $config;
    private UserPropertiesRepository $userPropertiesRepository;

    public function __construct(
        ConfigurationService     $config,
        UserPropertiesRepository $userPropertiesRepository
    )
    {
        $this->config = $config;
        $this->userPropertiesRepository = $userPropertiesRepository;
    }

    public function getHTML(Planet $planet): string
    {
        $userId = (int)\EtoA\Legacy\UserSession::getInstance($this->config)->user_id;
        $properties = $this->userPropertiesRepository->getOrCreateProperties($userId);

        return $properties->smallResBox ? $this->getHTMLSmall($planet) : $this->getHTMLNormal($planet);
    }

    private function getHTMLNormal(Planet $planet): string
    {
        $style0 = "resmetalcolor";
        $style1 = "rescrystalcolor";
        $style2 = "resplasticcolor";
        $style3 = "resfuelcolor";
        $style4 = "resfoodcolor";
        $style5 = "respeoplecolor";

        if ($planet->storeMetal <= floor($planet->resMetal) && floor($planet->resMetal) > 0) {
            $style0 = "resfullcolor";
        }
        if ($planet->storeCrystal <= floor($planet->resCrystal) && floor($planet->resCrystal) > 0) {
            $style1 = "resfullcolor";
        }
        if ($planet->storePlastic <= floor($planet->resPlastic) && floor($planet->resPlastic) > 0) {
            $style2 = "resfullcolor";
        }
        if ($planet->storeFuel <= floor($planet->resFuel) && floor($planet->resFuel) > 0) {
            $style3 = "resfullcolor";
        }
        if ($planet->storeFood <= floor($planet->resFood) && floor($planet->resFood) > 0) {
            $style4 = "resfullcolor";
        }
        if ($planet->peoplePlace <= floor($planet->people) && floor($planet->people) > 0) {
            $style5 = "resfullcolor";
        }
        if (floor($planet->prodPower) - floor($planet->usePower) < 0) {
            $style6 = "resfullcolor";
            $power_rest = floor($planet->prodPower) - floor($planet->usePower);
        } else {
            $style6 = "respowercolor";
            $power_rest = floor($planet->prodPower) - floor($planet->usePower);
        }

        $rtn = tableStart("Ressourcen") . "<tr>
        <th class=\"resBoxTitleCell\"><div class=\"resmetal\">" . ResourceNames::METAL . "</div></th>
        <th class=\"resBoxTitleCell\"><div class=\"rescrystal\">" . ResourceNames::CRYSTAL . "</div></th>
        <th class=\"resBoxTitleCell\"><div class=\"resplastic\">" . ResourceNames::PLASTIC . "</div></th>
        <th class=\"resBoxTitleCell\"><div class=\"resfuel\">" . ResourceNames::FUEL . "</div></th>
        <th class=\"resBoxTitleCell\"><div class=\"resfood\">" . ResourceNames::FOOD . "</div></th>
        <th class=\"resBoxTitleCell\"><div class=\"respeople\">Bewohner</div></th>
        <th class=\"resBoxTitleCell\"><div class=\"respower\">Energie</div></th>
        </tr><tr>"

            . $this->getResourceRow($style0, ResourceNames::METAL, "images/resources/metal.png", $planet->resMetal, $planet->storeMetal, $planet->prodMetal)
            . $this->getResourceRow($style1, ResourceNames::CRYSTAL, "images/resources/crystal.png", $planet->resCrystal, $planet->storeCrystal, $planet->prodCrystal)
            . $this->getResourceRow($style2, ResourceNames::PLASTIC, "images/resources/plastic.png", $planet->resPlastic, $planet->storePlastic, $planet->prodPlastic)
            . $this->getResourceRow($style3, ResourceNames::FUEL, "images/resources/fuel.png", $planet->resFuel, $planet->storeFuel, $planet->prodFuel)
            . $this->getResourceRow($style4, ResourceNames::FOOD, "images/resources/food.png", $planet->resFood, $planet->storeFood, $planet->prodFood)
            . $this->getResourceRow($style5, "Bevölkerung", "images/resources/people.png", $planet->people, $planet->peoplePlace, $planet->prodPeople)

            . "<td class=\"$style6\" " . mTT(ResourceNames::POWER, "<img width=\"40px\" height=\"40px\" src=\"images/resources/power.png\" style=\"float:left;margin-right:5px;\"/> <b>Produktion:</b> " . StringUtils::formatNumber($planet->prodPower) . "<br/><b>Verfügbar:</b> " . StringUtils::formatNumber($power_rest) . "<br/><b>Verbrauch:</b> " . StringUtils::formatNumber($planet->usePower) . "<br style=\"clear:both;\"/>") . ">" . StringUtils::formatNumber($power_rest) . "</td>
        </tr></table>";

        return $rtn;
    }

    private function getHTMLSmall(Planet $planet): string
    {
        $style0 = 'resmetal';
        $style1 = 'rescrystal';
        $style2 = 'resplastic';
        $style3 = 'resfuel';
        $style4 = 'resfood';
        $style5 = 'respeople';
        $style6 = '';

        if ($planet->storeMetal <= floor($planet->resMetal) && floor($planet->resMetal) > 0) {
            $style0 .= ' resfullcolor';
        }
        if ($planet->storeCrystal <= floor($planet->resCrystal) && floor($planet->resCrystal) > 0) {
            $style1 .= ' resfullcolor';
        }
        if ($planet->storePlastic <= floor($planet->resPlastic) && floor($planet->resPlastic) > 0) {
            $style2 .= ' resfullcolor';
        }
        if ($planet->storeFuel <= floor($planet->resFuel) && floor($planet->resFuel) > 0) {
            $style3 .= ' resfullcolor';
        }
        if ($planet->storeFood <= floor($planet->resFood) && floor($planet->resFood) > 0) {
            $style4 .= ' resfullcolor';
        }
        if ($planet->peoplePlace <= floor($planet->people) && floor($planet->people) > 0) {
            $style5 = ' resfullcolor';
        }
        if (floor($planet->prodPower) - floor($planet->usePower) < 0) {
            $style6 = 'respower resfullcolor';
            $power_rest = floor($planet->prodPower) - floor($planet->usePower);
        } else {
            $power_rest = floor($planet->prodPower) - floor($planet->usePower);
        }
        $rtn = "<div id=\"resbox\">
        <div id=\"resboxheader\">Resourcen</div>
        <div id=\"resboxcontent\">"
            . $this->getResourceRow($style0, ResourceNames::METAL, "images/resources/metal.png", $planet->resMetal, $planet->storeMetal, $planet->prodMetal, true)
            . $this->getResourceRow($style1, ResourceNames::CRYSTAL, "images/resources/crystal.png", $planet->resCrystal, $planet->storeCrystal, $planet->prodCrystal, true)
            . $this->getResourceRow($style2, ResourceNames::PLASTIC, "images/resources/plastic.png", $planet->resPlastic, $planet->storePlastic, $planet->prodPlastic, true)
            . $this->getResourceRow($style3, ResourceNames::FUEL, "images/resources/fuel.png", $planet->resFuel, $planet->storeFuel, $planet->prodFuel, true)
            . $this->getResourceRow($style4, ResourceNames::FOOD, "images/resources/food.png", $planet->resFood, $planet->storeFood, $planet->prodFood, true)
            . $this->getResourceRow($style5, "Bevölkerung", "images/resources/people.png", $planet->people, $planet->peoplePlace, $planet->prodPeople, true)
            . "<span class=\"respower " . $style6 . "\" " . mTT(ResourceNames::POWER, "<img src=\"images/resources/power.png\" style=\"float:left;margin-right:5px;\"/> <b>Produktion:</b> " . StringUtils::formatNumber($planet->prodPower) . "<br/><b>Verfügbar:</b> " . StringUtils::formatNumber($power_rest) . "<br/><b>Verbrauch:</b> " . StringUtils::formatNumber($planet->usePower) . "<br style=\"clear:both;\"/>") . ">" . StringUtils::formatNumber($power_rest, false, true) . "</span>
        </div>
        </div>";

        return $rtn;
    }

    private function getResourceRow(string $style, string $title, string $icon, float $amount, int $store, float $production, bool $shortAmount = false): string
    {
        return sprintf(
            $shortAmount ? '<span class="%s" %s>%s</span>' : '<td class="%s" %s>%s</td>',
            $style,
            $this->getResourceTooltip($title, $icon, $amount, $store, $production),
            $shortAmount ? StringUtils::formatNumber($amount, false, true) : StringUtils::formatNumber(floor($amount))
        );
    }

    private function getResourceTooltip(string $title, string $icon, float $amount, int $store, float $production): string
    {
        $remainingStore = $store - $amount;
        $storeFullMessage = '';
        if ($production > 0 && $remainingStore > 0 && $title != 'Bevölkerung') {
            $storeFullMessage = sprintf('<br><b>Voll in:</b> %s', StringUtils::formatTimespan(($remainingStore / $production) * 3600));
        }

        return mTT(
            $title,
            sprintf(
                '<img width="40px" height="40px" src="%s" style="float:left;margin-right:5px;"/> <b>Vorhanden:</b> %s<br/><b>Speicher:</b> %s%s<br style=\"clear:both;\"/>',
                $icon,
                StringUtils::formatNumber($amount),
                StringUtils::formatNumber($store),
                $storeFullMessage
            )
        );
    }
}
