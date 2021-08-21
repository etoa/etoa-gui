<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class DefenseCategoriesForm extends SimpleForm
{
    protected function getName(): string
    {
        return "Verteidigungs-Kategorien";
    }

    protected function getTable(): string
    {
        return "def_cat";
    }

    protected function getTableId(): string
    {
        return "cat_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "cat_order";
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "cat_name",
                "text" => "Kategoriename",
                "type" => "text",
                "size" => 30,
                "maxlen" => 250,
            ], [
                "name" => "cat_order",
                "text" => "Sortierung",
                "type" => "numeric",
                "def_val" => "1",
            ],
        ];
    }
}
