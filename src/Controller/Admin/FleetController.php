<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Fleet\Fleet;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSendRequest;
use EtoA\Fleet\FleetService;
use EtoA\Fleet\FleetStatus;
use EtoA\Fleet\FleetWithShips;
use EtoA\Fleet\InvalidFleetParametersException;
use EtoA\Form\Type\Admin\FleetSearchType;
use EtoA\Form\Type\Admin\FleetType;
use EtoA\Form\Type\Admin\SendShipsType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FleetController extends AbstractAdminController
{
    public function __construct(
        private FleetRepository $fleetRepository,
        private FleetService $fleetService,
        private PlanetRepository $planetRepository,
    ) {
    }

    #[Route('/admin/fleets/', name: 'admin.fleets')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function fleets(Request $request): Response
    {
        return $this->render('admin/fleet/search.html.twig', [
            'form' => $this->createForm(FleetSearchType::class, $request->query->all())->createView(),
            'total' => $this->fleetRepository->count(),
        ]);
    }

    #[Route('/admin/fleets/new', name: 'admin.fleets.new')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function new(Request $request): Response
    {
        $fleet = new FleetWithShips(Fleet::empty(), [0 => 1]);
        $form = $this->createForm(FleetType::class, $fleet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resources = new BaseResources();
            $resources->metal = $fleet->resMetal;
            $resources->crystal = $fleet->resCrystal;
            $resources->plastic = $fleet->resPlastic;
            $resources->fuel = $fleet->resFuel;
            $resources->food = $fleet->resFood;
            $resources->people = $fleet->resPeople;

            $fetch = new BaseResources();
            $fetch->metal = $fleet->fetchMetal;
            $fetch->crystal = $fleet->fetchCrystal;
            $fetch->plastic = $fleet->fetchPlastic;
            $fetch->fuel = $fleet->fetchFuel;
            $fetch->food = $fleet->fetchFood;
            $fetch->people = $fleet->fetchPeople;

            $fleetId = $this->fleetRepository->add(
                $fleet->userId,
                $fleet->launchTime,
                $fleet->landTime,
                $fleet->entityFrom,
                $fleet->entityTo,
                $fleet->action,
                $fleet->status,
                $resources,
                $fetch,
                $fleet->pilots,
                $fleet->usageFuel,
                $fleet->usageFood,
                $fleet->usagePower,
                $fleet->leaderId,
                $fleet->nextId,
                $fleet->nextActionTime,
                $fleet->supportUsageFuel,
                $fleet->supportUsageFood
            );

            foreach ($fleet->ships as $ship) {
                $this->fleetRepository->addShipsToFleet($fleetId, $ship['shipId'], $ship['count']);
            }

            $this->addFlash('success', 'Flotte erstellt!');

            $this->redirectToRoute('admin.fleet.edit', ['id' => $fleetId]);
        }

        return $this->render('admin/fleet/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/fleets/{id}/edit', name: 'admin.fleets.edit')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function edit(Request $request, int $id): Response
    {
        if ($request->request->has('cancel')) {
            try {
                $this->fleetService->cancel($id);

                $this->addFlash('success', "Flug abgebrochen!");
            } catch (InvalidFleetParametersException $ex) {
                $this->addFlash('error', "Kann Flotte nicht abbrechen: " . $ex->getMessage());
            }

            return $this->redirectToRoute('admin.fleets.edit', ['id' => $id]);
        }

        if ($request->request->has('return')) {
            try {
                $this->fleetService->cancel($id, true);

                $this->addFlash('success', "Flotte zurück gesendet!");
            } catch (InvalidFleetParametersException $ex) {
                $this->addFlash('error', "Kann Flotte nicht zurücksenden: " . $ex->getMessage());
            }

            return $this->redirectToRoute('admin.fleets.edit', ['id' => $id]);
        }

        if ($request->request->has('land')) {
            try {
                $this->fleetService->land($id);
                $this->addFlash('success', "Flotte gelandet!");
            } catch (InvalidFleetParametersException $ex) {
                $this->addFlash('error', "Kann Flotte nicht landen: " . $ex->getMessage());
            }

            return $this->redirectToRoute('admin.fleets');
        }

        $fleetShips = $this->fleetRepository->findAllShipsInFleet($id);
        $fleet = new FleetWithShips($this->fleetRepository->find($id), $fleetShips);
        $form = $this->createForm(FleetType::class, $fleet);
        $form->handleRequest($request);
        if ($request->request->has('edit')) {
            $shipIds = array_map(fn (array $row) => $row['shipId'], $fleet->ships);
            if (count($shipIds) !== count(array_unique($shipIds))) {
                $form->get('ships')->addError(new FormError('Nur ein Eintrag pro Shiff möglich'));
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $shipMap = [];
                foreach ($fleetShips as $ship) {
                    $shipMap[$ship->shipId] = $ship->count;
                }

                foreach ($fleet->ships as $ship) {
                    $toBeAdded = $ship['count'] - ($shipMap[$ship['shipId']] ?? 0);
                    if ($toBeAdded !== 0) {
                        $this->fleetRepository->addShipsToFleet($fleet->id, $ship['shipId'], $toBeAdded);
                    }
                    unset($shipMap[$ship['shipId']]);
                }

                foreach (array_keys($shipMap) as $shipId) {
                    $this->fleetRepository->removeShipsFromFleet($fleet->id, $shipId);
                }

                $this->fleetRepository->save($fleet);

                $this->addFlash('success', 'Flottendaten geändert!');
            }
        }

        return $this->render('admin/fleet/edit.html.twig', [
            'form' => $form->createView(),
            'fleet' => $fleet,
        ]);
    }

    #[Route('/admin/fleets/{id}/delete', name: 'admin.fleets.delete')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function delete(int $id): RedirectResponse
    {
        $this->fleetRepository->removeAllShipsFromFleet($id);
        $this->fleetRepository->remove($id);

        $this->addFlash('success', "Die Flotte wurde gelöscht!");

        return $this->redirectToRoute('admin.fleets');
    }

    #[Route('/admin/fleets/send-ships', name: 'admin.fleets.send-ships')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function sendShips(Request $request): Response
    {
        $sendShips = FleetSendRequest::new();
        $form = $this->createForm(SendShipsType::class, $sendShips);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $count = 0;
            foreach ($this->planetRepository->getMainPlanets() as $planet) {
                $fleetId = $this->fleetRepository->add(
                    $planet->userId,
                    $sendShips->launchTime,
                    $sendShips->landTime,
                    $sendShips->entityFrom,
                    $planet->id,
                    FleetAction::FLIGHT,
                    FleetStatus::ARRIVAL,
                    new BaseResources()
                );

                $this->fleetRepository->addShipsToFleet(
                    $fleetId,
                    $sendShips->shipId,
                    $sendShips->count,
                );
                $count++;
            }

            $this->addFlash('success', "$count Flotten erstellt!");
        }

        return $this->render('admin/fleet/send-ships.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
