<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingId;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Missile\MissileDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechTreeSelectionType extends AbstractType
{
    public function __construct(
        private readonly TechnologyDataRepository $technologyDataRepository,
        private readonly BuildingDataRepository   $buildingDataRepository,
        private readonly ShipDataRepository       $shipDataRepository,
        private readonly DefenseDataRepository    $defenseDataRepository,
        private readonly MissileDataRepository    $missileDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'placeholder' => 'Auswahl',
            'choices' => [
                'Gebäude' => $this->buildingDataRepository->findBy(['show'=>1]),
                'Technologien' => $this->technologyDataRepository->findBy(['show'=>1]),
                'Schiffe' => $this->shipDataRepository->findAll(),
                'Verteidigung' => $this->defenseDataRepository->findAll(),
                'Raketen' => $this->missileDataRepository->findAll(),
            ],
            'choice_label' => function (Mixed $type): string {
                return $type->getName();
            },
            'data' => $this->buildingDataRepository->find(BuildingId::BUILDING->value)
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
