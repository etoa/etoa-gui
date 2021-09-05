<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Support\StringUtils;

class ShipRenderer
{
    public function tooltip(Ship $ship): string
    {
        $tt = "<div style=\"display:none;\" id=\"shiptt" . $ship->id . "\">
			<div style=\"width:450px\">
			" . $this->image($ship, "medium", "left") . "
			<div style=\"float:left;width:260px\">
			<b>$ship->name</b><br/>
			$ship->shortComment<br/><br/>
			<table style=\"width:260px;font-size:small;\">
			<tr>
			<td>Schaden:</td><td>" . StringUtils::formatNumber($ship->weapon) . "</td>
			<td>Regeneration:</td><td>" . StringUtils::formatNumber($ship->heal) . "</td>
			</tr><tr>
			<td>Schild:</td><td>" . StringUtils::formatNumber($ship->shield) . "</td>
			<td>Kapazität:</td><td>" . StringUtils::formatNumber($ship->capacity) . "</td>
			</tr><tr>
			<td>Struktur:</td><td>" . StringUtils::formatNumber($ship->structure) . "</td>
			<td>Speed:</td><td>" . StringUtils::formatNumber($ship->speed) . "</td>
			</tr>
			</table><br/>" . $this->actions($ship) . "</div>
			<br style=\"clear:both;\"/></div></div>";
        return $tt . "<span " . tt('shiptt' . $ship->id) . ">" . $ship->name . "</span>";
    }

    public function image(Ship $ship, string $type = "small", string $float = null): string
    {
        if ($float === "left")
            return "<img src=\"" . $ship->getImagePath($type) . "\" style=\"float:left;margin-right:6px;\"/>";
        if ($float === "right")
            return "<img src=\"" . $ship->getImagePath($type) . "\" style=\"float:right;\"/>";
        return "<img src=\"" . $ship->getImagePath($type) . "\" style=\"\"/>";
    }

    public function actions(Ship $ship): string
    {
        $actions = array_filter(explode(",", $ship->actions));
        $entries = [];
        foreach ($actions as $i) {
            if ($ac = \FleetAction::createFactory($i)) {
                $entries[] = $ac->__toString();
            }
        }

        return implode(', ', $entries);
    }
}
