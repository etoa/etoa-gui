<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ShipTypesController extends SimpleGameDataCrudController
{
    #[Route('/admin/ships/types', name: 'admin.ships.types')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function __invoke(Request $request): Response
    {
        return $this->handleRequest($request);
    }

    public function getName(): string
    {
        return "Schiff-Kategorien";
    }

    protected function getTable(): string
    {
        return "ship_cat";
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
                "max_len" => 250,
            ], [
                "name" => "cat_order",
                "text" => "Sortierung",
                "type" => "numeric",
                "def_val" => "1",
            ], [
                "name" => "cat_color",
                "text" => "Farbe",
                "type" => "color",
                "def_val" => "#ffffff",
            ],
        ];
    }
}
