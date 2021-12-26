<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\Alliance;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Form\Type\Admin\AllianceCreateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllianceController extends AbstractAdminController
{

    public function __construct(
        private AllianceService $allianceService
    ){
    }

    #[Route('/admin/alliances/', name: 'admin.alliances')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function search()
    {

    }

    #[Route('/admin/alliances/new', name: 'admin.alliances.new')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(AllianceCreateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            try {
                $alliance = $this->allianceService->create(
                    $data['tag'],
                    $data['name'],
                    (int)$data['founder'],
                );

                $this->addFlash('success', sprintf('Alliance %s erstellt', $alliance->nameWithTag));

                return $this->redirectToRoute('admin.alliances');
            } catch (InvalidAllianceParametersException $ex) {
                $this->addFlash('error', "Allianz konnte nicht erstellt werden!\n\n" . $ex->getMessage() . "");
            }
        }

        return $this->render('admin/alliance/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
