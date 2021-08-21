<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class TutorialsForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Tutorial Texte";
    }

    protected function getTable(): string
    {
        return "tutorial_texts";
    }

    protected function getTableId(): string
    {
        return "text_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "text_tutorial_id, text_step";
    }

    protected function getTableSort(): ?string
    {
        return 'text_step';
    }

    protected function getTableSortParent(): ?string
    {
        return 'text_tutorial_id';
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "text_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => true,
            ], [
                "name" => "text_tutorial_id",
                "text" => "Tutorial",
                "type" => "select",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "items" => $this->getSelectElements('tutorial', "tutorial_id", "tutorial_title", "tutorial_title", ["0" => "-"]),
                "show_overview" => true,
            ], [
                "name" => "text_title",
                "text" => "Titel",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => true,
                "link_in_overview" => true,
            ], [
                "name" => "text_content",
                "text" => "Inhalt",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "15",
                "cols" => "150",
                "show_overview" => false,
            ],
        ];
    }
}
