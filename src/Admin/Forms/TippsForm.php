<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class TippsForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Tipps";
    }

    protected function getTable(): string
    {
        return "tips";
    }

    protected function getTableId(): string
    {
        return "tip_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "tip_text";
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'tip_active',
        ];
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "tip_text",
                "text" => "Tipp",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "10",
                "cols" => "40",
                "show_overview" => 1,
            ],
        ];
    }
}
