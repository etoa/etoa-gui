<?php

namespace EtoA\Controller\Game;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Form\Type\Core\ChooseSectorSetupType;
use EtoA\Form\Type\Core\ItemSetupType;
use EtoA\Form\Type\Core\PlanetSetupType;
use EtoA\Form\Type\Core\RaceSetupType;
use EtoA\Message\MessageRepository;
use EtoA\Support\Checker;
use EtoA\Text\TextRepository;
use EtoA\UI\Tooltip;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\GalaxyMap;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSetupService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResIcons;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\Message\MessageCategoryId;
use EtoA\Support\BBCodeUtils;

class SetupController extends AbstractGameController
{
    public function __construct(
        private readonly PlanetRepository         $planetRepository,
        private readonly MessageRepository        $messageRepository,
        private readonly EntityRepository         $entityRepository,
        private readonly Checker                  $checker,
        private readonly StarRepository           $starRepository,
        private readonly PlanetTypeRepository     $planetTypeRepository,
        private readonly SolarTypeRepository      $solarTypeRepository,
        private readonly RaceDataRepository       $raceRepository,
        private readonly TextRepository           $textRepository,
        private readonly UserRepository           $userRepository,
        private readonly ConfigurationService     $configurationService,
        private readonly UserSetupService         $userSetupService,
        private readonly DefaultItemRepository    $defaultItemRepository,
    )
    {
    }
    #[Route('/game/setup/race', name: 'game.setup.race')]
    public function setupRace(Request $request): Response
    {
        if($this->getUser()->getData()->getRaceId() === 0) {
            $addForm = $this->createForm(RaceSetupType::class);
            $addForm->handleRequest($request);

            if($addForm->isSubmitted() && $addForm->isValid()) {
                if($request->request->has('race_setup') &&
                    intval($request->request->all('race_setup')['raceId']) > 0 && $this->checker->checker_verify()) {
                        $this->getUser()->getData()->setRaceId(intval($request->request->all('race_setup')['raceId']));
                        $this->userRepository->save($this->getUser()->getData());
                        return $this->redirectToRoute('game.setup.sector');
                }
                return $this->redirect($request->getUri());
            }

            $addForm->get('checker')->setData($this->checker->checker_init());

            return $this->render("game/setup/setup_race.html.twig", [
                'addForm' => $addForm->createView(),
            ]);
        }
        return $this->redirectToRoute('game.setup.sector');
    }

    #[Route('/game/setup/sector', name: 'game.setup.sector')]
    public function setupSector(): Response
    {
        if($this->getUser()->getData()->isSetup()) {
            return $this->redirectToRoute('game.setup.finished');
        }

        if($this->getUser()->getData()->getRaceId() > 0 ) {
            $sx_num = $this->config->param1Int('num_of_sectors');
            $sy_num = $this->config->param2Int('num_of_sectors');

            $sec_x_size = GalaxyMap::WIDTH / $sx_num;
            $sec_y_size = GalaxyMap::WIDTH / $sy_num;
            $xcnt = 1;
            $ycnt = 1;
            $map=[];

            for ($x = 0; $x < GalaxyMap::WIDTH; $x += $sec_x_size) {
                $ycnt = 1;
                for ($y = 0; $y < GalaxyMap::WIDTH; $y += $sec_y_size) {
                    $countStars = $this->entityRepository->countEntitiesOfCodeInSector($xcnt, $ycnt, EntityType::STAR);
                    $countPlanets = $this->entityRepository->countEntitiesOfCodeInSector($xcnt, $ycnt, EntityType::PLANET);
                    $countInhabitedPlanets = $this->planetRepository->countWithUserInSector($xcnt, $ycnt);

                    $tt = new Tooltip();
                    $tt->addTitle("Sektor $xcnt/$ycnt");
                    $tt->addText("Sternensysteme: " . $countStars);
                    $tt->addText("Planeten: " . $countPlanets);
                    $tt->addGoodCond("Bewohnte Planeten: " . $countInhabitedPlanets);
                    $tt->addComment("Klickt hier um euren Heimatplaneten in Sektor <b>" . $xcnt . "/" . $ycnt . "</b> anzusiedeln!");

                    $map[$y][$x] = "<area shape=\"rect\" coords=\"$x," . (GalaxyMap::WIDTH - $y) . "," . ($x + $sec_x_size) . "," . (GalaxyMap::WIDTH - $y - $sec_y_size) . "\" href=\"".$this->generateUrl('game.setup.planet', array('setup_sx' => $xcnt,'setup_sy'=>$ycnt)) . "\" alt=\"Sektor $xcnt / $ycnt\" " . $tt->toString() . ">\n";
                    $ycnt++;
                }
                $xcnt++;
            }

            $addForm = $this->createForm(ChooseSectorSetupType::class,[$this->checker->checker_init()]);
            return $this->render('game/setup/setup_choosesector.html.twig', [
                'map' => $map,
                'addForm' => $addForm->createView(),
            ]);
        }
        return $this->redirectToRoute('game.setup.race');
    }

