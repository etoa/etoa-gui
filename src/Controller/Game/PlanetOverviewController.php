<?php

namespace EtoA\Controller\Game;

use EtoA\Entity\Planet;
use EtoA\Fleet\FleetRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\Universe\Star\StarRepository;
use EtoA\User\UserService;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class PlanetOverviewController extends AbstractGameController
{
    public function __construct(
        private readonly PlanetRepository             $planetRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly TechnologyDataRepository     $technologyDataRepository,
        private readonly UserService                  $userService,
        private readonly FleetRepository              $fleetRepository,
        private readonly ShipListRepository           $shipListRepository,
        private readonly PlanetService                $planetService,
        private readonly StarRepository               $starRepository
    )
    {
    }

    #[Route('/game/planetoverview/overview', name: 'game.planetoverview.overview')]
    public function overview(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        if ($cp && $cp->getUser() === $this->getUser()->getData()) {
            return $this->render('game/planetoverview/overview_overview.html.twig', [
                'planet' => $cp,
                'star' => $this->starRepository->findStarForCell($cp->getEntity()->getCell())
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Dieser Planet existiert nicht oder er gehört nicht dir!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Übersicht'
        ]);
    }

    #[Route('/game/planetoverview/name', name: 'game.planetoverview.name')]
    public function name(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        if ($cp && $cp->getUser() === $this->getUser()->getData()) {
            $form = $this->createFormBuilder($cp)
                ->add('name', TextType::class, [
                    'attr' => [
                        'maxlength' => 15
                    ],
                    'constraints' => new NotBlank([
                        'message' => 'Du musst einen Text eingeben!',
                    ]),
                ])
                ->add('description', TextareaType::class, [
                    'required' => false,
                    'attr' => [
                        'rows' => 2,
                        'cols' => 30
                    ]
                ])
                ->add('save', SubmitType::class, ['label' => 'Speichern'])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->planetRepository->save();
            }

            return $this->render('game/planetoverview/overview_name.html.twig', [
                'planet' => $cp,
                'form' => $form
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Dieser Planet existiert nicht oder er gehört nicht dir!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Übersicht'
        ]);
    }

    #[Route('/game/planetoverview/fields', name: 'game.planetoverview.fields')]
    public function fields(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        if ($cp && $cp->getUser() === $this->getUser()->getData()) {
            return $this->render('game/planetoverview/overview_fields.html.twig', [
                'planet' => $cp,
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Dieser Planet existiert nicht oder er gehört nicht dir!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Übersicht'
        ]);
    }

    #[Route('/game/planetoverview/ships', name: 'game.planetoverview.ships')]
    public function ships(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $cu = $this->getUser()->getData();

        if ($cp && $cp->getUser() === $cu) {
            $bonusStructure = 0;
            $bonusShield = 0;
            $bonusWeapon = 0;
            $bonusHeal = 0;

            $shield_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::SHILED, 'user' => $cu])?->getCurrentLevel() ?? 0;
            $shield_tech_a = 1 + ($shield_tech_level / 10);

            $structure_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::STRUCTURE, 'user' => $cu])?->getCurrentLevel() ?? 0;
            $structure_tech_a = 1 + ($structure_tech_level / 10);

            $weapon_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::WEAPON, 'user' => $cu])?->getCurrentLevel() ?? 0;
            $weapon_tech_a = 1 + ($weapon_tech_level / 10);

            $heal_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::REGENA, 'user' => $cu])?->getCurrentLevel() ?? 0;
            $heal_tech_a = 1 + ($heal_tech_level / 10);

            $totalStructure = 0;
            $totalShield = 0;
            $totalWeapon = 0;
            $totalHeal = 0;
            $shipCounts = 0;

            foreach ($cp->getShiplist() as $shiplist) {
                $shipCounts += $shiplist->getCount();
                $totalStructure += $shiplist->getCount() * $shiplist->getShip()->getStructure();
                $bonusStructure += $shiplist->getCount() * $shiplist->getShip()->getSpecialBonusStructure();
                $totalShield += $shiplist->getCount() * $shiplist->getShip()->getShield();
                $bonusShield += $shiplist->getCount() * $shiplist->getShip()->getSpecialBonusShield();
                $totalWeapon += $shiplist->getCount() * $shiplist->getShip()->getWeapon();
                $bonusWeapon += $shiplist->getCount() * $shiplist->getShip()->getSpecialBonusWeapon();
                $totalHeal += $shiplist->getCount() * $shiplist->getShip()->getHeal();
                $bonusHeal += $shiplist->getCount() * $shiplist->getShip()->getSpecialBonusHeal();
            }

            return $this->render('game/planetoverview/overview_ships.html.twig', [
                'planet' => $cp,
                'totalStructure' => $totalStructure,
                'bonusStructure' => $bonusStructure,
                'totalShield' => $totalShield,
                'bonusShield' => $bonusShield,
                'totalWeapon' => $totalWeapon,
                'bonusWeapon' => $bonusWeapon,
                'totalHeal' => $totalHeal,
                'bonusHeal' => $bonusHeal,
                'shield_tech_a' => $shield_tech_a,
                'structure_tech_a' => $structure_tech_a,
                'weapon_tech_a' => $weapon_tech_a,
                'heal_tech_a' => $heal_tech_a,
                'shipCounts' => $shipCounts,
                'shield_tech_level' => $shield_tech_level,
                'structure_tech_level' => $structure_tech_level,
                'weapon_tech_level' => $weapon_tech_level,
                'heal_tech_level' => $heal_tech_level,
                'structure' => $this->technologyDataRepository->find(TechnologyId::STRUCTURE)->getName(),
                'shield' => $this->technologyDataRepository->find(TechnologyId::SHILED)->getName(),
                'weapon' => $this->technologyDataRepository->find(TechnologyId::WEAPON)->getName(),
                'regena' => $this->technologyDataRepository->find(TechnologyId::REGENA)->getName(),
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Dieser Planet existiert nicht oder er gehört nicht dir!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Übersicht'
        ]);
    }

    #[Route('/game/planetoverview/defense', name: 'game.planetoverview.defense')]
    public function defense(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $cu = $this->getUser()->getData();

        if ($cp && $cp->getUser() === $cu) {
            $shield_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::SHILED, 'user' => $cu])?->getCurrentLevel() ?? 0;
            $shield_tech_a = 1 + ($shield_tech_level / 10);

            $structure_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::STRUCTURE, 'user' => $cu])?->getCurrentLevel() ?? 0;
            $structure_tech_a = 1 + ($structure_tech_level / 10);

            $weapon_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::WEAPON, 'user' => $cu])?->getCurrentLevel() ?? 0;
            $weapon_tech_a = 1 + ($weapon_tech_level / 10);

            $heal_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::REGENA, 'user' => $cu])?->getCurrentLevel() ?? 0;
            $heal_tech_a = 1 + ($heal_tech_level / 10);

            $totalStructure = 0;
            $totalShield = 0;
            $totalWeapon = 0;
            $totalHeal = 0;
            $defCounts = 0;

            foreach ($cp->getDeflist() as $deflist) {
                $defCounts += $deflist->getCount();
                $totalStructure += $deflist->getCount() * $deflist->getDefense()->getStructure();
                $totalShield += $deflist->getCount() * $deflist->getDefense()->getShield();
                $totalWeapon += $deflist->getCount() * $deflist->getDefense()->getWeapon();
                $totalHeal += $deflist->getCount() * $deflist->getDefense()->getHeal();
            }

            return $this->render('game/planetoverview/overview_defense.html.twig', [
                'planet' => $cp,
                'totalStructure' => $totalStructure,
                'totalShield' => $totalShield,
                'totalWeapon' => $totalWeapon,
                'totalHeal' => $totalHeal,
                'shield_tech_a' => $shield_tech_a,
                'structure_tech_a' => $structure_tech_a,
                'weapon_tech_a' => $weapon_tech_a,
                'heal_tech_a' => $heal_tech_a,
                'defCounts' => $defCounts,
                'shield_tech_level' => $shield_tech_level,
                'structure_tech_level' => $structure_tech_level,
                'weapon_tech_level' => $weapon_tech_level,
                'heal_tech_level' => $heal_tech_level,
                'structure' => $this->technologyDataRepository->find(TechnologyId::STRUCTURE)->getName(),
                'shield' => $this->technologyDataRepository->find(TechnologyId::SHILED)->getName(),
                'weapon' => $this->technologyDataRepository->find(TechnologyId::WEAPON)->getName(),
                'regena' => $this->technologyDataRepository->find(TechnologyId::REGENA)->getName(),
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Dieser Planet existiert nicht oder er gehört nicht dir!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Übersicht'
        ]);
    }

    #[Route('/game/planetoverview/delete', name: 'game.planetoverview.delete')]
    public function delete(Request $request): Response
    {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $cu = $this->getUser()->getData();

        if (!$planet->isMainPlanet()) {
            $threshold = $planet->getUserChanged() + Planet::COLONY_DELETE_THRESHOLD;
            if ($threshold < time()) {
                $form = $this->createFormBuilder()
                    ->add('save', SubmitType::class, ['label' => 'Ja, die Kolonie soll aufgehoben werden'])
                    ->getForm()
                    ->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    if (!$this->shipListRepository->hasShipsOnEntity($planet->getEntity())) {
                        if (!$this->fleetRepository->hasFleetsRelatedToEntity($planet->getEntity())) {
                            if ($cu === $planet->getUser()) {
                                $this->planetService->reset($planet);

                                return $this->render('game/success.html.twig', [
                                    'msg' => "Die Kolonie wurde aufgehoben!",
                                    'path' => $this->generateUrl('game.overview'),
                                    'headline' => 'Übersicht'
                                ]);
                            } else {
                                return $this->render('game/error.html.twig', [
                                    'msg' => "Der Planet ist aktuell nicht ausgewählt oder er gehört nicht dir!",
                                    'path' => $this->generateUrl('game.planetoverview.overview'),
                                    'headline' => 'Übersicht'
                                ]);
                            }
                        } else {
                            return $this->render('game/error.html.twig', [
                                'msg' => "Kolonie kann nicht gelöscht werden da Schiffe von/zu diesem Planeten unterwegs sind!",
                                'path' => $this->generateUrl('game.planetoverview.overview'),
                                'headline' => 'Übersicht'
                            ]);
                        }
                    } else {
                        return $this->render('game/error.html.twig', [
                            'msg' => "Kolonie kann nicht gelöscht werden da noch Schiffe auf dem Planeten stationiert sind oder Schiffe noch im Bau sind!",
                            'path' => $this->generateUrl('game.planetoverview.overview'),
                            'headline' => 'Übersicht'
                        ]);
                    }
                }

                return $this->render('game/planetoverview/overview_delete.html.twig', [
                    'planet' => $planet,
                    'form' => $form
                ]);
            } else {
                return $this->render('game/error.html.twig', [
                    'msg' => "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                               erst ab <b>" . StringUtils::formatDate($threshold) . "</b> gelöscht werden!",
                    'path' => $this->generateUrl('game.planetoverview.overview'),
                    'headline' => 'Übersicht'
                ]);
            }
        } else {
            return $this->render('game/error.html.twig', [
                'msg' => "Dies ist ein Hauptplanet! Hauptplaneten können nicht aufgegeben werden!",
                'path' => $this->generateUrl('game.planetoverview.overview'),
                'headline' => 'Übersicht'
            ]);
        }
    }

    #[Route('/game/planetoverview/change', name: 'game.planetoverview.change')]
    public function changeMain(Request $request): Response
    {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $cu = $this->getUser()->getData();

        if (!$planet->isMainPlanet()) {
            $threshold = $planet->getUserChanged() + Planet::COLONY_DELETE_THRESHOLD;
            if ($threshold < time()) {
                if (!$cu->isUserChangedMainPlanet()) {

                    $form = $this->createFormBuilder()
                        ->add('save', SubmitType::class, ['label' => 'Ja, Hauptplanet wechseln'])
                        ->getForm()
                        ->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $this->planetRepository->setMain($planet, $cu);
                        $entity = $planet->getEntity();

                        $this->userService->addToUserLog($cu, "planets", "{nick} wählt [b]" . $entity->toString() . "[/b] als neuen Hauptplanet aus.", false);

                        return $this->render('game/success.html.twig', [
                            'msg' => "<b>" . $planet->getName() . "</b> ist nun dein Hauptplanet!",
                            'path' => $this->generateUrl('game.planetoverview.overview'),
                            'headline' => 'Übersicht'
                        ]);
                    }


                    return $this->render('game/planetoverview/overview_change_main.html.twig', [
                        'planet' => $planet,
                        'form' => $form
                    ]);
                } else {
                    return $this->render('game/error.html.twig', [
                        'msg' => "Du kannst deinen Hauptplaneten nur ein Mal ändern!",
                        'path' => $this->generateUrl('game.planetoverview.overview'),
                        'headline' => 'Übersicht'
                    ]);
                }
            } else {
                return $this->render('game/error.html.twig', [
                    'msg' => "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                               erst ab <b>" . StringUtils::formatDate($threshold) . "</b> zu deinem Hauptplaneten gemacht werden!",
                    'path' => $this->generateUrl('game.planetoverview.overview'),
                    'headline' => 'Übersicht'
                ]);
            }
        } else {
            return $this->render('game/error.html.twig', [
                'msg' => "Dies ist bereits ein Hauptplanet!",
                'path' => $this->generateUrl('game.planetoverview.overview'),
                'headline' => 'Übersicht'
            ]);
        }
    }
}