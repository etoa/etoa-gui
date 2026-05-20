<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Planet;
use EtoA\Form\Type\Core\EditPopulationType;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Technology\TechnologyRequirementRepository;
use EtoA\Technology\TechnologyService;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetService;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PopulationController extends AbstractGameController
{
    public function __construct(
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly ConfigurationService $configurationService,
        private readonly TechnologyService $technologyService,
        private readonly TechnologyDataRepository $technologyDataRepository,
        private readonly BuildingRepository $buildingRepository,
        private readonly PlanetService $planetService,
        private readonly EntityRepository $entityRepository
    )
    {
    }

    #[Route('/game/population/{id}', name: 'game.population')]
    public function population(Request $request, ?Planet $planet = null): Response {
        if($planet && $planet->getUser() === $this->getUser()->getData()) {
            $peopleStorageBuildings = $this->buildingListItemRepository->getPeopleStorageBuildings($planet);
            if (count($peopleStorageBuildings) > 0) {
                $pcnt = $this->configurationService->param1Int('user_start_people');
                $values = [];

                foreach ($peopleStorageBuildings as $storage) {
                    $place = round($storage->getBuilding()->getPeoplePlace() * pow($storage->getBuilding()->getStoreFactor(), $storage->getCurrentLevel() - 1));
                    $values['building'][$storage->getBuilding()->getName()] = $place;
                    $pcnt += $place;
                }
                $values['total'] = $pcnt;

                $workplaces = $this->buildingListItemRepository->getWorkplaceBuildings($planet);

                if (count($workplaces) > 0) {
                    //fake genlab
                    if($this->technologyService->requirementsPassed($this->technologyDataRepository->find(TechnologyId::GEN))) {
                        $genlab = $this->buildingListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'entity'=>$planet,'building'=>$this->buildingRepository->find(BuildingId::PEOPLE)]);
                        $genlab->displayName = 'Genlabor';
                        $workplaces[] = $genlab;
                    }

                    foreach ($workplaces as $workplace) {
                        if ($workplace->getBuilding()->getId() === BuildingId::BUILDING) {
                            $workplace->displayName = 'Bauhof';
                        }
                    }
                }

                $form = $this->createFormBuilder(['workplaces'=>$workplaces])
                    ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($planet) {
                        $event->setData($this->validateWorker($event->getData(), $planet));
                    })
                    ->add('workplaces', CollectionType::class, [
                        'entry_type'   => EditPopulationType::class,
                        'entry_options' => [
                            'label' => false
                        ],
                    ])
                    ->add('save', SubmitType::class, ['label' => 'Speichern'])
                    ->add('free', SubmitType::class, ['label' => 'Alle Arbeiter freigeben'])
                    ->getForm()
                    ->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()) {
                    if($form->get('save')->isClicked()) {
                        $this->buildingListItemRepository->save();
                    }

                    if($form->get('free')->isClicked()) {
                        foreach ($workplaces as $workplace) {
                            if($workplace->getPeopleWorkingStatus() === 0) {
                                $workplace->setPeopleWorking(0);
                            }
                            $this->buildingListItemRepository->save();
                        }
                    }
                }

                $specialist = $this->getUser()->getData()->getSpecialist();
                $race = $this->getUser()->getData()->getRace();

                // Zählt alle arbeiter die eingetragen snid (besetzt oder nicht) für die anszeige!
                $peopleWorking = $this->planetService->getTotalPeopleWorking($planet);

                // Infodaten
                $capacity = max($planet->getPeoplePlace(), 200);

                $peopleFree = floor($planet->getPeople()) - $peopleWorking;
                $star = $this->entityRepository->findOneBy(['code'=>'s','cell'=>$planet->getEntity()->getCell()])?->getType();

                $peopleDiv = $planet->getPeople() * (($this->configurationService->getFloat('people_multiply')  + $planet->getPlanetType()->getPeople() + $race->getPopulation() + $star->getSolarType()->getPeople() + ($specialist !== null ? $specialist->getProdPeople() : 1) - 4) * (1 - ($planet->getPeople() / ($capacity + 1))) / 24);

                return $this->render('game/population/population.html.twig',[
                    'values' => $values,
                    'planet' => $planet,
                    'form' => $form,
                    'peopleFree' => $peopleFree,
                    'peopleDiv' => $peopleDiv,
                    'peopleWorking' => $peopleWorking,
                    'star' => $star

                ]);
            }

            return $this->render('game/error.html.twig',[
                'msg' => 'Es sind noch keine Gebäude gebaut, in denen deine Bevölkerung wohnen oder arbeiten kann!',
                'path' => $this->generateUrl('game.overview'),
                'headline' => 'Bevölkerungsübersicht'
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Dieser Planet existiert nicht!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Bevölkerungsübersicht'
        ]);
    }


    private function validateWorker(array $data, Planet $planet):array
    {
        $working = 0;
        // Frei = total auf Planet - gesperrt auf Planet
        $free_people = floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet,true);

        foreach ($data['workplaces'] as $workplace) {
            $working += StringUtils::parseFormattedNumber($workplace->getPeopleWorking());
        }

        $available = min($free_people, $working);

        foreach ($data['workplaces'] as $workplace) {
            $num = StringUtils::parseFormattedNumber($workplace->getPeopleWorking());
            $work = $available > 0 ? min($num, $available) : 0;
            $available -= $num;
            $workplace->setPeopleWorking((int)$work);
        }

        return $data;
    }
}