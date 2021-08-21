<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class TechnologyTypesForm extends SimpleForm
{
    protected function getName(): string
    {
        return "Technoligiekategorien";
    }

    protected function getTable(): string
    {
        return "tech_types";
    }

    protected function getTableId(): string
    {
        return "type_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "type_order";
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "type_name",
                "text" => "Kategoriename",
                "type" => "text",
                "size" => 30,
                "maxlen" => 250,
            ], [
                "name" => "type_order",
                "text" => "Sortierung",
                "type" => "numeric",
                "def_val" => "1",
            ],
        ];
    }
}
