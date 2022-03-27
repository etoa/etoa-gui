<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Backend\BackendMessageService;
use EtoA\Form\Type\Admin\EditAsteroidType;
use EtoA\Form\Type\Admin\EditEmptySpaceType;
use EtoA\Form\Type\Admin\EditNebualType;
use EtoA\Form\Type\Admin\EditPlanetType;
use EtoA\Form\Type\Admin\EditStartType;
use EtoA\Form\Type\Admin\EditWormholeType;
use EtoA\Form\Type\Admin\EntitySearchType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Universe\Asteroid\AsteroidRepository;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\WormholeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntityController extends AbstractAdminController
{
    public function __construct(
        private EntityRepository $entityRepository,
        private StarRepository $starRepository,
        private EmptySpaceRepository $emptySpaceRepository,
        private WormholeRepository $wormholeRepository,
        private AsteroidRepository $asteroidRepository,
        private NebulaRepository $nebulaRepository,
        private PlanetRepository $planetRepository,
        private PlanetService $planetService,
        private LogRepository $logRepository,
        private BackendMessageService $backendMessageService
    ) {
    }

    #[Route('/admin/universe/entities', name: 'admin.universe.entities')]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function search(Request $request): Response
    {
        return $this->render('admin/universe/entities.html.twig', [
            'form' => $this->createForm(EntitySearchType::class, $request->query->all()),
        ]);
    }

    #[Route('/admin/universe/planets/{id}/calculate', name: 'admin.universe.planet.calculate')]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function calculatePlanet(int $id): Response
    {
        $planet = $this->planetRepository->find($id);
        if ($planet !== null) {
            $this->backendMessageService->updatePlanet($id);

            $this->addFlash('success', "Resourcen werden neu berechnet");
            sleep(2);
        }

        return $this->redirectToRoute('admin.universe.entity', ['id' => $id]);
    }

    #[Route('/admin/universe/entities/{id}', name: 'admin.universe.entity')]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function edit(Request $request, int $id): Response
    {
        $entity = $this->entityRepository->searchEntityLabel(EntitySearch::create()->id($id));
        if ($entity === null) {
            $this->addFlash('error', 'Entity nicht vorhanden');

            return $this->redirectToRoute('admin.universe.entities');
        }

        $form = null;
        switch ($entity->code) {
            case EntityType::STAR:
                $form = $this->handleStar($request, $entity);

                break;
            case EntityType::EMPTY_SPACE:
                $form = $this->handleEmptySpace($entity);

                break;
            case EntityType::WORMHOLE:
                $form = $this->handleWormhole($request, $entity);

                break;
            case EntityType::ASTEROID:
                $form = $this->handleAsteroid($request, $entity);

                break;
            case EntityType::NEBULA:
                $form = $this->handleNebula($request, $entity);

                break;
            case EntityType::PLANET:
                $form = $this->handlePlanet($request, $entity);

                break;
        }


        return $this->render('admin/universe/edit-entity.html.twig', [
            'form' => $form?->createView(),
            'entity' => $entity,
        ]);
    }

    private function handleStar(Request $request, EntityLabel $entity): FormInterface
    {
        $star = $this->starRepository->find($entity->id);
        $form = $this->createForm(EditStartType::class, $star);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->starRepository->update($star->id, $star->name, $star->typeId);

            $this->addFlash('success', 'Änderungen übernommen');
        }

        return $form;
    }

    private function handleEmptySpace(EntityLabel $entity): FormInterface
    {
        $emptySpace = $this->emptySpaceRepository->find($entity->id);

        return $this->createForm(EditEmptySpaceType::class, $emptySpace);
    }

    private function handleWormhole(Request $request, EntityLabel $entity): FormInterface
    {
        $wormhole = $this->wormholeRepository->find($entity->id);
        $form = $this->createForm(EditWormholeType::class, $wormhole);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->wormholeRepository->setPersistent($wormhole->id, $wormhole->persistent);

            $this->addFlash('success', 'Änderungen übernommen');
        }

        return $form;
    }

    private function handleAsteroid(Request $request, EntityLabel $entity): FormInterface
    {
        $asteroid = $this->asteroidRepository->find($entity->id);
        $form = $this->createForm(EditAsteroidType::class, $asteroid);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->asteroidRepository->update($asteroid->id, $asteroid->resMetal, $asteroid->resCrystal, $asteroid->resPlastic, $asteroid->resFuel, $asteroid->resFood, $asteroid->resPower);

            $this->addFlash('success', 'Änderungen übernommen');

            $form = $this->createForm(EditAsteroidType::class, $asteroid); // Recreate form to refresh resource values
        }

        return $form;
    }

    private function handleNebula(Request $request, EntityLabel $entity): FormInterface
    {
        $nebula = $this->nebulaRepository->find($entity->id);
        $form = $this->createForm(EditNebualType::class, $nebula);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->nebulaRepository->update($nebula->id, $nebula->resMetal, $nebula->resCrystal, $nebula->resPlastic, $nebula->resFuel, $nebula->resFood, $nebula->resPower);

            $this->addFlash('success', 'Änderungen übernommen');

            $form = $this->createForm(EditNebualType::class, $nebula); // Recreate form to refresh resource values
        }

        return $form;
    }

    private function handlePlanet(Request $request, EntityLabel $entity): FormInterface
    {
        $planet = $this->planetRepository->find($entity->id);
        $wasMainPlanet = $planet->mainPlanet;
        $previousPlanetUserId = $planet->userId;
        $form = $this->createForm(EditPlanetType::class, $planet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->planetRepository->update($planet);
            if ((bool) $form->get('resetUserChanged')->getData()) {
                $this->planetRepository->resetUserChanged($planet->id);
            }

            if ($wasMainPlanet && !$planet->mainPlanet) {
                if ($this->planetRepository->setMain($planet->id, $planet->userId)) {
                    $this->addFlash('success', "Hauptplanet gesetzt; ursprüngliche Hautpplanet-Zuordnung entfernt!");
                }
            } elseif (!$wasMainPlanet && $planet->mainPlanet) {
                if ($this->planetRepository->unsetMain($planet->id)) {
                    $this->addFlash('success', "Hauptplanet-Zuordnung entfernt. Denke daran, einen neuen Hautplanet festzulegen!");
                }
            }

            if ($planet->userId !== $previousPlanetUserId) {
                $this->planetService->changeOwner($planet->id, $planet->userId);

                if ($planet->userId === 0) {
                    $this->planetRepository->reset($planet->id);
                }

                //Log Schreiben
                $this->logRepository->add(LogFacility::GALAXY, LogSeverity::INFO, $this->getUser()->getUsername() . " wechselt den Besitzer vom Planeten: [page galaxy sub=edit id=" . $planet->id . "][B]" . $planet->id . "[/B][/page]
Alter Besitzer: [page user sub=edit user_id=" . $previousPlanetUserId . "][B]" . $previousPlanetUserId . "[/B][/page]
Neuer Besitzer: [page user sub=edit user_id=" . $planet->userId . "][B]" . $planet->userId . "[/B][/page]");

                $this->addFlash('success', "Der Planet wurde dem User mit der ID: " . $planet->userId . " übergeben!");
            }

            $this->addFlash('success', 'Änderungen übernommen');

            $form = $this->createForm(EditPlanetType::class, $planet); // Recreate form to refresh resource values
        }

        return $form;
    }
}
