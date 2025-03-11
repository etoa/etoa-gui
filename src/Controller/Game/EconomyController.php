<?php

namespace EtoA\Controller\Game;

use EtoA\Backend\BackendMessageService;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Economy\EconomyService;
use EtoA\Entity\Entity;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetStatus;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Specialist\SpecialistService;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotNullValidator;
use function Symfony\Component\Translation\t;

class EconomyController extends AbstractGameController
{
    public function __construct(
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly EntityRepository $entityRepository,
        private readonly UserRepository $userRepository,
        private readonly ConfigurationService $configurationService,
        private readonly SpecialistService $specialistService,
        private readonly PlanetRepository $planetRepository,
        private readonly SpecialistDataRepository $specialistDataRepository,
        private readonly BackendMessageService $backendMessageService,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly DefenseQueueRepository $defenseQueueRepository,
        private readonly FleetRepository $fleetRepository,
        private readonly EconomyService $economyService
    )
    {
    }

    #[Route('/game/economy/{id}', name: 'game.economy')]
    public function economy(Request $request, Entity $entity = null): Response {
        $producingBuildingsItem = $this->buildingListItemRepository->findWithProductionOrPowerUse($entity);

        $cnt = array(
            "metal" => 0,
            "crystal" => 0,
            "plastic" => 0,
            "fuel" => 0,
            "food" => 0
        );
        $prodIncludingBoni = [];
        $powerUsed = 0;

        $pwrcnt = 0;

        $resourceKeys = ['metal', 'crystal', 'plastic', 'fuel', 'food'];
        $cp = $entity->getType();
        $race = $this->getUser()->getData()->getRace();
        $specialist = $this->getUser()->getData()->getSpecialist();
        $star = $this->entityRepository->findOneBy(['code'=>'s','cell'=>$entity->getCell()])?->getType();
        $bareBuildingProduction = [];
        $baseResourceProd = [];

        $form = $this->createFormBuilder();


        foreach ($producingBuildingsItem as $producingBuilding) {
            $form = $form->add($producingBuilding->getBuilding()->getId(), ChoiceType::class, [
                'choices' => [
                    '100%' => 1
                ]
            ]);

            // update base resource production, used later for boost calculation.
            foreach ($resourceKeys as $resourceKey) {
                $bareBuildingProduction[$producingBuilding->getBuilding()->getId()][$resourceKey] = $prodIncludingBoni[$producingBuilding->getBuilding()->getId()][$resourceKey] = $producingBuilding->getBuilding()->{'getProd' . $resourceKey}() * pow($producingBuilding->getBuilding()->getProductionFactor(), $producingBuilding->getCurrentLevel() - 1);
                $baseResourceProd[$producingBuilding->getBuilding()->getId()][$resourceKey] = $bareBuildingProduction[$producingBuilding->getBuilding()->getId()][$resourceKey] * $producingBuilding->getProdPercent();
            }

            $bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['name'] = $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['name'] = $producingBuilding->getBuilding()->getName();

            // Addieren der Planeten-, Rassen- und Spezialistenboni
            if ($bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['metal'] != "") {
                $boni = $cp->getPlanetType()->getMetal() - 1 + $race->getMetal() - 1 + $star->getSolarType()->getMetal() - 1 + ($specialist !== null ? $specialist->getProdMetal() : 1) - 1;
                $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['metal'] += $bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['metal'] * $boni;
            }
            if ($bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['crystal'] != "") {
                $boni = $cp->getPlanetType()->getCrystal() - 1 + $race->getCrystal() - 1 + $star->getSolarType()->getCrystal() - 1 + ($specialist !== null ? $specialist->getProdCrystal() : 1) - 1;
                $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['crystal'] += $bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['crystal'] * $boni;
            }
            if ($bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['plastic'] != "") {
                $boni = $cp->getPlanetType()->getPlastic() - 1 + $race->getPlastic() - 1 + $star->getSolarType()->getPlastic() - 1 + ($specialist !== null ? $specialist->getProdPlastic() : 1) - 1;
                $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['plastic'] += $bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['plastic'] * $boni;
            }
            if ($bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['fuel'] != "") {
                $boni = $cp->getPlanetType()->getFuel() - 1 + $race->getFuel() - 1 + $star->getSolarType()->getFuel() - 1 + ($specialist !== null ? $specialist->getProdFuel() : 1) - 1 + $cp->getFuelProductionBonusFactor() * -1;
                $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['fuel'] += $bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['fuel'] * $boni;
            }
            if ($bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['food'] != "") {
                $boni = $cp->getPlanetType()->getFood() - 1 + $race->getFood() - 1 + $star->getSolarType()->getFood() - 1 + ($specialist !== null ? $specialist->getProdFood() : 1) - 1;
                $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['food'] += $bareBuildingProduction[$producingBuilding->getBuilding()->getId()]['food'] * $boni;
            }


            foreach ($resourceKeys as $resourceKey) {
                // apply production percent
                $prodIncludingBoni[$producingBuilding->getBuilding()->getId()][$resourceKey] *= $producingBuilding->getCurrentLevel();
                // add to total
                $cnt[$resourceKey] += floor($prodIncludingBoni[$producingBuilding->getBuilding()->getId()][$resourceKey]);
            }

            $building_power_use = floor($producingBuilding->getBuilding()->getPowerUse() * pow($producingBuilding->getBuilding()->getProductionFactor(), $producingBuilding->getCurrentLevel() - 1));
            $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['power'] = StringUtils::formatNumber(ceil($building_power_use * $producingBuilding->getProdPercent()));
            $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['prod'] = $producingBuilding->getProdPercent();
            $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['level'] = $producingBuilding->getCurrentLevel();
            $prodIncludingBoni[$producingBuilding->getBuilding()->getId()]['type'] = $producingBuilding->getBuilding()->getType()->getId();

            //KälteBonusString
            $fuelBonus = "Kältebonus: ";
            $spw = $cp->fuelProductionBonus();
            if ($spw >= 0) {
                $fuelBonus .= "<span style=\"color:#0f0\">+" . $spw . "%</span>";
            } else {
                $fuelBonus .= "<span style=\"color:#f00\">" . $spw . "%</span>";
            }
            $fuelBonus .= " " . ResourceNames::FUEL . "-Produktion";


        }

        $form = $form->add('send', SubmitType::class, [
            'label' => 'Senden'
        ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { }

        $pwrcnt += $building_power_use * $producingBuilding->getProdPercent();

        return $this->render('game/economy/economy.html.twig', [
            'bareBuildingProduction' => $bareBuildingProduction,
            'prodIncludingBoni' => $prodIncludingBoni,
            'planet' => $cp,
            'resourceKeys' => $resourceKeys,
            'fuelBonus' => $fuelBonus,
            'form' => $form
        ]);
    }

    #[Route('/game/specialists', name: 'game.specialists')]
    public function specialists(Request $request): Response {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $cu = $this->getUser()->getData();
        $specialist = $cu->getSpecialist();

        $formDischarge = $this->createFormBuilder()
              ->add('discharge', SubmitType::class, [
                'label' => 'Entlassen',
                'attr' => [
                    'onclick' => "confirm('Willst du den Spezialisten wirklich entlassen? Es werden keine Ressourcen zurückerstattet, da der Spezialist diese als Abgangsentschädigung behält!')"
                ]
            ])
            ->getForm()
            ->handleRequest($request);

        // Discharge specialist
        if ($formDischarge->isSubmitted() && $formDischarge->isValid()) {
            if ($specialist && $cu->getSpecialistTime() > time()) {
                $inUse = false;
                $inittime = $cu->getSpecialistTime() - (86400 * $specialist->getDays());

                // check if research is in progress if using the professor
                if ($specialist->getTimeTechnologies() !== 1.0) {
                    $technologyEntries = $this->technologyListItemRepository->findForUser($cu, time());
                    foreach ($technologyEntries as $entry) {
                        if ($entry->getStartTime() > $inittime) {
                            $inUse = true;
                            break;
                        }
                    }
                }

                //Ingenieur
                if ($specialist->getTimeDefense() !== 1.0) {
                    $entries = $this->defenseQueueRepository->findBy(['user'=>$cu]);
                    foreach ($entries as $entry) {
                        if ($entry->getEndTime() > time() && $entry->getUserClickTime() > $inittime) {
                            $inUse = true;
                            break;
                        }
                    }
                }

                //Architekt
                if ($specialist->getTimeBuildings() !== 1.0) {
                    $buildingEntries = $this->buildingListItemRepository->findForUser($cu, null, time());
                    foreach ($buildingEntries as $entry) {
                        if ($entry->getStartTime() > $inittime) {
                            $inUse = true;
                            break;
                        }
                    }
                }

                //Admiral
                if ($specialist->getFleetSpeed() !== 1.0) {
                    /** @var FleetRepository $fleetRepository */
                    $fleets = $this->fleetRepository->findBy(['user'=>$cu]);
                    foreach ($fleets as $fleet) {
                        if ($fleet->getLaunchTime() > $inittime) {
                            if ($fleet->getStatus() == FleetStatus::DEPARTURE) {
                                $inUse = true;
                                break;
                            } else {
                                $duration = $fleet->getLandTime() - $fleet->getLaunchTime();
                                $org_launchtime = $fleet->getLaunchTime() - $duration;

                                if ($org_launchtime >= $inittime) {
                                    $inUse = true;
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($inUse) {
                    return $this->render('game/error.html.twig',[
                        'msg' => 'Der Spezialist wird gerade verwendet!',
                        'path' => $this->generateUrl('game.overview'),
                        'headline' => 'Spezialisten'
                    ]);
                } else {
                    $cu->setSpecialist(null);
                    $cu->setSpecialistTime(0);

                    $this->userRepository->save();

                    $msg['success'] = 'Der Spezialist wurde entlassen!';
                }
            } else {
                return $this->render('game/error.html.twig',[
                    'msg' => 'Du kannst niemanden entlassen, da kein Spezialist angestellt ist!',
                    'path' => $this->generateUrl('game.specialists'),
                    'headline' => 'Spezialisten'
                ]);
            }
        }

        $formSpecialists = $this->createFormBuilder($this->getUser()->getData())
            ->add('engage', SubmitType::class, [
                'label' => 'Gewählten Spezialisten einstellen',
            ])
            ->add('specialist', ChoiceType::class, [
                'label' => 'Gewählten Spezialisten einstellen',
                'choices' => $this->specialistDataRepository->findBy(['enabled'=>true]),
                'expanded' => true,
                'constraints' => [new NotNull()]
            ])
            ->getForm()
            ->handleRequest($request);

        if ($formSpecialists->isSubmitted() && $formSpecialists->isValid()) {
            $specialist = $cu->getSpecialist();
            if ($cu->getSpecialistTime() < time()) {
                $factor = $this->specialistService->getFactor($this->getUser()->getData()->getSpecialist());

                if ($cu->getPoints() >= $specialist->getPointsRequirement()) {
                    if (
                        $planet->getResMetal() >= $specialist->getCostsMetal() * $factor &&
                        $planet->getResCrystal() >= $specialist->getCostsCrystal() * $factor &&
                        $planet->getResPlastic() >= $specialist->getCostsPlastic() * $factor &&
                        $planet->getResFuel() >= $specialist->getCostsFuel() * $factor &&
                        $planet->getResFood() >= $specialist->getCostsFood() * $factor
                    ) {
                        $st = time() + (86400 * $specialist->getDays());
                        $cu->setSpecialistTime($st);

                        $this->planetRepository->addResources(
                            $planet,
                            -$specialist->getCostsMetal() * $factor,
                            -$specialist->getCostsCrystal() * $factor,
                            -$specialist->getCostsPlastic() * $factor,
                            -$specialist->getCostsFuel() * $factor,
                            -$specialist->getCostsFood() * $factor
                        );

                        $this->userRepository->save();

                        //Update every planet
                        foreach ($cu->getPlanets() as $userPlanet) {
                            $this->backendMessageService->updatePlanet($userPlanet->getId());
                        }
                        $msg['success'] = 'Der gewählte Spezialist wurde eingestellt!';
                    } else {
                        return $this->render('game/error.html.twig',[
                            'msg' => 'Zuwenig Rohstoffe vorhanden!',
                            'path' => $this->generateUrl('game.specialists'),
                            'headline' => 'Spezialisten'
                        ]);
                    }
                } else {
                    return $this->render('game/error.html.twig',[
                        'msg' => 'Zuwenig Punkte!',
                        'path' => $this->generateUrl('game.overview'),
                        'headline' => 'Spezialisten'
                    ]);
                }
            } else {
                return $this->render('game/error.html.twig',[
                    'msg' => 'Es ist bereits ein Spezialist eingestellt.
                            Seine Anstellung dauert noch bis ' . StringUtils::formatDate($cu->getSpecialistTime()) . '.
                            Du musst warten bis seine Anstellung beendet ist!',
                    'path' => $this->generateUrl('game.overview'),
                    'headline' => 'Spezialisten'
                ]);
            }
        }

        return $this->render('game/economy/specialist.html.twig', [
            'form_specialists' => $formSpecialists,
            'form_discharge' => $formDischarge,
            'specialistService' => $this->specialistService,
            'planet' => $planet,
            'msg' => $msg??null
        ]);
    }

    #[Route('/game/planetstats', name: 'game.planetstats')]
    public function planetStats(Request $request): Response {
        return $this->render('game/economy/planetstats.html.twig', [
            'ress' => $this->economyService->renderRess(),
            'prod' => $this->economyService->renderProduction()
        ]);
    }
}