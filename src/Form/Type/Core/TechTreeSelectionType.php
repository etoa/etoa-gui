<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Building\BuildingDataRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Missile\MissileDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechTreeSelectionType extends AbstractType
{
    public function __construct(
        private TechnologyDataRepository $technologyDataRepository,
        private BuildingDataRepository $buildingDataRepository,
        private ShipDataRepository $shipDataRepository,
        private DefenseDataRepository $defenseDataRepository,
        private MissileDataRepository $missileDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => 'Auswahl',
            'choices' => [
                'Gebäude' => array_map(fn (int $id) => 'b:'.$id, array_flip($this->buildingDataRepository->getBuildingNames())),
                'Technologien' => array_map(fn (int $id) => 't:'.$id, array_flip($this->technologyDataRepository->getTechnologyNames())),
                'Schiffe' => array_map(fn (int $id) => 's:'.$id, array_flip($this->shipDataRepository->getShipNames())),
                'Verteidigung' => array_map(fn (int $id) => 'd:'.$id, array_flip($this->defenseDataRepository->getDefenseNames())),
                'Raketen' => array_map(fn (int $id) => 'm:'.$id, array_flip($this->missileDataRepository->getMissileNames())),
            ],
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
