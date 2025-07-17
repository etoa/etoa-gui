<?php

namespace EtoA\Controller\Game;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\Planet;
use EtoA\Form\Type\Core\CountType;
use EtoA\Form\Type\Core\DefenseListItemType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipListRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecycleController extends AbstractGameController
{
    public function __construct(
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly ConfigurationService $configurationService,
        private readonly ShipListRepository $shipListRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly DefenseRepository $defenseRepository,
        private readonly LogRepository $logRepository
    )
    {
    }

    #[Route('/game/recycle', name: 'game.recycle')]
    public function recycle(Request $request): Response
    {
        $tech_level = $this->technologyListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'technology'=>TechnologyId::RECYCLING])?->getCurrentLevel();

        if ($tech_level) {

            $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

            $payback_max = $this->configurationService->getFloat('recyc_max_payback');
            $payback = ($payback_max) - ($payback_max / $tech_level);
            $pb_percent = round($payback * 100, 2);
            $pb = [];
            $pb[0] = 0;
            $pb[1] = 0;
            $pb[2] = 0;
            $pb[3] = 0;
            $pb[4] = 0;
            $cnt = 0;
            $log_ships = "";
            $log_def = "";

            //
            //Schiffe
            //
            $ships = $this->shipListRepository->getRecyclable($this->getUser()->getData(), $cp);

            $formShips = $this->createFormBuilder(['ships'=>$ships])
                ->add('ships', CollectionType::class, [
                    'entry_type' => CountType::class,
                    'label' => false,
                    'required' => false,
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Ausgewählte Schiffe recyceln'
                ])

                ->getForm()
                ->handleRequest($request);

            if ($formShips->isSubmitted() && $formShips->isValid()) {
                foreach ($formShips->get('ships')->all() as $ship) {

                    //Anzahl muss grösser als 0 sein
                    if($ship->get('count')->getData()) {
                        $num = abs($ship->get('count')->getData());

                        //Anzahl anpassen, wenn angegebene Anzahl grösser ist, als die effektive Anzahl auf dem Planeten
                        if ($num > $ship->getData()->getCount()) {
                            $num = $ship->getData()->getCount();
                        }

                        //Schiffe vom Planeten abziehen
                        $this->shipListRepository->removeShips($ship->getData(), $num);

                        //Rohstoffe summieren
                        $pb[0] += ceil($payback * $ship->getData()->getShip()->getCostsMetal() * $num);
                        $pb[1] += ceil($payback * $ship->getData()->getShip()->getCostsCrystal() * $num);
                        $pb[2] += ceil($payback * $ship->getData()->getShip()->getCostsPlastic() * $num);
                        $pb[3] += ceil($payback * $ship->getData()->getShip()->getCostsFuel() * $num);
                        $pb[4] += ceil($payback * $ship->getData()->getShip()->getCostsFood() * $num);
                        $cnt += $num;

                        $log_ships .= "[B]" . $ship->getData()->getShip()->getName() . ":[/B] " . $num . "\n";
                    }
                }

                //Rohstoffe Updaten
                $this->planetRepository->addResources($cp, $pb[0], $pb[1], $pb[2], $pb[3], $pb[4]);

                //Log schreiben
                $log = "Der User [page user sub=edit user_id=" . $this->getUser()->getId() . "] [B]" . $this->getUser()->getData()->getNick() . "[/B] [/page] hat auf dem Planeten [page galaxy sub=edit id=" . $cp->getId() . "][B]" . $cp->getName() . "[/B][/page] folgende Schiffe mit dem r&uuml;ckgabewert von " . ($payback * 100) . "% recycelt:\n\n" . $log_ships . "\nDies hat ihm folgende Rohstoffe gegeben:\n" . ResourceNames::METAL . ": " . StringUtils::formatNumber($pb[0]) . "\n" . ResourceNames::CRYSTAL . ": " . StringUtils::formatNumber($pb[1]) . "\n" . ResourceNames::PLASTIC . ": " . StringUtils::formatNumber($pb[2]) . "\n" . ResourceNames::FUEL . ": " . StringUtils::formatNumber($pb[3]) . "\n" . ResourceNames::FOOD . ": " . StringUtils::formatNumber($pb[4]) . "\n";

                $this->logRepository->add(LogFacility::RECYCLING, LogSeverity::INFO, $log);

                $msg['success'] = StringUtils::formatNumber($cnt) . " Schiffe erfolgreich recycelt!";
            }


            $defenses = $this->defenseRepository->getRecyclable($this->getUser()->getData(), $cp);

            $formDefenses = $this->createFormBuilder(['defenses'=>$defenses])
                ->add('defenses', CollectionType::class, [
                    'entry_type' => CountType::class,
                    'label' => false,
                    'required' => false,
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Ausgewählte Anlagen recyceln'
                ])

                ->getForm()
                ->handleRequest($request);

            if ($formDefenses->isSubmitted() && $formDefenses->isValid()) {
                $fields = 0;

                foreach ($formDefenses->get('defenses')->all() as $defense) {
                    //Anzahl muss grösser als 0 sein
                    if($defense->get('count')->getData()) {
                        $num = abs($defense->get('count')->getData());

                        //Anzahl anpassen, wenn angegebene Anzahl grösser ist, als die effektive Anzahl auf dem Planeten
                        if ($num > $defense->getData()->getCount()) {
                            $num = $defense->getData()->getCount();
                        }

                        //Defense vom Planeten Abziehen
                        $this->defenseRepository->removeDefense($defense->getData(), $num);

                        //Rohstoffe summieren
                        $pb[0] += ceil($payback * $defense->getData()->getDefense()->getCostsMetal() * $num);
                        $pb[1] += ceil($payback * $defense->getData()->getDefense()->getCostsCrystal() * $num);
                        $pb[2] += ceil($payback * $defense->getData()->getDefense()->getCostsPlastic() * $num);
                        $pb[3] += ceil($payback * $defense->getData()->getDefense()->getCostsFuel() * $num);
                        $pb[4] += ceil($payback * $defense->getData()->getDefense()->getCostsFood() * $num);
                        $fields += $defense->getData()->getDefense()->getFields() * $num;
                        $cnt += $num;

                        $log_def .= "[B]" . $defense->getData()->getDefense()->getName() . ":[/B] " . $num . "\n";
                    }
                }

                //Rohstoffe und Felder updaten
                $this->planetRepository->addResources($cp, $pb[0], $pb[1], $pb[2], $pb[3], $pb[4], 0, -$fields);

                //Log schreiben
                $log = "Der User [page=".$this->generateUrl('game.userinfo',['id'=>$this->getUser()->getId()])."] [B]" . $this->getUser()->getData()->getNick() . "[/B] [/page] hat auf dem Planeten [page galaxy sub=edit id=" . $cp->getId() . "][B]" . $cp->getName() . "[/B][/page] folgende Verteidigungsanlagen mit dem r&uuml;ckgabewert von " . ($payback * 100) . "% recycelt:\n\n" . $log_def . "\nDies hat ihm folgende Rohstoffe gegeben:\n" . ResourceNames::METAL . ": " . StringUtils::formatNumber($pb[0]) . "\n" . ResourceNames::CRYSTAL . ": " . StringUtils::formatNumber($pb[1]) . "\n" . ResourceNames::PLASTIC . ": " . StringUtils::formatNumber($pb[2]) . "\n" . ResourceNames::FUEL . ": " . StringUtils::formatNumber($pb[3]) . "\n" . ResourceNames::FOOD . ": " . StringUtils::formatNumber($pb[4]) . "\n";
                $this->logRepository->add(LogFacility::RECYCLING, LogSeverity::INFO, $log);
                $msg['success'] = StringUtils::formatNumber($cnt) . " Verteidigungsanlagen erfolgreich recycelt!";
            }

            return $this->render('game/recycle/recycle.html.twig',[
                'msg' => $msg??null,
                'planet' => $cp,
                'formShips' => $formShips,
                'techLevel' => $tech_level,
                'payback' => $pb_percent,
                'ships' => $ships,
                'formDefenses' => $formDefenses
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Es können keine Schiffe oder Verteidigungsanlagen recycelt werden, da die Recyclingtechnologie noch nicht erforscht wurde!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Recycling'
        ]);
    }
}