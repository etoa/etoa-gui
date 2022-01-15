<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Form\Type\Core\SearchableChoiceType;
use EtoA\Missile\MissileDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultItemType extends AbstractType
{
    public function __construct(
        private TechnologyDataRepository $technologyDataRepository,
        private BuildingDataRepository $buildingDataRepository,
        private DefenseDataRepository $defenseDataRepository,
        private ShipDataRepository $shipDataRepository,
        private MissileDataRepository $missileDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => 'Objekt wählen',
            'choices' => [
                'Gebäude' => array_map(fn (int $id) => 'b:'.$id, array_flip($this->buildingDataRepository->getBuildingNames(true))),
                'Technologien' => array_map(fn (int $id) => 't:'.$id, array_flip($this->technologyDataRepository->getTechnologyNames(true))),
                'Schiffe' => array_map(fn (int $id) => 's:'.$id, array_flip($this->shipDataRepository->getShipNames(true))),
                'Verteidigung' => array_map(fn (int $id) => 'd:'.$id, array_flip($this->defenseDataRepository->getDefenseNames(true))),
                'Raketen' => array_map(fn (int $id) => 'm:'.$id, array_flip($this->missileDataRepository->getMissileNames(true))),
            ],
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
