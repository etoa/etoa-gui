<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class TicketCategoriesForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Ticket-Kategorien";
    }

    protected function getTable(): string
    {
        return "ticket_cat";
    }

    protected function getTableId(): string
    {
        return "id";
    }

    protected function getOverviewOrderField(): string
    {
        return "sort";
    }

    protected function getTableSort(): ?string
    {
        return 'sort';
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "name",
                "text" => "Name",
                "type" => "text",
                "def_val" => "",
                "size" => "40",
                "maxlen" => "100",
                "rows" => "10",
                "cols" => "40",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
            ], [
                "name" => "sort",
                "text" => "Sortierung",
                "type" => "numeric",
                "def_val" => "1",
                "size" => "1",
                "maxlen" => "2",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
            ],
        ];
    }
}
