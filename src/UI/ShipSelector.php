<?php

declare(strict_types=1);

namespace EtoA\UI;

use EtoA\Ship\ShipDataRepository;

class ShipSelector
{
    private ShipDataRepository $shipDataRepository;

    public function __construct(
        ShipDataRepository $shipDataRepository
    ) {
        $this->shipDataRepository = $shipDataRepository;
    }

    public function getHTML(string $name, int $shipId = 0, bool $showEmptyOption = true): string
    {
        $str = "<select name=\"" . $name . "\">";
        if ($showEmptyOption) {
            $str .= "<option value=\"\" style=\"font-style:italic\">(Schiff wählen...)</option>";
        }
        foreach ($this->shipDataRepository->getShipNames(true) as $id => $label) {
            $str .= "<option value=\"$id\"";
            if ($id === $shipId) {
                $str .= " selected=\"selected\"";
            }
            $str .= ">" . $label . "</option>";
        }
        $str .= "</select>";

        return $str;
    }
}
