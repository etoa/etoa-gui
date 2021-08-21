<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class ObjectTransformsForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Objekt-Transformationen";
    }

    protected function getTable(): string
    {
        return "obj_transforms";
    }

    protected function getTableId(): string
    {
        return "id";
    }

    protected function getOverviewOrderField(): string
    {
        return "id";
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "def_id",
                "text" => "Verteidigung",
                "type" => "select",
                "items" => $this->getSelectElements('defense', "def_id", "def_name", "def_order"),
                "show_overview" => true,
                "link_in_overview" => true,
            ], [
                "name" => "ship_id",
                "text" => "Schiff",
                "type" => "select",
                "items" => $this->getSelectElements('ships', "ship_id", "ship_name", "ship_order"),
                "show_overview" => true,
            ], [
                "name" => "costs_metal",
                "text" => "Kosten Metall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "costs_food",
                "text" => "Kosten Nahrung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "costs_factor_sd",
                "text" => "Kostenfaktor S -> V",
                "type" => "decimal",
                "def_val" => "0.0",
                "show_overview" => true,
            ], [
                "name" => "costs_factor_ds",
                "text" => "Kostenfaktor V -> S",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => true,
            ], [
                "name" => "num_def",
                "text" => "Anzahl V pro S",
                "type" => "numeric",
                "def_val" => "1",
                "show_overview" => true,
            ],
        ];
    }
}
