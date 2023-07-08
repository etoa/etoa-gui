<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TippsController extends AdvancedGameDataCrudController
{
    #[Route('/admin/tipps', name: 'admin.tipps')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function __invoke(Request $request): Response
    {
        return $this->handleRequest($request);
    }

    public function getName(): string
    {
        return "Tipps";
    }

    protected function getTable(): string
    {
        return "tips";
    }

    protected function getTableId(): string
    {
        return "tip_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "tip_text";
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'tip_active',
        ];
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "tip_text",
                "text" => "Tipp",
                "type" => "textarea",
                "rows" => 10,
                "cols" => 40,
                "show_overview" => true,
                "link_in_overview" => true,
            ],
        ];
    }
}
