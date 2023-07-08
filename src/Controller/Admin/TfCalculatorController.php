<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\TFCalculatorType;
use EtoA\Log\DebrisLogRepository;
use EtoA\Market\MarketResourceRepository;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Resources\BaseResources;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TfCalculatorController extends AbstractAdminController
{
    public function __construct(
        private readonly EntityRepository         $entityRepository,
        private readonly MarketResourceRepository $marketResourceRepository,
        private readonly DebrisLogRepository      $debrisLogRepository
    )
    {
    }

    #[Route('/admin/tf-calculator', name: 'admin.tf-calculator')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function view(Request $request): Response
    {
        $form = $this->createForm(TFCalculatorType::class, ['planets' => [[], [], []]]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $market = $this->entityRepository->searchEntityLabel(EntityLabelSearch::create()->codeIn([EntityType::MARKET]));
            foreach ($form->getData()['planets'] as $planetData) {
                $entity = $this->entityRepository->searchEntityLabel(EntityLabelSearch::create()->id((int)$planetData['planet']));
                $resource = new BaseResources();
                $resource->metal = (int)$planetData['metal'];
                $resource->crystal = (int)$planetData['crystal'];
                $resource->plastic = (int)$planetData['plastic'];

                $this->marketResourceRepository->add(0, $market->id, $entity->ownerId, 0, 'Trümmerfeld', new BaseResources(), $resource);
                $this->debrisLogRepository->add($this->getUser()->getId(), $entity->ownerId, $resource->metal, $resource->crystal, $resource->plastic);
            }

            $this->addFlash('success', "Trümmerfeld aufgeteilt!");

            return $this->redirectToRoute('admin.tf-calculator');
        }

        return $this->render('admin/tf-calculator/view.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