    #[Route('/game/setup/planet', name: 'game.setup.planet')]
    public function setupPlanet(Request $request): Response
    {
        if($this->getUser()->getData()->getRaceId() === 0) {
            return $this->redirectToRoute('game.setup.race');
        }

        $sets = $this->defaultItemRepository->getSets();
        if($this->planetRepository->getUserMainId($this->getUser()->getId())) {
            if (count($sets) > 1) {
                return $this->redirectToRoute('game.setup.itemset');
            }
            return $this->redirectToRoute('game.setup.finished');
        }

        $sx_num = $this->configurationService->param1Int('num_of_sectors');
        $sy_num = $this->configurationService->param2Int('num_of_sectors');

        if($request->query->has('setup_sx')
            && $request->query->getInt('setup_sx') > 0
            && $request->query->has('setup_sy')
            && $request->query->getInt('setup_sy') > 0
            && $request->query->getInt('setup_sx') <= $sx_num
            && $request->query->getInt('setup_sy') <= $sy_num) {

            $planetId = $this->planetRepository->getRandomFreePlanetId(
                $request->query->getInt('setup_sx'),
                $request->query->getInt('setup_sy'),
                $this->configurationService->getInt('user_min_fields'),
                $request->query->has('filter_p') ? $request->query->getInt('filter_p') : null,
                $request->query->has('filter_s') ? $request->query->getInt('filter_s') : null
            );

            if(!$planetId) {
                $flashes = $request->getSession()->getFlashBag();
                $flashes->add(
                    'warning',
                    'Leider konnte kein geeigneter Planet in diesem Sektor gefunden werden.
                              Bitte wähle einen anderen Sektor!'
                );
                return $this->redirectToRoute('game.setup.sector');
            }

            $planet = $this->planetRepository->find($planetId);
            $planetType = $this->planetTypeRepository->find($planet->typeId);
            $entity = $this->entityRepository->findIncludeCell($planet->id);
            $starEntity = $this->entityRepository->findByCellAndPosition($entity->cellId, 0);
            $star = $this->starRepository->find($starEntity->id);
            $starType = $this->solarTypeRepository->find($star->typeId);
            $race = $this->raceRepository->getRace($this->getUser()->getData()->getRaceId());

            $stats =
            "<tr><td class=\"tbldata\">" . ResIcons::METAL . "Produktion " . ResourceNames::METAL . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->metal, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->metal, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->metal, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->metal, $race->metal, $starType->metal], true) . "</td></tr>".

