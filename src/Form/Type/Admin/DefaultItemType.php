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
        private readonly TechnologyDataRepository $technologyDataRepository,
        private readonly BuildingDataRepository   $buildingDataRepository,
        private readonly DefenseDataRepository    $defenseDataRepository,
        private readonly ShipDataRepository       $shipDataRepository,
        private readonly MissileDataRepository    $missileDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => 'Objekt wählen',
            'choices' => [
                'Gebäude' => $this->buildingDataRepository->getBuildingNames(true),
                'Technologien' => $this->technologyDataRepository->getTechnologyNames(true),
                'Schiffe' => $this->shipDataRepository->getShipNames(true),
                'Verteidigung' => $this->defenseDataRepository->getDefenseNames(true),
                'Raketen' => $this->missileDataRepository->getMissileNames(true),
            ],
            'choice_label' => 'name',
            'choice_value' => 'id'
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
