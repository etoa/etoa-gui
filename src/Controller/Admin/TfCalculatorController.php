<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Entity\Entity;
use EtoA\Form\Type\Admin\TFCalculatorType;
use EtoA\Log\DebrisLogRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TfCalculatorController extends AbstractAdminController
{
    public function __construct(
        private readonly PlanetRepository    $planetRepository,
        private readonly DebrisLogRepository $debrisLogRepository
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
            $failed = [];
            $split = 0;

            foreach ($form->getData()['planets'] as $planetData) {
                /** @var Entity $entity the form only offers planets that have an owner */
                $entity = $planetData['planet'];

                // getOwner() is the isset-safe way in; Entity::$planet is a typed
                // non-nullable property and stays uninitialised for non-planets
                $owner = $entity->getOwner();
                if ($owner === null) {
                    continue;
                }
                $planet = $entity->getPlanet();

                $resource = new BaseResources();
                $resource->metal = (int) $planetData['metal'];
                $resource->crystal = (int) $planetData['crystal'];
                $resource->plastic = (int) $planetData['plastic'];

                // the owner pays their share of the debris field, as the xajax version did
                if (!$this->planetRepository->removeResources($planet, $resource)) {
                    $failed[] = $owner->getNick() . ' (' . $entity->coordinatesString() . ')';
                    continue;
                }

                $this->debrisLogRepository->add($this->getUser()->getData(), $owner, $resource);
                $split++;
            }

            if ($failed !== []) {
                $this->addFlash('error', "Nicht genug Ressourcen vorhanden bei: " . implode(', ', $failed));
            }
            if ($split > 0) {
                $this->addFlash('success', "Trümmerfeld aufgeteilt!");
            }

            return $this->redirectToRoute('admin.tf-calculator');
        }

        return $this->render('admin/tf-calculator/view.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