            "<tr><td class=\"tbldata\">" . ResIcons::CRYSTAL . "Produktion " . ResourceNames::CRYSTAL . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->crystal, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->crystal, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->crystal, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->crystal, $race->crystal, $starType->crystal], true) . "</td></tr>".

            "<tr><td class=\"tbldata\">" . ResIcons::PLASTIC . "Produktion " . ResourceNames::PLASTIC . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->plastic, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->plastic, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->plastic, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->plastic, $race->plastic, $starType->plastic], true) . "</td></tr>".

            "<tr><td class=\"tbldata\">" . ResIcons::FUEL . "Produktion " . ResourceNames::FUEL . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->fuel, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->fuel, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->fuel, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->fuel, $race->fuel, $starType->fuel], true) . "</td></tr>".

            "<tr><td class=\"tbldata\">" . ResIcons::FOOD . "Produktion " . ResourceNames::FOOD . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->food, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->food, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->food, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->food, $race->food, $starType->food], true) . "</td></tr>".

            "<tr><td class=\"tbldata\">" . ResIcons::POWER . "Produktion Energie</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->power, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->power, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->power, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->power, $race->power, $starType->power], true) . "</td></tr>".

            "<tr><td class=\"tbldata\">" . ResIcons::PEOPLE . "Bevölkerungswachstum</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->people, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->population, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->people, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->people, $race->population, $starType->people], true) . "</td></tr>".

            "<tr><td class=\"tbldata\">" . ResIcons::TIME . "Forschungszeit</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->researchTime, true, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->researchTime, true, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->researchTime, true, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->researchTime, $race->researchTime, $starType->researchTime], true, true) . "</td></tr>".

            "<tr><td class=\"tbldata\">" . ResIcons::TIME . "Bauzeit (Geb&aumlude)</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->buildTime, true, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->buildTime, true, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->buildTime, true, true) . "</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->buildTime, $race->buildTime, $starType->buildTime], true, true) . "</td></tr>".

            "<tr><td class=\"tbldata\">" . ResIcons::TIME . "Fluggeschwindigkeit</td>".
            "<td class=\"tbldata\">-</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->fleetTime, true) . "</td>".
            "<td class=\"tbldata\">-</td>".
            "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->fleetTime, true) . "</td></tr>";

            $addForm = $this->createForm(PlanetSetupType::class,['planet_id'=>$planetId,'checker'=>$this->checker->checker_init()]);
            $data = $request->request->all('planet_setup')??[];
            $data['planet_id'] = $planetId;
            $data['checker'] =  $this->checker->checker_init();
            $request->request->set('planet_setup',$data);
            $addForm->handleRequest($request);

            if($addForm->get('redo')->isClicked()) {
                return $this->redirect('game.setup.sector');
            }

            if($addForm->get('submit_chooseplanet')->isClicked() && $addForm->isValid()) {
                $planet = $this->planetRepository->find($request->request->all('planet_setup')['planet_id']);

                if ($planet !== null &&
                    $this->planetTypeRepository->isHabitable($planet->typeId) &&
                    $planet->userId == 0 &&
                    $planet->fields > $this->configurationService->getInt('user_min_fields') &&
                    $this->checker->checker_verify()
                ) {
                    $this->userSetupService->coloniseMainPlanet($request->request->all('planet_setup')['planet_id']);

                    if (count($sets) > 1) {
                        return $this->redirectToRoute('game.setup.itemset');
                    } elseif (count($sets) === 1) {
                        $this->userSetupService->addItemSetListToPlanet($planetId, $this->getUser()->getId(), $sets[0]->id);
                        $this->userRepository->setSetupFinished($this->getUser()->getId());
                        return $this->redirectToRoute('game.setup.finished');
                    } else {
                        $this->userRepository->setSetupFinished($this->getUser()->getId());
                        return $this->redirectToRoute('game.setup.finished');
                    }
                }
                return $this->redirect($request->getUri());
            }

            return $this->render('game/setup/setup_planet.html.twig', [
                'checker' => $this->checker->checker_init(),
                'planet_id' => $planetId,
                'addForm' => $addForm->createView(),
                'entity' => $entity,
                'starType' =>$starType,
                'planetType' =>$planetType,
                'planet' => $planet,
                'stats' => $stats,
                'race' => $race
            ]);
        }
        return $this->redirectToRoute('game.setup.sector');
    }

    #[Route('/game/setup/itemset', name: 'game.setup.itemset')]
    public function setupItemset(Request $request): Response
    {
        if($this->getUser()->getData()->isSetup()) {
            return $this->redirectToRoute('game.setup.finished');
        }

        $planetId = $this->planetRepository->getUserMainId($this->getUser()->getId());
        if($planetId) {
            $addForm = $this->createForm(ItemSetupType::class);
            $addForm->handleRequest($request);

            if($addForm->isSubmitted() && $addForm->isValid()) {
                $this->userSetupService->addItemSetListToPlanet($planetId, $this->getUser()->getId(), $addForm->getData()['itemset_id']);
                $this->userRepository->setSetupFinished($this->getUser()->getId());
                return $this->redirectToRoute('game.setup.finished');
            }

            return $this->render('game/setup/setup_itemset.html.twig', [
                'addForm' => $addForm->createView(),
            ]);
        }
        return $this->redirectToRoute('game.setup.planet');
    }

    #[Route('/game/setup/finished', name: 'game.setup.finished')]
    public function setupFinished(Request $request): Response
    {
        $welcomeText = $this->textRepository->find('welcome_message');
        $text = '';

        if ($welcomeText->isEnabled()) {
            $text = BBCodeUtils::toHTML($welcomeText->content);
            $this->messageRepository->createSystemMessage($this->getUser()->getId(), MessageCategoryId::USER, 'Willkommen', $welcomeText->content);
        }

        return $this->render('game/setup/setup_finished.html.twig', [
            'text' => $text
        ]);
    }
}