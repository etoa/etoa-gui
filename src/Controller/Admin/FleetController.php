<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Fleet;
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
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FleetController extends AbstractAdminController
{
    public function __construct(
        private readonly FleetRepository      $fleetRepository,
        private readonly FleetService         $fleetService,
        private readonly PlanetRepository     $planetRepository,
        private readonly ConfigurationService $config,
        private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/admin/fleets/', name: 'admin.fleets')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function fleets(Request $request): Response
    {
        return $this->render('admin/fleet/search.html.twig', [
            'form' => $this->createForm(FleetSearchType::class, $request->query->all()),
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
            $this->entityManager->persist($fleet);
            $this->entityManager->flush();

            foreach ($fleet->ships as $ship) {
                $this->fleetRepository->addShipsToFleet($fleet->getId(), $ship['shipId'], $ship['count']);
            }

            $this->addFlash('success', 'Flotte erstellt!');

            $this->redirectToRoute('admin.fleets.edit', ['id' => $fleet->getId()]);
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
            $shipIds = array_map(fn(array $row) => $row['shipId'], $fleet->ships);
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
                        $this->fleetRepository->addShipsToFleet($fleet->getId(), $ship['shipId'], $toBeAdded);
                    }
                    unset($shipMap[$ship['shipId']]);
                }

                foreach (array_keys($shipMap) as $shipId) {
                    $this->fleetRepository->removeShipsFromFleet($fleet->getId(), $shipId);
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

    #[Route('/admin/fleets/options', name: 'admin.fleets.options')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function options(Request $request): Response
    {
        if ($request->request->has('flightban_deactivate')) {
            $this->config->set('flightban', 0, '');
            $this->addFlash('success', 'Flottensperre deaktiviert');
        }

        // Flottensperre aktivieren
        if ($request->request->has('flightban_activate') || $request->request->has('flightban_update')) {
            $flightBanFrom = strtotime($request->request->get('flightban_time_from'));
            $flightBanTo = strtotime($request->request->get('flightban_time_to'));

            if ($flightBanFrom < $flightBanTo) {
                $this->config->set('flightban', 1, $request->request->get('flightban_reason'));
                $this->config->set('flightban_time', '', $flightBanFrom, $flightBanTo);

                $this->addFlash('success', 'Flottensperre aktualisiert!');
            } else {
                $this->addFlash('error', "Das Ende muss nach dem Start erfolgen!");
            }
        }

        // Kampfsperre deaktivieren
        if ($request->request->has('battleban_deactivate')) {
            $this->config->set('battleban', 0, '');

            $this->addFlash('success', 'Kampfsperre deaktiviert');
        }

        // Kampfsperre aktivieren
        if ($request->request->has('battleban_activate') || $request->request->has('battleban_update')) {
            $battleBanFrom = strtotime($request->request->get('battleban_time_from'));
            $battleBanTo = strtotime($request->request->get('battleban_time_to'));

            if ($battleBanFrom < $battleBanTo) {
                $this->config->set('battleban', 1, $request->request->get('battleban_reason'));
                $this->config->set('battleban_time', '', $battleBanFrom, $battleBanTo);
                $this->config->set('battleban_arrival_text', '', $request->request->get('battleban_arrival_text_fleet'), $request->request->get('battleban_arrival_text_missiles'));

                $this->addFlash('success', 'Kampfsperre aktualisiert');
            } else {
                $this->addFlash('error', "Das Ende muss nach dem Start erfolgen!");
            }
        }

        if ($this->config->getBoolean('flightban')) {
            if ($this->config->param1Int('flightban_time') <= time() && $this->config->param2Int('flightban_time') >= time()) {
                $flightBanTimeStatus = "Sie wirkt zum jetzigen Zeitpunkt!";
            } elseif ($this->config->param1Int('flightban_time') > time() && $this->config->param2Int('flightban_time') > time()) {
                $flightBanTimeStatus = "Sie wirkt erst ab: " . date("d.m.Y H:i", $this->config->param1Int('flightban_time')) . "!";
            } else {
                $flightBanTimeStatus = "Sie ist nun aber abgelaufen!";
            }

            $flightBanStatus = "<div style=\"color:#f90\">Die Flottensperre ist aktiviert! " . $flightBanTimeStatus . "</div>";
            $flightBanTimeFrom = $this->config->param1Int('flightban_time');
            $flightBanTimeTo = $this->config->param2Int('flightban_time');
        } else {
            $flightBanStatus = "<div style=\"color:#0f0\">Die Flottensperre ist deaktiviert!</div>";
            $flightBanTimeFrom = time();
            $flightBanTimeTo = time() + 3600;
        }

        if ($this->config->getBoolean('battleban')) {
            if ($this->config->param1Int('battleban_time') <= time() && $this->config->param2Int('battleban_time') >= time()) {
                $battleban_time_status = "Sie wirkt zum jetzigen Zeitpunkt!";
            } elseif ($this->config->param1Int('battleban_time') > time() && $this->config->param2Int('battleban_time') > time()) {
                $battleban_time_status = "Sie wirkt erst ab: " . date("d.m.Y H:i", $this->config->param1Int('battleban_time')) . "!";
            } else {
                $battleban_time_status = "Sie ist nun aber abgelaufen!";
            }

            $battleBanStatus = "<div style=\"color:#f90\">Die Kampfsperre ist aktiviert! " . $battleban_time_status . "</div>";
            $battleBanTimeFrom = $this->config->param1Int('battleban_time');
            $battleBanTimeTo = $this->config->param2Int('battleban_time');
        } else {
            $battleBanStatus = "<div style=\"color:#0f0\">Die Kampfsperre ist deaktiviert!</div>";
            $battleBanTimeFrom = time();
            $battleBanTimeTo = time() + 3600;
        }

        return $this->render('admin/fleet/options.html.twig', [
            'flightBanReason' => $this->config->param1('flightban'),
            'flightBanActive' => $this->config->getBoolean('flightban'),
            'flightBanStatus' => $flightBanStatus,
            'flightBanTimeFrom' => $flightBanTimeFrom,
            'flightBanTimeTo' => $flightBanTimeTo,
            'battleBanReason' => $this->config->param1('battleban'),
            'battleBanActive' => $this->config->getBoolean('battleban'),
            'battleBanStatus' => $battleBanStatus,
            'battleBanTimeFrom' => $battleBanTimeFrom,
            'battleBanTimeTo' => $battleBanTimeTo,
            'battleBanFleetText' => $this->config->param1('battleban_arrival_text'),
            'battleBanMissileText' => $this->config->param2('battleban_arrival_text'),
        ]);
    }
}
