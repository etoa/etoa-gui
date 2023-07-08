<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Core\ObjectWithImage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AllianceTechnologiesController extends AdvancedGameDataCrudController
{
    #[Route('/admin/alliances/technologies/data', name: 'admin.alliances.technologies.data')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function __invoke(Request $request): Response
    {
        return $this->handleRequest($request);
    }

    protected function getName(): string
    {
        return "Allianztechnologien";
    }

    protected function getTable(): string
    {
        return "alliance_technologies";
    }

    protected function getTableId(): string
    {
        return "alliance_tech_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "alliance_tech_id";
    }

    protected function getImagePath(): ?string
    {
        return ObjectWithImage::BASE_PATH . "/atechnologies/technology<DB_TABLE_ID>_small.png";
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'alliance_tech_show',
        ];
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "alliance_tech_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => true,
            ], [
                "name" => "alliance_tech_name",
                "text" => "Name",
                "type" => "text",
                "size" => 20,
                "max_len" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ], [
                "name" => "alliance_tech_shortcomment",
                "text" => "Kurzbeschreibung",
                "type" => "textarea",
                "rows" => 7,
                "cols" => 35,
                "show_overview" => true,
                "line" => true,
            ], [
                "name" => "alliance_tech_longcomment",
                "text" => "Lange Beschreibung",
                "type" => "textarea",
                "rows" => 7,
                "cols" => 35,
                "show_overview" => false,
                "line" => true,
            ], [
                "name" => "alliance_tech_costs_metal",
                "text" => "Kosten Metall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_build_time",
                "text" => "Bauzeit (Sekunden)",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_factor",
                "text" => "Kostenfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
                "line" => true,
            ], [
                "name" => "alliance_tech_last_level",
                "text" => "Max Level",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => true,
            ], [
                "name" => "alliance_tech_needed_id",
                "text" => "Voraussetzung",
                "type" => "select",
                "def_val" => "",
                "items" => $this->getSelectElements('alliance_technologies', "alliance_tech_id", "alliance_tech_name", "alliance_tech_id"),
                "show_overview" => true,
            ], [
                "name" => "alliance_tech_needed_level",
                "text" => "Voraussetzung Stufe",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => true,
            ],
        ];
    }
}
