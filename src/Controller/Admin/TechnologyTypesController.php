<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TechnologyTypesController extends SimpleGameDataCrudController
{
    #[Route('/admin/technologies/types', name: 'admin.technologies.types')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function __invoke(Request $request): Response
    {
        return $this->handleRequest($request);
    }

    public function getName(): string
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
                "max_len" => 250,
            ], [
                "name" => "type_order",
                "text" => "Sortierung",
                "type" => "numeric",
                "def_val" => "1",
            ],
        ];
    }
}
