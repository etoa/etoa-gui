<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\Forms\AllianceTechnologiesForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AllianceTechnologiesController extends GameDataCrudController
{
    public function __construct(
        private readonly AllianceTechnologiesForm $allianceTechnologiesForm,
    )
    {
    }

    #[Route('/admin/alliances/technologies', name: 'admin.alliances.technology_data')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function technologies(Request $request): Response
    {
        return $this->renderContent($request, $this->allianceTechnologiesForm);
    }
}
