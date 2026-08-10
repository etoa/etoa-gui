<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\DefaultItem\DefaultItemType as DefaultItemCategory;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Entity\Building;
use EtoA\Entity\Defense;
use EtoA\Entity\Missile;
use EtoA\Entity\Ship;
use EtoA\Entity\Technology;
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
                'Gebäude' => $this->buildChoices($this->buildingDataRepository->getBuildingNames(true), DefaultItemCategory::BUILDING),
                'Technologien' => $this->buildChoices($this->technologyDataRepository->getTechnologyNames(true), DefaultItemCategory::TECHNOLOGY),
                'Schiffe' => $this->buildChoices($this->shipDataRepository->getShipNames(true), DefaultItemCategory::SHIP),
                'Verteidigung' => $this->buildChoices($this->defenseDataRepository->getDefenseNames(true), DefaultItemCategory::DEFENSE),
                'Raketen' => $this->buildChoices($this->missileDataRepository->getMissileNames(true), DefaultItemCategory::MISSILE),
            ],
        ]);
    }

    /**
     * Object ids are only unique within a category, so the choice value is prefixed
     * with the category. This matches the format of DefaultItem::getObject().
     *
     * @param array<Building|Technology|Ship|Defense|Missile> $objects
     *
     * @return array<string, string>
     */
    private function buildChoices(array $objects, DefaultItemCategory $category): array
    {
        $choices = [];
        foreach ($objects as $object) {
            $label = (string) $object->getName();
            if (isset($choices[$label])) {
                $label .= ' (#' . $object->getId() . ')';
            }

            $choices[$label] = $category->value . ':' . $object->getId();
        }

        return $choices;
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
