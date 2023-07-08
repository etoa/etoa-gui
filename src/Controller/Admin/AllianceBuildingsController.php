<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\Forms\AllianceBuildingsForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AllianceBuildingsController extends GameDataCrudController
{
    public function __construct(
        private readonly AllianceBuildingsForm $allianceBuildingsForm,
    )
    {
    }

    #[Route('/admin/alliances/buildings', name: 'admin.alliances.building_data')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function buildings(Request $request): Response
    {
        return $this->renderContent($request, $this->allianceBuildingsForm);
    }
}
