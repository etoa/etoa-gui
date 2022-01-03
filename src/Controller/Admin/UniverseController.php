<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Universe\GalaxyChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UniverseController extends AbstractAdminController
{
    public function __construct(
        private GalaxyChecker $galaxyChecker
    ) {
    }

    #[Route("/admin/universe/check", name: "admin.universe.check")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function check(): Response
    {
        return $this->render('admin/universe/check.html.twig', [
            'planetsWithInvalidUserId' => $this->galaxyChecker->planetsWithInvalidUserId(),
            'mainPlanetsWithoutUsers' => $this->galaxyChecker->mainPlanetsWithoutUsers(),
            'usersWithInvalidNumberOfMainPlanets' => $this->galaxyChecker->usersWithInvalidNumberOfMainPlanets(),
            'invalidEntities' => $this->galaxyChecker->invalidEntities(),
        ]);
    }
}
