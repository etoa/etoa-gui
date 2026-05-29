<?php

namespace EtoA\Controller\Game;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\DefaultItem\DefaultItemSetRepository;
use EtoA\Entity\Planet;
use EtoA\Form\Type\Core\ChooseSectorSetupType;
use EtoA\Form\Type\Core\ItemSetupType;
use EtoA\Form\Type\Core\PlanetSetupType;
use EtoA\Form\Type\Core\RaceSetupType;
use EtoA\Message\MessageCategoryRepository;
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
        private readonly TextRepository           $textRepository,
        private readonly UserRepository           $userRepository,
        private readonly ConfigurationService     $configurationService,
        private readonly UserSetupService         $userSetupService,
        private readonly DefaultItemSetRepository $defaultItemSetRepository,
        private readonly MessageCategoryRepository $messageCategoryRepository

    )
    {
    }
    #[Route('/game/setup/race', name: 'game.setup.race')]
    public function setupRace(Request $request): Response
    {
        if(!$this->getUser()->getData()->getRace()) {
            $addForm = $this->createForm(RaceSetupType::class);
            $addForm->handleRequest($request);

            if($addForm->isSubmitted() && $addForm->isValid()) {
                $this->getUser()->getData()->setRace($addForm->get('race')->getData());
                $this->userRepository->save();
                return $this->redirectToRoute('game.setup.sector');
            }

            #$addForm->get('checker')->setData($this->checker->checker_init());

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

        if($this->getUser()->getData()->getRace()) {
            $sx_num = $this->configurationService->param1Int('num_of_sectors');
            $sy_num = $this->configurationService->param2Int('num_of_sectors');

            $sec_x_size = GalaxyMap::WIDTH / $sx_num;
            $sec_y_size = GalaxyMap::WIDTH / $sy_num;
            $xcnt = 1;
            $map=[];

            for ($x = 0; $x < GalaxyMap::WIDTH; $x += $sec_x_size) {
                $ycnt = 1;
                for ($y = 0; $y < GalaxyMap::WIDTH; $y += $sec_y_size) {
                    $countStars = $this->entityRepository->countEntitiesOfCodeInSector($xcnt, $ycnt, EntityType::STAR);
                    $countPlanets = $this->entityRepository->countEntitiesOfCodeInSector($xcnt, $ycnt, EntityType::PLANET);
                    $countInhabitedPlanets = $this->entityRepository->countWithUserInSector($xcnt, $ycnt);

                    $planet = $this->planetRepository->getRandomFreePlanet(
                        $xcnt,
                        $ycnt,
                        $this->configurationService->getInt('user_min_fields')
                    );

                    $tt = new Tooltip();
                    $tt->addTitle("Sektor $xcnt/$ycnt");
                    $tt->addText("Sternensysteme: " . $countStars);
                    $tt->addText("Planeten: " . $countPlanets);
                    $tt->addGoodCond("Bewohnte Planeten: " . $countInhabitedPlanets);
                    $tt->addComment("Klickt hier um euren Heimatplaneten in Sektor <b>" . $xcnt . "/" . $ycnt . "</b> anzusiedeln!");

                    $map[$y][$x] = "<area shape=\"rect\" coords=\"$x," . (GalaxyMap::WIDTH - $y) . "," . ($x + $sec_x_size) . "," . (GalaxyMap::WIDTH - $y - $sec_y_size) . "\" href=\"".$this->generateUrl('game.setup.planet', ['id'=>$planet?->getEntity()->getId()]) . "\" alt=\"Sektor $xcnt / $ycnt\" " . $tt->toString() . ">\n";
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

    #[Route('/game/setup/planet/{id?}', name: 'game.setup.planet')]
    public function setupPlanet(?Planet $planet, Request $request): Response
    {
        if(!$this->getUser()->getData()->getRace()) {
            return $this->redirectToRoute('game.setup.race');
        }

        $sets = $this->defaultItemSetRepository->getSets();

        if($this->planetRepository->findBy(['mainPlanet'=>true,'user'=>$this->getUser()->getData()]))  {
            if (count($sets) > 1) {
                return $this->redirectToRoute('game.setup.itemset');
            }
            return $this->redirectToRoute('game.setup.finished');
        }

        $addForm = $this->createForm(PlanetSetupType::class,['checker'=>$this->checker->checker_init()]);

        if(!$planet) {
            $flashes = $request->getSession()->getFlashBag();
            $flashes->add(
                'warning',
                'Leider konnte kein geeigneter Planet in diesem Sektor gefunden werden.
                          Bitte wähle einen anderen Sektor!'
            );
            return $this->redirectToRoute('game.setup.sector');
        }


        $planetType = $planet->getPlanetType();
        $entity = $planet->getEntity();
        $star = $this->starRepository->find($this->entityRepository->findByCellAndPosition($entity->getCell(), 0)->getId());
        $starType = $star->getSolarType();
        $race = $this->getUser()->getData()->getRace();

        $stats =
        "<tr><td class=\"tbldata\">" . ResIcons::METAL . "Produktion " . ResourceNames::METAL . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->getMetal(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getMetal(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->getMetal(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->getMetal(), $race->getMetal(), $starType->getMetal()], true) . "</td></tr>".

        "<tr><td class=\"tbldata\">" . ResIcons::CRYSTAL . "Produktion " . ResourceNames::CRYSTAL . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->getCrystal(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getCrystal(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->getCrystal(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->getCrystal(), $race->getCrystal(), $starType->getCrystal()], true) . "</td></tr>".

        "<tr><td class=\"tbldata\">" . ResIcons::PLASTIC . "Produktion " . ResourceNames::PLASTIC . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->getPlastic(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getPlastic(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->getPlastic(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->getPlastic(), $race->getPlastic(), $starType->getPlastic()], true) . "</td></tr>".

        "<tr><td class=\"tbldata\">" . ResIcons::FUEL . "Produktion " . ResourceNames::FUEL . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->getFuel(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getFuel(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->getFuel(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->getFuel(), $race->getFuel(), $starType->getFuel()], true) . "</td></tr>".

        "<tr><td class=\"tbldata\">" . ResIcons::FOOD . "Produktion " . ResourceNames::FOOD . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->getFood(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getFood(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->getFood(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->getFood(), $race->getFood(), $starType->getFood()], true) . "</td></tr>".

        "<tr><td class=\"tbldata\">" . ResIcons::POWER . "Produktion Energie</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->getPower(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getPower(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->getPower(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->getPower(), $race->getPower(), $starType->getPower()], true) . "</td></tr>".

        "<tr><td class=\"tbldata\">" . ResIcons::PEOPLE . "Bevölkerungswachstum</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->getPeople(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getPopulation(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->getPeople(), true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->getPeople(), $race->getPopulation(), $starType->getPeople()], true) . "</td></tr>".

        "<tr><td class=\"tbldata\">" . ResIcons::TIME . "Forschungszeit</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->getResearchTime(), true, true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getResearchTime(), true, true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->getResearchTime(), true, true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->getResearchTime(), $race->getResearchTime(), $starType->getResearchTime()], true, true) . "</td></tr>".

        "<tr><td class=\"tbldata\">" . ResIcons::TIME . "Bauzeit (Geb&aumlude)</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->getBuildTime(), true, true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getBuildTime(), true, true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->getBuildTime(), true, true) . "</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->getBuildTime(), $race->getBuildTime(), $starType->getBuildTime()], true, true) . "</td></tr>".

        "<tr><td class=\"tbldata\">" . ResIcons::TIME . "Fluggeschwindigkeit</td>".
        "<td class=\"tbldata\">-</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getFleetTime(), true) . "</td>".
        "<td class=\"tbldata\">-</td>".
        "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->getFleetTime(), true) . "</td></tr>";

        $addForm->handleRequest($request);

        if($addForm->get('redo')->isClicked()) {
            return $this->redirect('game.setup.sector');
        }

        if($addForm->get('new_planet')->isClicked()) {
            $planet = $this->planetRepository->getRandomFreePlanet(
                $planet->getEntity()->getCell()->getSx(),
                $planet->getEntity()->getCell()->getSy(),
                $this->configurationService->getInt('user_min_fields'),
                $addForm->get('filter_planet_id')->getData()?->getId(),
                $addForm->get('filter_sol_id')->getData()?->getId()
            );
            $session = $request->getSession();
            $session->set('filter_sol_id',$addForm->get('filter_sol_id')->getData());
            $session->set('filter_planet_id',$addForm->get('filter_planet_id')->getData());

            return $this->redirectToRoute('game.setup.planet',['id'=> $planet?->getEntity()->getId()]);
        }

        if($addForm->get('submit_chooseplanet')->isClicked() && $addForm->isValid()) {
            if ($planet->getPlanetType()->isHabitable() &&
                $planet->getUser() === null &&
                $planet->getFields() > $this->configurationService->getInt('user_min_fields')
                #&& $this->checker->checker_verify()
            ) {
                $this->userSetupService->coloniseMainPlanet($planet);

                if (count($sets) > 1) {
                    return $this->redirectToRoute('game.setup.itemset');
                } elseif (count($sets) === 1) {
                    $this->userSetupService->addItemSetListToPlanet($planet, $this->getUser()->getData(), $sets[0]);
                    $this->userRepository->setSetupFinished($this->getUser()->getData());
                    return $this->redirectToRoute('game.setup.finished');
                } else {
                    $this->userRepository->setSetupFinished($this->getUser()->getData());
                    return $this->redirectToRoute('game.setup.finished');
                }
            }
            return $this->redirect($request->getUri());
        }

        return $this->render('game/setup/setup_planet.html.twig', [
            'checker' => $this->checker->checker_init(),
            'addForm' => $addForm->createView(),
            'entity' => $entity,
            'starType' =>$starType,
            'planetType' =>$planetType,
            'planet' => $planet,
            'stats' => $stats,
            'race' => $race
        ]);
    }

    #[Route('/game/setup/itemset', name: 'game.setup.itemset')]
    public function setupItemset(Request $request): Response
    {
        if($this->getUser()->getData()->isSetup()) {
            return $this->redirectToRoute('game.setup.finished');
        }

        $planet = $this->planetRepository->getUserMain($this->getUser()->getData());
        if($planet) {
            $addForm = $this->createForm(ItemSetupType::class);
            $addForm->handleRequest($request);

            if($addForm->isSubmitted() && $addForm->isValid()) {
                $this->userSetupService->addItemSetListToPlanet($planet, $this->getUser()->getData(), $addForm->getData()['itemset_id']->getId());
                $this->userRepository->setSetupFinished($this->getUser()->getData());
                return $this->redirectToRoute('game.setup.finished');
            }

            return $this->render('game/setup/setup_itemset.html.twig', [
                'addForm' => $addForm->createView(),
            ]);
        }
        return $this->redirectToRoute('game.setup.planet');
    }

    #[Route('/game/setup/finished', name: 'game.setup.finished')]
    public function setupFinished(): Response
    {
        $welcomeText = $this->textRepository->find('welcome_message');
        $text = '';

        if ($welcomeText?->isEnabled()) {
            $text = BBCodeUtils::toHTML($welcomeText->getContent());
            $this->messageRepository->createSystemMessage($this->getUser()->getData(),  $this->messageCategoryRepository->find(MessageCategoryId::USER), 'Willkommen', $welcomeText->getContent());
        }

        return $this->render('game/setup/setup_finished.html.twig', [
            'text' => $text
        ]);
    }
}