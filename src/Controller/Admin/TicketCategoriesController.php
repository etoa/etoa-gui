<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TicketCategoriesController extends SimpleGameDataCrudController
{
    #[Route('/admin/tickets/categories', name: 'admin.ticket.categories')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function __invoke(Request $request): Response
    {
        return $this->handleRequest($request);
    }

    public function getName(): string
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
                "max_len" => 100,
            ], [
                "name" => "sort",
                "text" => "Sortierung",
                "type" => "numeric",
                "def_val" => "1",
            ],
        ];
    }
}
