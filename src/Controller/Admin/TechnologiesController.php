<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Doctrine\DBAL\Connection;
use EtoA\Core\ObjectWithImage;
use EtoA\Ranking\RankingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TechnologiesController extends AdvancedGameDataCrudController
{
    public function __construct(
        Connection                      $connection,
        string                          $projectDir,
        private readonly RankingService $rankingService,
    )
    {
        parent::__construct($connection, $projectDir);
    }

    #[Route('/admin/technologies/data', name: 'admin.technologies.data')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function __invoke(Request $request): Response
    {
        return $this->handleRequest($request);
    }

    public function getName(): string
    {
        return "Forschung";
    }

    protected function getTable(): string
    {
        return "technologies";
    }

    protected function getTableId(): string
    {
        return "tech_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "tech_type_id, tech_order,tech_name";
    }

    protected function getTableSort(): ?string
    {
        return 'tech_order';
    }

    protected function getTableSortParent(): ?string
    {
        return 'tech_type_id';
    }

    protected function getImagePath(): ?string
    {
        return ObjectWithImage::BASE_PATH . "/technologies/technology<DB_TABLE_ID>_small.png";
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'tech_show',
        ];
    }

    protected function runPostInsertUpdateHook(): string
    {
        $numTechnologies = $this->rankingService->calcTechPoints();

        return sprintf("Die Punkte von %s Technologien wurden aktualisiert!", $numTechnologies);
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "tech_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => true,
            ],
            [
                "name" => "tech_name",
                "text" => "Name",
                "type" => "text",
                "size" => 20,
                "max_len" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ],
            [
                "name" => "tech_type_id",
                "text" => "Kategorie",
                "type" => "select",
                "items" => $this->getSelectElements('tech_types', "type_id", "type_name", "type_name"),
                "show_overview" => true,
            ],
            [
                "name" => "tech_shortcomment",
                "text" => "Kurzbeschrieb",
                "type" => "textarea",
                "rows" => 5,
                "cols" => 50,
                "show_overview" => true,
            ],
            [
                "name" => "tech_longcomment",
                "text" => "Beschreibung",
                "type" => "textarea",
                "rows" => 7,
                "cols" => 50,
                "show_overview" => false,
                "line" => true,
            ],
            [
                "name" => "tech_costs_metal",
                "text" => "Kosten Metall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "tech_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "tech_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "tech_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "tech_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "tech_build_costs_factor",
                "text" => "Kostenfaktor Bau",
                "type" => "decimal",
                "def_val" => "0.00",
                "show_overview" => false,
                "line" => true,
            ],
            [
                "name" => "tech_last_level",
                "text" => "Max Level",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => true,
            ],
            [
                "name" => "tech_stealable",
                "text" => "Stehlbar",
                "type" => "radio",
                "def_val" => "1",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => false,
            ],
        ];
    }
}
