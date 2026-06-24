<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Doctrine\Common\Util\ClassUtils;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingId;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Entity\Building;
use EtoA\Entity\Defense;
use EtoA\Entity\Missile;
use EtoA\Entity\Ship;
use EtoA\Entity\Technology;
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
            'placeholder' => false,
            'choices' => [
                'Gebäude' => $this->buildingDataRepository->findBy(['show'=>1],['name'=>'ASC']),
                'Technologien' => $this->technologyDataRepository->findBy(['show'=>1],['name'=>'ASC']),
                'Schiffe' => $this->shipDataRepository->findBy(['show'=>1],['name'=>'ASC']),
                'Verteidigung' => $this->defenseDataRepository->findBy(['show'=>1],['name'=>'ASC']),
                'Raketen' => $this->missileDataRepository->findBy(['show'=>1],['name'=>'ASC']),
            ],
            'choice_label' => function (Mixed $type): string {
                return $type->getName();
            },
            'choice_value' => function (Mixed $type): string {
                return match(ClassUtils::getClass($type)) {
                    Building::class => 'b-'.$type->getId(),
                    Technology::class => 't-'.$type->getId(),
                    Ship::class => 's-'.$type->getId(),
                    Defense::class => 'd-'.$type->getId(),
                    Missile::class => 'm-'.$type->getId(),
                    default => '',
                };
            },
            'data' => $this->buildingDataRepository->find(BuildingId::BUILDING->value)
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
