<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class TicketCategoriesForm extends SimpleForm
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

    protected function getFields(): array
    {
        return [
            [
                "name" => "name",
                "text" => "Name",
                "type" => "text",
                "size" => 40,
                "maxlen" => 100,
            ], [
                "name" => "sort",
                "text" => "Sortierung",
                "type" => "numeric",
                "def_val" => "1",
            ],
        ];
    }
}
