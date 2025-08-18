<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Backend\BackendMessageService;
use EtoA\Entity\Entity;
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
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\WormholeRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EntityController extends AbstractAdminController
{
    public function __construct(
        private readonly StarRepository        $starRepository,
        private readonly WormholeRepository    $wormholeRepository,
        private readonly AsteroidRepository    $asteroidRepository,
        private readonly NebulaRepository      $nebulaRepository,
        private readonly PlanetRepository      $planetRepository,
        private readonly PlanetService         $planetService,
        private readonly LogRepository         $logRepository,
        private readonly BackendMessageService $backendMessageService
    )
    {
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
    public function edit(Request $request, ?Entity $entity = null): Response
    {
        if ($entity === null) {
            $this->addFlash('error', 'Entity nicht vorhanden');

            return $this->redirectToRoute('admin.universe.entities');
        }

        $form = null;
        switch ($entity->getCode()) {
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

    private function handleStar(Request $request, Entity $entity): FormInterface
    {
        $star = $entity->getStar();
        $form = $this->createForm(EditStartType::class, $star);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->starRepository->save();

            $this->addFlash('success', 'Änderungen übernommen');
        }

        return $form;
    }

    private function handleEmptySpace(Entity $entity): FormInterface
    {
        $emptySpace = $entity->getEmptySpace();

        return $this->createForm(EditEmptySpaceType::class, $emptySpace);
    }

    private function handleWormhole(Request $request, Entity $entity): FormInterface
    {
        $wormhole = $entity->getWormhole();
        $form = $this->createForm(EditWormholeType::class, $wormhole);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->wormholeRepository->save();

            $this->addFlash('success', 'Änderungen übernommen');
        }

        return $form;
    }

    private function handleAsteroid(Request $request, Entity $entity): FormInterface
    {
        $asteroid = $entity->getAsteroid();
        $form = $this->createForm(EditAsteroidType::class, $asteroid);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->asteroidRepository->save();

            $this->addFlash('success', 'Änderungen übernommen');
        }

        return $form;
    }

    private function handleNebula(Request $request, Entity $entity): FormInterface
    {
        $nebula = $entity->getNebula();
        $form = $this->createForm(EditNebualType::class, $nebula);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->nebulaRepository->save();

            $this->addFlash('success', 'Änderungen übernommen');
        }

        return $form;
    }

    private function handlePlanet(Request $request, Entity $entity): FormInterface
    {
        $planet = $entity->getPlanet();
        $form = $this->createForm(EditPlanetType::class, $planet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('resetUserChanged')->getData()) {
                $planet->setUserChanged(0);
            }

            $changeset = $this->planetRepository->getChangeset($planet);

            if (array_key_exists('mainPlanet',$changeset) && $planet->isMainPlanet()) {
                $this->planetRepository->setMain($planet);
                $this->addFlash('success', "Hauptplanet gesetzt; ursprüngliche Hautpplanet-Zuordnung entfernt!");

            } elseif (array_key_exists('mainPlanet',$changeset) && !$planet->isMainPlanet()) {
                $this->planetRepository->unsetMain($planet);
                $this->addFlash('success', "Hauptplanet-Zuordnung entfernt. Denke daran, einen neuen Hautplanet festzulegen!");
            }

            if (array_key_exists('user',$changeset)) {
                $this->planetService->changeOwner($planet);

                if (!$planet->getUser()) {
                    $this->planetRepository->reset($planet);
                }

                //Log Schreiben
                $this->logRepository->add(LogFacility::GALAXY, LogSeverity::INFO, $this->getUser()->getUsername() . " wechselt den Besitzer vom Planeten: [page galaxy sub=edit id=" . $planet->getId() . "][B]" . $planet->getId() . "[/B][/page]
Alter Besitzer: [page=".$this->generateUrl('admin.users.edit',['id'=>$changeset['user'][0]?->getId()])."][B]" . $changeset['user'][0]?->getId() . "[/B][/page]
Neuer Besitzer: [page=".$this->generateUrl('admin.users.edit',['id'=>$planet->getUser()?->getId()])."][B]" . $planet->getUser()?->getId() . "[/B][/page]");

                $this->addFlash('success', "Der Planet wurde dem User mit der ID: " . $planet->getUser()->getId() . " übergeben!");
            }
            $this->planetRepository->save();
            $this->addFlash('success', 'Änderungen übernommen');
        }

        return $form;
    }
}
